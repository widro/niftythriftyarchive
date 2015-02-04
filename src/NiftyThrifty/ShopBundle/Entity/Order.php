<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use NiftyThrifty\ShopBundle\Entity\Coupon;
use NiftyThrifty\ShopBundle\Entity\State;

/**
 * NiftyThrifty\ShopBundle\Entity\Order
 *
 * @ORM\Table(name="order")
 * @ORM\Entity
 */
class Order
{
    /**
     * @var integer $orderId
     *
     * @ORM\Column(name="order_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderId;

    /**
     * @var integer $basketId
     *
     * @ORM\Column(name="basket_id", type="bigint", nullable=false)
     */
    private $basketId;

    /**
     * @var string $orderStatus
     *
     * @ORM\Column(name="order_status", type="string", nullable=false)
     */
    private $orderStatus;

    /**
     * @var \DateTime $orderDateCreation
     *
     * @ORM\Column(name="order_date_creation", type="datetime", nullable=false)
     */
    private $orderDateCreation;

    /**
     * @var \DateTime $orderDateEnd
     *
     * @ORM\Column(name="order_date_end", type="datetime", nullable=false)
     */
    private $orderDateEnd;

    /**
     * @var string $orderUserFirstName
     *
     * @ORM\Column(name="order_user_first_name", type="string", length=100, nullable=false)
     */
    private $orderUserFirstName;

    /**
     * @var string $orderUserLastName
     *
     * @ORM\Column(name="order_user_last_name", type="string", length=100, nullable=false)
     */
    private $orderUserLastName;

    /**
     * @var string $orderUserEmail
     *
     * @ORM\Column(name="order_user_email", type="string", length=100, nullable=false)
     */
    private $orderUserEmail;

    /**
     * @var float $orderAmount
     *
     * @ORM\Column(name="order_amount", type="float", nullable=false)
     */
    private $orderAmount;

    /**
     * @var float $orderAmountCoupon
     *
     * @ORM\Column(name="order_amount_coupon", type="float", nullable=false)
     */
    private $orderAmountCoupon;

    /**
     * @var float $orderAmountVat
     *
     * @ORM\Column(name="order_amount_vat", type="float", nullable=false)
     */
    private $orderAmountVat;

    /**
     * @var float $orderAmountShipping
     *
     * @ORM\Column(name="order_amount_shipping", type="float", nullable=false)
     */
    private $orderAmountShipping;

    /**
     * @var float $orderAmountCredits
     *
     * @ORM\Column(name="order_amount_credits", type="float", nullable=false)
     */
    private $orderAmountCredits;

    /**
     * @var float $orderAmountTotal
     *
     * @ORM\Column(name="order_amount_total", type="float", nullable=false)
     */
    private $orderAmountTotal;

    /**
     * @var string $orderProducts
     *
     * @ORM\Column(name="order_products", type="text", nullable=false)
     */
    private $orderProducts;

    /**
     * @var string $orderShippingMethod
     *
     * @ORM\Column(name="order_shipping_method", type="string", nullable=true)
     */
    private $orderShippingMethod;

    /**
     * @var string $orderShippingAddressFirstName
     *
     * @ORM\Column(name="order_shipping_address_first_name", type="string", length=64, nullable=false)
     */
    private $orderShippingAddressFirstName;

    /**
     * @var string $orderShippingAddressLastName
     *
     * @ORM\Column(name="order_shipping_address_last_name", type="string", length=64, nullable=false)
     */
    private $orderShippingAddressLastName;

    /**
     * @var string $orderShippingAddressStreet
     *
     * @ORM\Column(name="order_shipping_address_street", type="string", length=255, nullable=false)
     */
    private $orderShippingAddressStreet;

    /**
     * @var string $orderShippingAddressCity
     *
     * @ORM\Column(name="order_shipping_address_city", type="string", length=64, nullable=false)
     */
    private $orderShippingAddressCity;

    /**
     * @var string $orderShippingAddressState
     *
     * @ORM\Column(name="order_shipping_address_state", type="string", length=64, nullable=false)
     */
    private $orderShippingAddressState;

    /**
     * @var string $orderShippingAddressZipcode
     *
     * @ORM\Column(name="order_shipping_address_zipcode", type="string", length=20, nullable=false)
     */
    private $orderShippingAddressZipcode;

    /**
     * @var string $orderShippingAddressCountry
     *
     * @ORM\Column(name="order_shipping_address_country", type="string", length=64, nullable=false)
     */
    private $orderShippingAddressCountry;

    /**
     * @var string $orderBillingAddressFirstName
     *
     * @ORM\Column(name="order_billing_address_first_name", type="string", length=64, nullable=false)
     */
    private $orderBillingAddressFirstName;

    /**
     * @var string $orderBillingAddressLastName
     *
     * @ORM\Column(name="order_billing_address_last_name", type="string", length=64, nullable=false)
     */
    private $orderBillingAddressLastName;

    /**
     * @var string $orderBillingAddressStreet
     *
     * @ORM\Column(name="order_billing_address_street", type="string", length=255, nullable=false)
     */
    private $orderBillingAddressStreet;

    /**
     * @var string $orderBillingAddressCity
     *
     * @ORM\Column(name="order_billing_address_city", type="string", length=64, nullable=false)
     */
    private $orderBillingAddressCity;

    /**
     * @var string $orderBillingAddressState
     *
     * @ORM\Column(name="order_billing_address_state", type="string", length=64, nullable=false)
     */
    private $orderBillingAddressState;

    /**
     * @var string $orderBillingAddressZipcode
     *
     * @ORM\Column(name="order_billing_address_zipcode", type="string", length=20, nullable=false)
     */
    private $orderBillingAddressZipcode;

    /**
     * @var string $orderBillingAddressCountry
     *
     * @ORM\Column(name="order_billing_address_country", type="string", length=64, nullable=false)
     */
    private $orderBillingAddressCountry;

    /**
     * @var string $orderUserIpAddress
     *
     * @ORM\Column(name="order_user_ip_address", type="string", length=255, nullable=false)
     */
    private $orderUserIpAddress;

    /**
     * @var integer $couponId
     *
     * @ORM\Column(name="coupon_id", type="bigint", nullable=true)
     */
    private $couponId;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\Coupon
     */
    private $coupon;

    const STATUS_PAID       = 'paid';
    const STATUS_UNPAID     = 'unpaid';
    const STATUS_EXPIRED    = 'expired';

    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // May have to do a basketId validation at some point.
        $metadata->addPropertyConstraint('basketId', new Assert\NotBlank(array('message' => 'Basket can not be blank.')));
        $metadata->addConstraint(new Assert\Callback(array('methods' => array('validateCouponId'))));

        // Check order status and method is one of the valid entries
        $metadata->addPropertyConstraint('orderStatus', new Assert\NotBlank(array('message' => 'Order status can not be blank.')));
        $metadata->addPropertyConstraint('orderStatus', new Assert\Choice(
                                                                array(
                                                                    'choices' => array(self::STATUS_PAID,
                                                                                       self::STATUS_UNPAID,
                                                                                       self::STATUS_EXPIRED),
                                                                    'message' => 'Order status string is not valid.')));
        $metadata->addPropertyConstraint('orderShippingMethod', new Assert\NotBlank(array('message' => 'Shipping method can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingMethod', new Assert\Choice(
                                                                        array(
                                                                            'choices' => array('classic', 'express'),
                                                                            'message' => 'Shipping method is not valid.')));

        // These fields are not dependent on user input and should have been validated elsewhere.
        $metadata->addPropertyConstraint('orderDateCreation',   new Assert\NotBlank(array('message' => 'Date creation can not be blank.')));
        $metadata->addPropertyConstraint('orderDateCreation',   new Assert\DateTime(array('message' => 'Date creation is not a valid date.')));
        $metadata->addPropertyConstraint('orderDateEnd',        new Assert\NotBlank(array('message' => 'Date end can not be blank.')));
        $metadata->addPropertyConstraint('orderDateEnd',        new Assert\DateTime(array('message' => 'Date end is not a valid date.')));
        $metadata->addPropertyConstraint('orderUserFirstName',  new Assert\NotBlank(array('message' => 'Order first name can not be blank.')));
        $metadata->addPropertyConstraint('orderUserLastName',   new Assert\NotBlank(array('message' => 'Order last name can not be blank.')));
        $metadata->addPropertyConstraint('orderUserEmail',      new Assert\NotBlank(array('message' => 'Order e-mail can not be blank.')));
        $metadata->addPropertyConstraint('orderUserIpAddress',  new Assert\NotBlank(array('message' => 'User IP address must be defined.')));
        $metadata->addPropertyConstraint('orderUserIpAddress',  new Assert\Ip(array('version' => 'all',
                                                                                    'message' => 'User IP address is invalid.')));
        $metadata->addPropertyConstraint('orderProducts',       new Assert\NotBlank(array('message' => 'Order product list can not be blank.')));

        // Value fields are automatically generated, but should be set to something, even if it's zero.
        $metadata->addPropertyConstraint('orderAmount',         new Assert\NotBlank(array('message' => 'Order amount can not be blank.')));
        $metadata->addPropertyConstraint('orderAmount',         new Assert\Type(array('type'    => 'numeric',
                                                                                      'message' => 'Order amount must be a number.')));
        $metadata->addPropertyConstraint('orderAmount',         new Assert\GreaterThanOrEqual(array('value'   => 0,
                                                                                                    'message' => 'Order amount can not be negative.')));

        $metadata->addPropertyConstraint('orderAmountCoupon',   new Assert\NotBlank(array('message' => 'Order coupon amount can not be blank.')));
        $metadata->addPropertyConstraint('orderAmountCoupon',   new Assert\Type(array('type'    => 'numeric',
                                                                                      'message' => 'Order amount coupon must be a number.')));
        $metadata->addPropertyConstraint('orderAmountCoupon',   new Assert\GreaterThanOrEqual(array('value'   => 0,
                                                                                                    'message' => 'Order coupon amount can not be negative.')));

        $metadata->addPropertyConstraint('orderAmountVat',      new Assert\NotBlank(array('message' => 'Order tax amount can not be blank.')));
        $metadata->addPropertyConstraint('orderAmountVat',      new Assert\Type(array('type'    => 'numeric',
                                                                                      'message' => 'Order tax amount must be a number.')));
        $metadata->addPropertyConstraint('orderAmountVat',      new Assert\GreaterThanOrEqual(array('value'   => 0,
                                                                                                    'message' => 'Order tax amount can not be negative.')));

        $metadata->addPropertyConstraint('orderAmountShipping', new Assert\NotBlank(array('message' => 'Order shipping amount can not be blank.')));
        $metadata->addPropertyConstraint('orderAmountShipping', new Assert\Type(array('type'    => 'numeric',
                                                                                      'message' => 'Order shipping amount must be a number.')));
        $metadata->addPropertyConstraint('orderAmountShipping', new Assert\GreaterThanOrEqual(array('value'   => 0,
                                                                                                    'message' => 'Order shipping amount can not be negative.')));

        $metadata->addPropertyConstraint('orderAmountCredits',  new Assert\NotBlank(array('message' => 'Order credit amount can not be blank.')));
        $metadata->addPropertyConstraint('orderAmountCredits',  new Assert\Type(array('type'    => 'numeric',
                                                                                      'message' => 'Order credit amount must be a number.')));
        $metadata->addPropertyConstraint('orderAmountCredits',  new Assert\GreaterThanOrEqual(array('value'   => 0,
                                                                                                    'message' => 'Order credit amount can not be negative.')));

        $metadata->addPropertyConstraint('orderAmountTotal',    new Assert\NotBlank(array('message' => 'Order total amount can not be blank.')));
        $metadata->addPropertyConstraint('orderAmountTotal',    new Assert\Type(array('type'    => 'numeric',
                                                                                      'message' => 'Order total amount must be a number.')));
        $metadata->addPropertyConstraint('orderAmountTotal',    new Assert\GreaterThanOrEqual(array('value'   => 0,
                                                                                                    'message' => 'Order total amount can not be negative.')));

        /**
         * These address validations are the same as the address model, but they can be entered directly
         * in the order form.  So validate them here, too.
         */
        // Shipping Address these validations should be the same as the actual address model.
        $metadata->addPropertyConstraint('orderShippingAddressFirstName', new Assert\NotBlank(array('message' => 'Shipping first name can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingAddressFirstName', new Assert\Length(array('max'        => 60,
                                                                                                  'maxMessage' => 'Shipping first name may only be 60 characters.')));
        $metadata->addPropertyConstraint('orderShippingAddressLastName', new Assert\NotBlank(array('message' => 'Shipping last name can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingAddressLastName', new Assert\Length(array('max'        => 60,
                                                                                                 'maxMessage' => 'Shipping last name may only be 60 characters.')));
        $metadata->addPropertyConstraint('orderShippingAddressStreet', new Assert\NotBlank(array('message' => 'Shipping street can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingAddressStreet', new Assert\Length(array('max'        => 255,
                                                                                               'maxMessage' => 'Shipping street may only be 255 characters.')));
        $metadata->addPropertyConstraint('orderShippingAddressCity', new Assert\NotBlank(array('message' => 'Shipping city can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingAddressCity', new Assert\Length(array('max'        => 60,
                                                                                             'maxMessage' => 'Shipping city may only be 60 characters.')));
        $metadata->addPropertyConstraint('orderShippingAddressState', new Assert\NotBlank(array('message' => 'Shipping state can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingAddressState', new Assert\Choice(array('choices' => State::getValidStateCodes(),
                                                                                              'message' => 'Shipping state code is not valid.')));
        $metadata->addPropertyConstraint('orderShippingAddressZipcode', new Assert\NotBlank(array('message' => 'Shipping zip code can not be blank.')));
        $metadata->addPropertyConstraint('orderShippingAddressZipcode', 
                                         new Assert\Regex(array('pattern' => '/^\d{5}(-\d{4})?$/',
                                                                'message' => 'Shipping zip code must be 5 digits or 9 digits with a hyphen.')));
        $metadata->addPropertyConstraint('orderShippingAddressCountry', new Assert\EqualTo(array('value' => 'USA',
                                                                                                 'message'=> 'We only ship within the US.')));

        // Billing address stuff
        $metadata->addPropertyConstraint('orderBillingAddressFirstName', new Assert\NotBlank(array('message' => 'Billing first name can not be blank.')));
        $metadata->addPropertyConstraint('orderBillingAddressFirstName', new Assert\Length(array('max'        => 60,
                                                                                                 'maxMessage' => 'Billing first name may only be 60 characters.')));
        $metadata->addPropertyConstraint('orderBillingAddressLastName', new Assert\NotBlank(array('message' => 'Billing last name can not be blank.')));
        $metadata->addPropertyConstraint('orderBillingAddressLastName', new Assert\Length(array('max'        => 60,
                                                                                                'maxMessage' => 'Billing last name may only be 60 characters.')));
        $metadata->addPropertyConstraint('orderBillingAddressStreet', new Assert\NotBlank(array('message' => 'Billing street can not be blank.')));
        $metadata->addPropertyConstraint('orderBillingAddressStreet', new Assert\Length(array('max'        => 255,
                                                                                              'maxMessage' => 'Billing street may only be 255 characters.')));
        $metadata->addPropertyConstraint('orderBillingAddressCity', new Assert\NotBlank(array('message' => 'Billing city can not be blank.')));
        $metadata->addPropertyConstraint('orderBillingAddressCity', new Assert\Length(array('max'        => 60,
                                                                                            'maxMessage' => 'Billing city may only be 60 characters.')));
        $metadata->addPropertyConstraint('orderBillingAddressState', new Assert\NotBlank(array('message' => 'Billing state can not be blank.')));
        $metadata->addPropertyConstraint('orderBillingAddressState', new Assert\Choice(array('choices' => State::getValidStateCodes(),
                                                                                             'message' => 'Billing state code is not valid.')));
        $metadata->addPropertyConstraint('orderBillingAddressZipcode', new Assert\NotBlank(array('message' => 'Billing zip code can not be blank.')));
        $metadata->addPropertyConstraint('orderBillingAddressZipcode', 
                                         new Assert\Regex(array('pattern' => '/^\d{5}(-\d{4})?$/',
                                                                'message' => 'Billing zip code must be 5 digits or 9 digits with a hyphen')));
        $metadata->addPropertyConstraint('orderBillingAddressCountry', new Assert\EqualTo(array('value' => 'USA',
                                                                                                'message'=> 'We only ship within the US.')));
    }

    /**
     * If there is a coupon, make sure it's not expired.
     */
    public function validateCouponId(ExecutionContextInterface $context)
    {
        if ($this->couponId) {
            if ($this->getCoupon() instanceof Coupon) {
                $nowTime = new \DateTime();
                if ($this->getCoupon()->getCouponDateEnd() < $nowTime) {
                    $context->addViolationAt('couponId', 'The entered coupon is expired.');
                }
            } else {
                $context->addViolationAt('couponId', 'The entered coupon is invalid.');
            }
        } else {
            $this->orderAmountCoupon = 0;
        }
    }
    
    /**
     * If all fields of both addresses are the same, then the addresses are the same.
     *
     * @return bool
     */
    public function areAddressesDuplicate()
    {
        
        return (($this->orderShippingAddressFirstName   == $this->orderBillingAddressFirstName) &&
                ($this->orderShippingAddressLastName    == $this->orderBillingAddressLastName) &&
                ($this->orderShippingAddressStreet      == $this->orderBillingAddressStreet) &&
                ($this->orderShippingAddressCity        == $this->orderBillingAddressCity) &&
                ($this->orderShippingAddressState       == $this->orderBillingAddressState) &&
                ($this->orderShippingAddressZipcode     == $this->orderBillingAddressZipcode) &&
                ($this->orderShippingAddressCountry     == $this->orderShippingAddressCountry));
    }

    /**
     * Get orderId
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set basketId
     *
     * @param integer $basketId
     * @return Order
     */
    public function setBasketId($basketId)
    {
        $this->basketId = $basketId;

        return $this;
    }

    /**
     * Get basketId
     *
     * @return integer
     */
    public function getBasketId()
    {
        return $this->basketId;
    }

    /**
     * Set orderStatus
     *
     * @param string $orderStatus
     * @return Order
     */
    public function setOrderStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * Set orderDateCreation
     *
     * @param \DateTime $orderDateCreation
     * @return Order
     */
    public function setOrderDateCreation($orderDateCreation)
    {
        $this->orderDateCreation = $orderDateCreation;

        return $this;
    }

    /**
     * Get orderDateCreation
     *
     * @return \DateTime
     */
    public function getOrderDateCreation()
    {
        return $this->orderDateCreation;
    }

    /**
     * Set orderDateEnd
     *
     * @param \DateTime $orderDateEnd
     * @return Order
     */
    public function setOrderDateEnd($orderDateEnd)
    {
        $this->orderDateEnd = $orderDateEnd;

        return $this;
    }

    /**
     * Get orderDateEnd
     *
     * @return \DateTime
     */
    public function getOrderDateEnd()
    {
        return $this->orderDateEnd;
    }

    /**
     * Set orderUserFirstName
     *
     * @param string $orderUserFirstName
     * @return Order
     */
    public function setOrderUserFirstName($orderUserFirstName)
    {
        $this->orderUserFirstName = $orderUserFirstName;

        return $this;
    }

    /**
     * Get orderUserFirstName
     *
     * @return string
     */
    public function getOrderUserFirstName()
    {
        return $this->orderUserFirstName;
    }

    /**
     * Set orderUserLastName
     *
     * @param string $orderUserLastName
     * @return Order
     */
    public function setOrderUserLastName($orderUserLastName)
    {
        $this->orderUserLastName = $orderUserLastName;

        return $this;
    }

    /**
     * Get orderUserLastName
     *
     * @return string
     */
    public function getOrderUserLastName()
    {
        return $this->orderUserLastName;
    }

    /**
     * Set orderUserEmail
     *
     * @param string $orderUserEmail
     * @return Order
     */
    public function setOrderUserEmail($orderUserEmail)
    {
        $this->orderUserEmail = $orderUserEmail;

        return $this;
    }

    /**
     * Get orderUserEmail
     *
     * @return string
     */
    public function getOrderUserEmail()
    {
        return $this->orderUserEmail;
    }

    /**
     * Set orderAmount
     *
     * @param float $orderAmount
     * @return Order
     */
    public function setOrderAmount($orderAmount)
    {
        $this->orderAmount = $orderAmount;

        return $this;
    }

    /**
     * Get orderAmount
     *
     * @return float
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    /**
     * Set orderAmountCoupon
     *
     * @param float $orderAmountCoupon
     * @return Order
     */
    public function setOrderAmountCoupon($orderAmountCoupon)
    {
        $this->orderAmountCoupon = $orderAmountCoupon;

        return $this;
    }

    /**
     * Get orderAmountCoupon
     *
     * @return float
     */
    public function getOrderAmountCoupon()
    {
        return $this->orderAmountCoupon;
    }

    /**
     * Set orderAmountVat
     *
     * @param float $orderAmountVat
     * @return Order
     */
    public function setOrderAmountVat($orderAmountVat)
    {
        $this->orderAmountVat = $orderAmountVat;

        return $this;
    }

    /**
     * Get orderAmountVat
     *
     * @return float
     */
    public function getOrderAmountVat()
    {
        return $this->orderAmountVat;
    }

    /**
     * Set orderAmountShipping
     *
     * @param float $orderAmountShipping
     * @return Order
     */
    public function setOrderAmountShipping($orderAmountShipping)
    {
        $this->orderAmountShipping = $orderAmountShipping;

        return $this;
    }

    /**
     * Get orderAmountShipping
     *
     * @return float
     */
    public function getOrderAmountShipping()
    {
        return $this->orderAmountShipping;
    }

    /**
     * Set orderAmountCredits
     *
     * @param float $orderAmountCredits
     * @return Order
     */
    public function setOrderAmountCredits($orderAmountCredits)
    {
        $this->orderAmountCredits = $orderAmountCredits;

        return $this;
    }

    /**
     * Get orderAmountCredits
     *
     * @return float
     */
    public function getOrderAmountCredits()
    {
        return $this->orderAmountCredits;
    }

    /**
     * Set orderAmountTotal
     *
     * @param float $orderAmountTotal
     * @return Order
     */
    public function setOrderAmountTotal($orderAmountTotal)
    {
        $this->orderAmountTotal = $orderAmountTotal;

        return $this;
    }

    /**
     * Get orderAmountTotal
     *
     * @return float
     */
    public function getOrderAmountTotal()
    {
        return $this->orderAmountTotal;
    }

    /**
     * Set orderProducts
     *
     * @param string $orderProducts
     * @return Order
     */
    public function setOrderProducts($orderProducts)
    {
        $this->orderProducts = $orderProducts;

        return $this;
    }

    /**
     * Get orderProducts
     *
     * @return string
     */
    public function getOrderProducts()
    {
        return $this->orderProducts;
    }

    /**
     * Set orderShippingMethod
     *
     * @param string $orderShippingMethod
     * @return Order
     */
    public function setOrderShippingMethod($orderShippingMethod, $shippingManager=false)
    {
        $this->orderShippingMethod = $orderShippingMethod;
        if ($shippingManager) {
            $this->orderAmountShipping = $shippingManager->getShippingCost($orderShippingMethod);
        }

        return $this;
    }

    /**
     * Get orderShippingMethod
     *
     * @return string
     */
    public function getOrderShippingMethod()
    {
        return $this->orderShippingMethod;
    }

    /**
     * Set orderShippingAddressFirstName
     *
     * @param string $orderShippingAddressFirstName
     * @return Order
     */
    public function setOrderShippingAddressFirstName($orderShippingAddressFirstName)
    {
        $this->orderShippingAddressFirstName = $orderShippingAddressFirstName;

        return $this;
    }

    /**
     * Get orderShippingAddressFirstName
     *
     * @return string
     */
    public function getOrderShippingAddressFirstName()
    {
        return $this->orderShippingAddressFirstName;
    }

    /**
     * Set orderShippingAddressLastName
     *
     * @param string $orderShippingAddressLastName
     * @return Order
     */
    public function setOrderShippingAddressLastName($orderShippingAddressLastName)
    {
        $this->orderShippingAddressLastName = $orderShippingAddressLastName;

        return $this;
    }

    /**
     * Get orderShippingAddressLastName
     *
     * @return string
     */
    public function getOrderShippingAddressLastName()
    {
        return $this->orderShippingAddressLastName;
    }

    /**
     * Set orderShippingAddressStreet
     *
     * @param string $orderShippingAddressStreet
     * @return Order
     */
    public function setOrderShippingAddressStreet($orderShippingAddressStreet)
    {
        $this->orderShippingAddressStreet = $orderShippingAddressStreet;

        return $this;
    }

    /**
     * Get orderShippingAddressStreet
     *
     * @return string
     */
    public function getOrderShippingAddressStreet()
    {
        return $this->orderShippingAddressStreet;
    }

    /**
     * Set orderShippingAddressCity
     *
     * @param string $orderShippingAddressCity
     * @return Order
     */
    public function setOrderShippingAddressCity($orderShippingAddressCity)
    {
        $this->orderShippingAddressCity = $orderShippingAddressCity;

        return $this;
    }

    /**
     * Get orderShippingAddressCity
     *
     * @return string
     */
    public function getOrderShippingAddressCity()
    {
        return $this->orderShippingAddressCity;
    }

    /**
     * Set orderShippingAddressState
     *
     * @param string $orderShippingAddressState
     * @return Order
     */
    public function setOrderShippingAddressState($orderShippingAddressState)
    {
        $this->orderShippingAddressState = $orderShippingAddressState;

        return $this;
    }

    /**
     * Get orderShippingAddressState
     *
     * @return string
     */
    public function getOrderShippingAddressState()
    {
        return $this->orderShippingAddressState;
    }

    /**
     * Set orderShippingAddressZipcode
     *
     * @param string $orderShippingAddressZipcode
     * @return Order
     */
    public function setOrderShippingAddressZipcode($orderShippingAddressZipcode)
    {
        $this->orderShippingAddressZipcode = $orderShippingAddressZipcode;

        return $this;
    }

    /**
     * Get orderShippingAddressZipcode
     *
     * @return string
     */
    public function getOrderShippingAddressZipcode()
    {
        return $this->orderShippingAddressZipcode;
    }

    /**
     * Set orderShippingAddressCountry
     *
     * @param string $orderShippingAddressCountry
     * @return Order
     */
    public function setOrderShippingAddressCountry($orderShippingAddressCountry)
    {
        $this->orderShippingAddressCountry = $orderShippingAddressCountry;

        return $this;
    }

    /**
     * Get orderShippingAddressCountry
     *
     * @return string
     */
    public function getOrderShippingAddressCountry()
    {
        return $this->orderShippingAddressCountry;
    }

    /**
     * Set orderBillingAddressFirstName
     *
     * @param string $orderBillingAddressFirstName
     * @return Order
     */
    public function setOrderBillingAddressFirstName($orderBillingAddressFirstName)
    {
        $this->orderBillingAddressFirstName = $orderBillingAddressFirstName;

        return $this;
    }

    /**
     * Get orderBillingAddressFirstName
     *
     * @return string
     */
    public function getOrderBillingAddressFirstName()
    {
        return $this->orderBillingAddressFirstName;
    }

    /**
     * Set orderBillingAddressLastName
     *
     * @param string $orderBillingAddressLastName
     * @return Order
     */
    public function setOrderBillingAddressLastName($orderBillingAddressLastName)
    {
        $this->orderBillingAddressLastName = $orderBillingAddressLastName;

        return $this;
    }

    /**
     * Get orderBillingAddressLastName
     *
     * @return string
     */
    public function getOrderBillingAddressLastName()
    {
        return $this->orderBillingAddressLastName;
    }

    /**
     * Set orderBillingAddressStreet
     *
     * @param string $orderBillingAddressStreet
     * @return Order
     */
    public function setOrderBillingAddressStreet($orderBillingAddressStreet)
    {
        $this->orderBillingAddressStreet = $orderBillingAddressStreet;

        return $this;
    }

    /**
     * Get orderBillingAddressStreet
     *
     * @return string
     */
    public function getOrderBillingAddressStreet()
    {
        return $this->orderBillingAddressStreet;
    }

    /**
     * Set orderBillingAddressCity
     *
     * @param string $orderBillingAddressCity
     * @return Order
     */
    public function setOrderBillingAddressCity($orderBillingAddressCity)
    {
        $this->orderBillingAddressCity = $orderBillingAddressCity;

        return $this;
    }

    /**
     * Get orderBillingAddressCity
     *
     * @return string
     */
    public function getOrderBillingAddressCity()
    {
        return $this->orderBillingAddressCity;
    }

    /**
     * Set orderBillingAddressState
     *
     * @param string $orderBillingAddressState
     * @return Order
     */
    public function setOrderBillingAddressState($orderBillingAddressState)
    {
        $this->orderBillingAddressState = $orderBillingAddressState;

        return $this;
    }

    /**
     * Get orderBillingAddressState
     *
     * @return string
     */
    public function getOrderBillingAddressState()
    {
        return $this->orderBillingAddressState;
    }

    /**
     * Set orderBillingAddressZipcode
     *
     * @param string $orderBillingAddressZipcode
     * @return Order
     */
    public function setOrderBillingAddressZipcode($orderBillingAddressZipcode)
    {
        $this->orderBillingAddressZipcode = $orderBillingAddressZipcode;

        return $this;
    }

    /**
     * Get orderBillingAddressZipcode
     *
     * @return string
     */
    public function getOrderBillingAddressZipcode()
    {
        return $this->orderBillingAddressZipcode;
    }

    /**
     * Set orderBillingAddressCountry
     *
     * @param string $orderBillingAddressCountry
     * @return Order
     */
    public function setOrderBillingAddressCountry($orderBillingAddressCountry)
    {
        $this->orderBillingAddressCountry = $orderBillingAddressCountry;

        return $this;
    }

    /**
     * Get orderBillingAddressCountry
     *
     * @return string
     */
    public function getOrderBillingAddressCountry()
    {
        return $this->orderBillingAddressCountry;
    }

    /**
     * Set orderUserIpAddress
     *
     * @param string $orderUserIpAddress
     * @return Order
     */
    public function setOrderUserIpAddress($orderUserIpAddress)
    {
        $this->orderUserIpAddress = $orderUserIpAddress;

        return $this;
    }

    /**
     * Get orderUserIpAddress
     *
     * @return string
     */
    public function getOrderUserIpAddress()
    {
        return $this->orderUserIpAddress;
    }

    /**
     * Set couponId
     *
     * @param integer $couponId
     * @return Order
     */
    public function setCouponId($couponId)
    {
        $this->couponId = $couponId;

        return $this;
    }

    /**
     * Get couponId
     *
     * @return integer
     */
    public function getCouponId()
    {
        return $this->couponId;
    }
    /**
     * @var \NiftyThrifty\ShopBundle\Entity\Basket
     */
    private $basket;


    /**
     * Set basket
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Basket $basket
     * @return Order
     */
    public function setBasket(\NiftyThrifty\ShopBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return \NiftyThrifty\ShopBundle\Entity\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforeSave()
    {
        $this->orderAmountTotal = $this->orderAmount + 
                                  $this->orderAmountShipping + 
                                  $this->orderAmountVat - 
                                  ($this->orderAmountCredits + $this->orderAmountCoupon);
        $this->orderDateCreation= $this->orderDateCreation ? $this->orderDateCreation : new \DateTime();
        $this->orderDateEnd     = new \DateTime();
        $this->orderDateEnd->modify("12 hours");
        $this->orderStatus      = $this->orderStatus ? $this->orderStatus : self::STATUS_UNPAID;
    }

    /**
     * Get the full order total.
     */
    public function getOrderTotal()
    {
        $total = 0;

        // Total the positives.
        $total = $this->orderAmount + $this->orderAmountShipping + $this->orderAmountVat;

        // Apply negatives
        $total = $total - ($this->orderAmountCoupon + $this->orderAmountCredits);

        return $total > 0 ? $total : 0;
    }
    
    /**
     * The shopping cart displays the total before applying credits and the total after.
     */
    public function getOrderTotalPreCredits()
    {
        $total = ($this->orderAmount + $this->orderAmountShipping + $this->orderAmountVat) - $this->orderAmountCoupon;
        return $total > 0 ? $total : 0;
    }

    /**
     * Get the order total that we will use to cacluclate whether the order qualifies for free shipping.  This is currently
     * defined as the item total minus coupons.  Credit, tax, and, obviously, shipping doesn't count here.
     * 
     * @param void
     * @return float;
     */
    public function getShippingCostTotal()
    {
        return $this->orderAmount - $this->orderAmountCoupon;
    }
    
    /**
     * Set coupon
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Coupon $coupon
     * @return Order
     */
    public function setCoupon(\NiftyThrifty\ShopBundle\Entity\Coupon $coupon = null)
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * Get coupon
     *
     * @return \NiftyThrifty\ShopBundle\Entity\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }
}
