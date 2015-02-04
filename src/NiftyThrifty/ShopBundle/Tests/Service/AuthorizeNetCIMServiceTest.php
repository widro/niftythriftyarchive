<?php

namespace NiftyThrifty\ShopBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use NiftyThrifty\ShopBundle\Service\AuthorizeNetCIMService;
use NiftyThrifty\ShopBundle\Entity\User;
use NiftyThrifty\ShopBundle\Entity\Order;

/**
 * Tests for the validator methods.
 */
class AuthorizeNetCIMServiceTest extends WebTestCase
{
    public $cimService;
    public $cimService2;
    public $testUser;
    public $testUser2;
    public $cardService;
    public $cardInfo;
    public $testOrder;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->cimService   = $kernel->getContainer()->get('authorize_cim');
        $this->cimService->setTestMode();
        $this->cimService2  = $kernel->getContainer()->get('authorize_cim');
        $this->cimService2->setTestMode();
        $this->cardService  = $kernel->getContainer()->get('credit_card_validator');
        $this->testUser = new User();
        $this->testUser->setUserFirstName('Service')
                       ->setUserLastName('Test')
                       ->setUserEmail('servicetest@niftythrifty.com');
        $this->testUser2 = new User();
        $this->testUser2->setUserFirstName('Service')
                        ->setUserLastName('Test')
                        ->setUserEmail('servicetest@niftythrifty.com');
        $this->cardInfo = array('cardName'     => 'Test Card Guy',
                                'cardNumber'   => '4111111111111111',
                                'expireMonth'  => '01',
                                'expireYear'   => '2020',
                                'cvv'          => '555');
        $this->testOrder = new Order();
        $this->testOrder->setOrderBillingAddressFirstName('Billing')
                        ->setOrderBillingAddressLastName('Address')
                        ->setOrderBillingAddressStreet('123 Somewhere Street')
                        ->setOrderBillingAddressCity('Derry')
                        ->setOrderBillingAddressState('ME')
                        ->setOrderBillingAddressZipcode('03453')
                        ->setOrderBillingAddressCountry('USA')
                        ->setOrderShippingAddressFirstName('Shipping')
                        ->setOrderShippingAddressLastName('Address')
                        ->setOrderShippingAddressStreet('456 Elsewhere Street')
                        ->setOrderShippingAddressCity('Castle Rock')
                        ->setOrderShippingAddressState('NH')
                        ->setOrderShippingAddressZipcode('12343')
                        ->setOrderShippingAddressCountry('USA');
    }

    public function testConstants()
    {
        $this->assertEquals(AuthorizeNetCIMService::AUTHORIZE_DUPLICATE_ERROR_CODE,'E00039');
        $this->assertEquals(AuthorizeNetCIMService::AUTHORIZE_CUSTOMER_TYPE,       'individual');
        $this->assertEquals(AuthorizeNetCIMService::TRANSACTION_TYPE_AUTH_CAPTURE, 'AuthCapture');
    }

    /**
     * Test that the constructor sets stuff
     *
     * @covers AuthorizeNetCIMService:getCustomer
     * @covers AuthorizeNetCIMService:getPaymentProfile
     * @covers AuthorizeNetCIMProfile:__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\AuthorizeNetCustomer',        $this->cimService->getCustomer());
        $this->assertInstanceOf('\AuthorizeNetPaymentProfile',  $this->cimService->getPaymentProfile());
    }

    /**
     * Set customer if a customerid is not defined.
     */
    public function testSetCustomerNewCustomer()
    {
        $cimCustomer = $this->cimService->setCustomer($this->testUser);

        $this->assertInstanceOf('\AuthorizeNetCustomer', $cimCustomer);
        $this->assertEquals($cimCustomer->merchantCustomerId,   null);
        $this->assertEquals($cimCustomer->description,          'Service Test');
        $this->assertEquals($cimCustomer->email,                'servicetest@niftythrifty.com');
        $this->assertNotEmpty($cimCustomer->customerProfileId);
    }

    /**
     * Set customer if the authorize id is defined
     */
    public function testSetCustomerExistingCustomer()
    {
        $cimCustomer = $this->cimService->setCustomer($this->testUser);
        $authorizeId = $cimCustomer->customerProfileId;
        $this->assertNotEmpty($authorizeId);
        $this->testUser->setAuthorizeNetCustomerId($authorizeId);

        $cimCustomer2 = $this->cimService2->setCustomer($this->testUser);
        $this->assertEquals($cimCustomer, $cimCustomer2);
    }

    /**
     * Set customer with duplicae info should succeed and return the correct id
     *
     * @covers AuthorizeNetCIMService:clearCustomer
     */
    public function testSetCustomerDuplicateCustomer()
    {
        $cimCustomer = $this->cimService->setCustomer($this->testUser);
        $authorizeId = $cimCustomer->customerProfileId;
        $this->assertNotEmpty($authorizeId);
        $this->cimService->clearCustomer();
        $this->assertEmpty($this->cimService->getCustomer()->customerProfileId);

        $cimCustomer2 = $this->cimService->setCustomer($this->testUser2);
        $this->assertEquals($cimCustomer, $cimCustomer2);
    }

    /**
     * Set customer with an error.
     *
     * @expectedException AuthorizeNetException
     */
    public function testSetCustomerError()
    {
        $user = new User();
        $this->cimService->setCustomer($user);
    }

    /**
     * Test regenerating a user.
     */
    public function testRegenerateUser()
    {
        $cimCustomer = $this->cimService->setCustomer($this->testUser);
        $authorizeId = $cimCustomer->customerProfileId;
        $this->assertNotEmpty($authorizeId);
        $this->testUser->setAuthorizeNetCustomerId($authorizeId);

        $cimCustomer2 = $this->cimService2->regenerateUser($this->testUser);
        $this->assertEquals($cimCustomer->email,                $cimCustomer2->email);
        $this->assertEquals($cimCustomer->merchantCustomerId,   $cimCustomer2->merchantCustomerId);
        $this->assertEquals($cimCustomer->description,          $cimCustomer2->description);
        $this->assertNotEquals($cimCustomer->customerProfileId, $cimCustomer2->customerProfileId);
    }

    /**
     * Setting setting a payment model.
     */
    public function testSetCreditCard()
    {
        $this->cardService->set($this->cardInfo);
        $cimCard = $this->cimService->setCreditCard($this->cardService);

        $this->assertInstanceOf('\AuthorizeNetCreditCard', $cimCard);
        $this->assertEquals($cimCard->cardNumber, $this->cardInfo['cardNumber']);
        $this->assertEquals($cimCard->expirationDate, '2020-01');
        $this->assertEquals($cimCard->cardCode, $this->cardInfo['cvv']);
    }

    /**
     * Test setting a payment profile by a card service data.  We do a lot of stuff in here
     * because the testing engine is bad at dealing with duplicate profiles.
     *
     * @covers AuthorizeNetCIMService:setCreditCard
     * @covers AuthorizeNetCIMService:getShippingAddress
     * @covers AuthorizeNetCIMService:clearPaymentProfile
     * @covers AuthorizeNetCIMService:setPaymentProfileById
     * @covers AuthorizeNetCIMService:createTransaction
     * @covers AuthorizeNetCIMService:executeTransaction
     */
    public function testSetPaymentProfileByCardService()
    {
        $cimCustomer = $this->cimService->setCustomer($this->testUser);
        $authorizeId = $cimCustomer->customerProfileId;
        $this->assertNotEmpty($authorizeId);
        $this->cardService->set($this->cardInfo);

        $cimPaymentProfile  = $this->cimService->setPaymentProfileByCardService($this->cardService, $this->testOrder);
        $cimShippingAddress = $this->cimService->getShippingAddress();

        $this->assertEquals($cimPaymentProfile->customerType,       'individual');
        $this->assertEquals($cimPaymentProfile->billTo->firstName,  'Billing');
        $this->assertEquals($cimPaymentProfile->billTo->lastName,   'Address');
        $this->assertEquals($cimPaymentProfile->billTo->address,    '123 Somewhere Street');
        $this->assertEquals($cimPaymentProfile->billTo->city,       'Derry');
        $this->assertEquals($cimPaymentProfile->billTo->state,      'ME');
        $this->assertEquals($cimPaymentProfile->billTo->zip,        '03453');
        $this->assertEquals($cimPaymentProfile->billTo->country,    'USA');
        $this->assertEquals($cimPaymentProfile->payment->creditCard->cardNumber,    '4111111111111111');
        $this->assertEquals($cimPaymentProfile->payment->creditCard->expirationDate,'2020-01');
        $this->assertEquals($cimPaymentProfile->payment->creditCard->cardCode,      '555');
        $this->assertNotEmpty($cimPaymentProfile->customerPaymentProfileId);

        $this->assertEquals($cimShippingAddress->firstName, 'Shipping');
        $this->assertEquals($cimShippingAddress->lastName,  'Address');
        $this->assertEquals($cimShippingAddress->address,   '456 Elsewhere Street');
        $this->assertEquals($cimShippingAddress->city,      'Castle Rock');
        $this->assertEquals($cimShippingAddress->state,     'NH');
        $this->assertEquals($cimShippingAddress->zip,       '12343');
        $this->assertEquals($cimShippingAddress->country,   'USA');

        // Test Fetch By Id
        $profileId = $cimPaymentProfile->customerPaymentProfileId;
        $this->cimService->clearPaymentProfile();
        $newProfile = $this->cimService->setPaymentProfileById($profileId);
        $this->assertEquals($newProfile->customerPaymentProfileId, $cimPaymentProfile->customerPaymentProfileId);

        // Test create/execute transaction
        $transactionValue = rand(1,1000);
        $transaction = $this->cimService->createTransaction($transactionValue);
        $this->assertInstanceOf('\AuthorizeNetTransaction',         $transaction);
        $this->assertEquals($transaction->customerProfileId,        $authorizeId);
        $this->assertEquals($transaction->customerPaymentProfileId, $profileId);
        $this->assertEquals($transaction->amount,                   $transactionValue);
        $this->assertTrue($this->cimService->executeTransaction());
    }

    /**
     * Not setting a customer will cause an exception.
     *
     * @expectedException AuthorizeNetException
     */
    public function testSetPaymentProfileNoCustomer()
    {
        $this->cardService->set($this->cardInfo);
        $this->cimService->setPaymentProfileByCardService($this->cardService, $this->testOrder);
    }

    /**
     * createAddress returns an AuthorizeNetAddress object
     */
    public function testCreateAddress()
    {
        $address = array('firstName'    => 'Create',
                         'lastName'     => 'Address',
                         'company'      => 'Nifty Thrifty',
                         'address'      => '123 Somewhere Place',
                         'city'         => 'Derry',
                         'state'        => 'ME',
                         'zip'          => '03234');
        $cimAddress = $this->cimService->createAddress($address);

        $this->assertInstanceOf('\AuthorizeNetAddress', $cimAddress);
        foreach ($address as $key => $value) {
            $this->assertEquals($address[$key], $cimAddress->$key);
        }
    }

    /**
     * Test executing a transaction with an exception
     *
     * @expectedException AuthorizeNetException
     */
    public function testExecuteTransactionError()
    {
        $transaction = $this->cimService->createTransaction(100);
        $this->cimService->executeTransaction();
    }
}
