<?php

namespace NiftyThrifty\ShopBundle\Service;

// I don't know how to do this with a use, so just do a hard require. 
require_once(__DIR__.'/../../../../vendor/AuthorizeDotNetBundle/AuthorizeDotNetBundle/AuthorizeNet.php');

/**
 * This service class wraps the Authorize.Net CIM class.  The CIM class tokenizes user's credit card
 * information so nothing is stored on our servers.  This class handles some of the idiosyncracies of
 * the Authorize class and cleans up some of the odder parts so they don't have to be handled in
 * the controller.
 *
 * Accessed in ShopBundle via 'authorize_cim'
 *
 * Author: Tom Phillips
 * For: Nifty Thrifty, INC
 * Throws: AuthorizeNetException
 */
class AuthorizeNetCIMService
{
    /**
     * This is an AuthorizeNetCIM object, set at construction.
     */
    private $_cim;
    private $_cimCustomer;
    private $_cimPaymentProfile;
    private $_cimPayment;
    private $_cimCreditCard;
    private $_cimShippingAddress;
    private $_cimTransaction;
    
    /**
     * this is used if we're adding transactions for unit tests.  This should never be turned
     * on except in unit tests.
     */
    private $_testMode;

    /**
     * This is the error code returned by the CIM API when you attempt to create a duplicate.  Usually,
     * we will try to recover from this.
     */
    const AUTHORIZE_DUPLICATE_ERROR_CODE = 'E00039';
    const AUTHORIZE_CUSTOMER_TYPE        = 'individual';
    const TRANSACTION_TYPE_AUTH_CAPTURE  = 'AuthCapture';

    /**
     * This constructs all the objects required for this and also initializes the cim API object.
     *
     * @param   string  $apiLoginId     This is Authorize.net's API login for the merchant.
     * @param   string  $transactionKey This is Authorize.net's Transaction key for the merchant.
     * @return void
     */
    public function __construct($apiLoginId, $transactionKey)
    {
        $this->_cim                 = new \AuthorizeNetCIM($apiLoginId, $transactionKey);
        $this->_cim->setSandbox(false);
        $this->_cimCustomer         = new \AuthorizeNetCustomer;
        $this->_cimPaymentProfile   = new \AuthorizeNetPaymentProfile;
        $this->_cimCreditCard       = new \AuthorizeNetCreditCard;
        $this->_cimPayment          = new \AuthorizeNetPayment;
        $this->_cimShippingAddress  = new \AuthorizeNetAddress;
        $this->_cimTransaction      = new \AuthorizeNetTransaction;
        $this->_testMode            = false;
    }
    
    public function setCustomerById($customerId)
    {
        $response = $this->_cim->getCustomerProfile($customerId);
        if ($response->isError()) throw new \AuthorizeNetException($response->getErrorMessage());
        $this->_cimCustomer->customerProfileId  = $response->getCustomerProfileId();
        $this->_cimCustomer->merchantCustomerId = $response->xml->merchantCustomerId;
        $this->_cimCustomer->description        = $response->xml->description;
        $this->_cimCustomer->email              = $response->xml->email;
        $this->_cimCustomer->paymentProfiles    = $response->xml->profile->paymentProfiles;
        $this->_cimCustomer->shipToList         = $response->xml->profile->shipToList; 
    }

    /**
     * Set the customer object.  This will try to initialize from the customer id if it's populated, or
     * try to construct it.  It will also swallow Authorize.NET's duplicate creation errors and return
     * the duplicate.
     *
     * @param   NiftyThriftyShopBundle:User     A user object, usually Controller::getUser
     * @return  AuthorizeNetCustomer
     */
    public function setCustomer($user)
    {
        $this->_cimCustomer->merchantCustomerId = $user->getUserId();
        $this->_cimCustomer->description        = $user->getUserFirstName() 
                                                    . ' ' 
                                                    . $user->getUserLastName();
        $this->_cimCustomer->email              = $user->getUserEmail();

        /**
         * If the user object has a customerid, verify that customer exists and then set the profileId
         */
        if ($user->getAuthorizeNetCustomerId()) {
            $response = $this->_cim->getCustomerProfile($user->getAuthorizeNetCustomerId());
            if ($response->isError()) throw new \AuthorizeNetException($response->getErrorMessage());
            $this->_cimCustomer->customerProfileId  = $response->getCustomerProfileId();
            $this->_cimCustomer->paymentProfiles    = $response->xml->profile->paymentProfiles;
            $this->_cimCustomer->shipToList         = $response->xml->profile->shipToList;

        /**
         * Otherwise, try to create the profile.  If the profile is a duplicate, Authorize will send back
         * the duplicate ID even though it returns an error.
         */
        } else {
            $response = $this->_cim->createCustomerProfile($this->_cimCustomer);
            if ($response->isOk()) {
                $this->_cimCustomer->customerProfileId = $response->getCustomerProfileId();
            } else {
                if ($response->getMessageCode() == self::AUTHORIZE_DUPLICATE_ERROR_CODE) {
                    preg_match_all('/\d+/', $response->getMessageText(), $ids);
                    $this->_cimCustomer->customerProfileId = $ids[0][0];
                } else {
                    throw new \AuthorizeNetException($response->getMessageText());
                }
            }
        }
        return $this->_cimCustomer;
    }

    /**
     * We can get in to a state where the user has a saved payment profile with authorize that we can
     * no longer access.  Since Authorize doesn't return the ID when you attempt to register a duplicate
     * it's possible that we will never be able to use the card data again.  So to get around it, we need
     * to wipe out the user, re-register them with authorize, and re-save the card.  This is not ideal, but
     * until Authorize fixes their API to return the duplicateId when registering a duplicate payment profile
     * it's the only option
     *
     * @param   User    The user we are regenerating
     * @return  AuthorizeCIMCustomer
     */
    public function regenerateUser($user)
    {
        $response = $this->_cim->deleteCustomerProfile($this->_cimCustomer->customerProfileId);
        $this->_cimCustomer = new \AuthorizeNetCustomer;

        if ($response->isOk()) {
            $user->setAuthorizeNetCustomerId(null);
            $this->setCustomer($user);

        // If this doesn't work, we have to bail.
        } else {
            throw new \AuthorizeNetException($response->getErrorMessage());
        }

        return $this->_cimCustomer;
    }
    
    /**
     * Given a saved profile id, get the profile from Authorize and set the internal profile
     * object in this model.
     *
     * @param   NiftyThriftyShopBundle:UserPaymentProfile       The payment profile that contains the ID
     * @return  AuthorizeNetPaymentProfile
     */
    public function setPaymentProfileById($profileId)
    {
        $response = $this->_cim->getCustomerPaymentProfile($this->_cimCustomer->customerProfileId, $profileId);
        if ($response->isError()) throw new \AuthorizeNetException($response->getMessageText());
        $this->_cimPaymentProfile->customerPaymentProfileId = $response->getPaymentProfileId();
        $this->_cimPaymentProfile->billTo                   = $response->xml->paymentProfile->billTo;
        $this->_cimPaymentProfile->payment                  = $response->xml->paymentProfile->payment;
        
        return $this->_cimPaymentProfile;
    }
    
    /**
     * Given a credit card number via a CardService object, create a new payment profile using
     * that card.  This will also try to recover from a duplicate profile error.  Setting a profile
     * this way, presumes we are creating a new profile.
     *
     * @param   NiftyThriftyShopBundle:CreditCardServie     The card we're creating
     * @param   NiftyThriftyShopBundle:Order                We will get the addresses out of this.
     * @return  AuthorizeNetPaymentProfile
     */
    public function setPaymentProfileByCardService($card, $order)
    {
        $this->setCreditCard($card);
        $billingAddress = $this->createAddress(array('firstName'=> $order->getOrderBillingAddressFirstName(),
                                                     'lastName' => $order->getOrderBillingAddressLastName(),
                                                     'address'  => $order->getOrderBillingAddressStreet(),
                                                     'city'     => $order->getOrderBillingAddressCity(),
                                                     'state'    => $order->getOrderBillingAddressState(),
                                                     'zip'      => $order->getOrderBillingAddressZipcode(),
                                                     'country'  => $order->getOrderBillingAddressCountry()));
        $this->_cimPaymentProfile->billTo       = $billingAddress;
        $this->_cimPaymentProfile->payment      = $this->_cimPayment;
        $this->_cimPaymentProfile->customerType = self::AUTHORIZE_CUSTOMER_TYPE;

        $response = $this->_cim->createCustomerPaymentProfile($this->_cimCustomer->customerProfileId,
                                                              $this->_cimPaymentProfile);

        /**
         * If the error is a duplicate error, try to recover from it.
         */
        if ($response->isOk()) {
            $this->_cimPaymentProfile->customerPaymentProfileId = $response->getPaymentProfileId();
        } else {
            if ($response->getMessageCode() == self::AUTHORIZE_DUPLICATE_ERROR_CODE) {
                $found = false;
                foreach ($this->_cimCustomer->paymentProfiles as $profile) {
                    if ($card->isMatch($profile->payment->creditCard->cardNumber)) {
                        $this->_cimPaymentProfile->customerPaymentProfileId = $profile->customerPaymentProfileId;
                        $found = true;
                    }
                }
                if (!$found) throw new \AuthorizeNetException('There was an error processing your saved profile.');
            } else {
                throw new \AuthorizeNetException($response->getMessageText());
            }
        }
        
        // Shipping address
        $shippingAddress = $this->createAddress(array('firstName'=> $order->getOrderShippingAddressFirstName(),
                                                      'lastName' => $order->getOrderShippingAddressLastName(),
                                                      'address'  => $order->getOrderShippingAddressStreet(),
                                                      'city'     => $order->getOrderShippingAddressCity(),
                                                      'state'    => $order->getOrderShippingAddressState(),
                                                      'zip'      => $order->getOrderShippingAddressZipcode(),
                                                      'country'  => $order->getOrderShippingAddressCountry()));
        $response = $this->_cim->createCustomerShippingAddress($this->_cimCustomer->customerProfileId,
                                                               $shippingAddress);

        if ($response->isOk()) {
            $this->_cimShippingAddress = $shippingAddress;
            $this->_cimShippingAddress->customerAddressId = $response->getCustomerAddressId();
        } else {
            if ($response->getMessageCode() != self::AUTHORIZE_DUPLICATE_ERROR_CODE) {
                throw new \AuthorizeNetException($response->getMessageText());
            }
        }
        return $this->_cimPaymentProfile;
    }
    
    /**
     * Set the payment profile by the user's payment profile.  This only works right now if the user has one payment
     * profile.
     *
     * !!!!!!!!!!!!!!!!Avoid this outside unit testing!!!!!!!!!!!!!!!!!!!!!!!
     */
    public function setPaymentProfileByCustomerProfile()
    {
        $profile = $this->_cimCustomer->paymentProfiles[0];
        $this->_cimPaymentProfile = $this->_cimCustomer->paymentProfiles[0];
        return $this->_cimPaymentProfile;
    }
    
    /**
     * Given a card stored in a CardService object, set the credit card for a payment profile
     *
     * @param   NiftyThriftyShopBundle:CreditCardService
     * @return  AuthorizeNetCreditCard;
     */
    public function setCreditCard($card)
    {
        $this->_cimCreditCard->cardNumber       = $card->cardNumber;
        $this->_cimCreditCard->expirationDate   = $card->getFormattedDate();
        $this->_cimCreditCard->cardCode         = $card->cvv;
        $this->_cimPayment->creditCard          = $this->_cimCreditCard;
        
        return $this->_cimCreditCard;
    }
    
    /**
     * Given an array of items, return an AuthorizeNet Address object.
     *
     * @param   Array       The array must contain all fields of an address object.
     *  array('firstName', 'lastName', street, city, state, zip
     * @return  AuthorizeNetAddress;
     */
    public function createAddress($opts=array())
    {
        $address = new \AuthorizeNetAddress;
        foreach ($opts as $key => $value) {
            $address->$key = $value;
        }
        return $address;
    }
    
    /**
     * Create a transaction.  This only deals with customer id, profile id, and amount.  We
     * don't set any of the other stuff yet until we have to.
     *
     * @param   float       An amount for the transaction
     * @return  AuthorizeNetTransaction
     */
    public function createTransaction($amount)
    {
        $this->_cimTransaction->customerProfileId         = $this->_cimCustomer->customerProfileId;
        $this->_cimTransaction->customerPaymentProfileId  = $this->_cimPaymentProfile->customerPaymentProfileId;
        $this->_cimTransaction->amount                    = $amount;
        
        return $this->_cimTransaction;
    }
    
    /**
     * Executes a transaction object.  We only execute AUTH_CAPTURE transactions right now.
     *
     * @param void
     * @return  bool
     */
    public function executeTransaction()
    {
        if ($this->_testMode) {
            $response = $this->_cim->createCustomerProfileTransaction(self::TRANSACTION_TYPE_AUTH_CAPTURE, 
                                                                      $this->_cimTransaction,
                                                                      'x_duplicate_window=0');
        } else {
            $response = $this->_cim->createCustomerProfileTransaction(self::TRANSACTION_TYPE_AUTH_CAPTURE, 
                                                                      $this->_cimTransaction);
        }
        if ($response->isError()) {
            throw new \AuthorizeNetException($response->getMessageText());
        }
        
        return true;
    }
    
    /**
     * @return AuthorizeNetCustomer
     */
    public function getCustomer()
    {
        return $this->_cimCustomer;
    }

    public function clearCustomer()
    {
        $this->_cimCustomer = new \AuthorizeNetCustomer;
    }

    public function clearPaymentProfile()
    {
        $this->_cimPaymentProfile = new \AuthorizeNetPaymentProfile;
    }

    public function clearShippingAddress()
    {
        $this->_cimShippingAddress = new \AuthorizeNetAddress;
    }
    
    /**
     * @return AuthorizeNetPaymentProfile
     */
    public function getPaymentProfile()
    {
        return $this->_cimPaymentProfile;
    }

    /**
     * @return AuthorizeNetAddress
     */
    public function getShippingAddress()
    {
        return $this->_cimShippingAddress;
    }
    
    public function setTestMode()
    {
        $this->_cim->setTestMode();
        $this->_cim->setSandbox(true);
        $this->_testMode = true;
    }
}
