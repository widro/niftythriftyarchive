<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * NiftyThrifty\ShopBundle\Entity\Coupon
 *
 * @ORM\Table(name="coupon")
 * @ORM\Entity
 */
class Coupon
{
    /**
     * @var integer $couponId
     *
     * @ORM\Column(name="coupon_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $couponId;

    /**
     * @var string $couponCode
     *
     * @ORM\Column(name="coupon_code", type="string", length=255, nullable=false)
     */
    private $couponCode;

    /**
     * @var \DateTime $couponDateStart
     *
     * @ORM\Column(name="coupon_date_start", type="date", nullable=true)
     */
    private $couponDateStart;

    /**
     * @var \DateTime $couponDateEnd
     *
     * @ORM\Column(name="coupon_date_end", type="date", nullable=true)
     */
    private $couponDateEnd;

    /**
     * @var float $couponPercent
     *
     * @ORM\Column(name="coupon_percent", type="float", nullable=true)
     */
    private $couponPercent;

    /**
     * @var float $couponAmount
     *
     * @ORM\Column(name="coupon_amount", type="float", nullable=true)
     */
    private $couponAmount;

    /**
     * @var string $couponQuantityLimited
     *
     * @ORM\Column(name="coupon_quantity_limited", type="string", nullable=false)
     */
    private $couponQuantityLimited;

    /**
     * @var integer $couponQuantity
     *
     * @ORM\Column(name="coupon_quantity", type="integer", nullable=true)
     */
    private $couponQuantity;

    /**
     * @var string $couponUnique
     *
     * @ORM\Column(name="coupon_unique", type="string", nullable=false)
     */
    private $couponUnique;

    /**
     * @var \DateTime $couponDateAdd
     *
     * @ORM\Column(name="coupon_date_add", type="datetime", nullable=false)
     */
    private $couponDateAdd;

    /**
     * @var string $couponFreeShipping
     *
     * @ORM\Column(name="coupon_free_shipping", type="string", nullable=false)
     */
    private $couponFreeShipping;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=true)
     */
    private $userId;
    
    /**
     * Add method for the CRUD controllers.
     */
    public function getId()
    {
        return $this->getCouponId();
    }

    /**
     * Object validation
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Coupon code can not be blank
        $metadata->addPropertyConstraint('couponCode', new Assert\NotBlank(array('message' => 'Coupon code can not be blank.')));
        $metadata->addPropertyConstraint('couponCode', new Assert\Length(array('max'        => 15,
                                                                               'maxMessage' => 'Coupon code may only be 15 characters.')));

        // Three fields must be either true or false
        $metadata->addPropertyConstraint('couponQuantityLimited', new Assert\NotBlank(array('message' => 'Select a limited quantity option.')));
        $metadata->addPropertyConstraint('couponQuantityLimited', new Assert\Choice(array('choices' => array('true','false'),
                                                                                          'message' => 'Select a limited quantity option.')));
        $metadata->addPropertyConstraint('couponUnique',          new Assert\NotBlank(array('message' => 'Select a unique coupon option.')));
        $metadata->addPropertyConstraint('couponUnique',          new Assert\Choice(array('choices' => array('true','false'),
                                                                                          'message' => 'Select a unique coupon option.')));
        $metadata->addPropertyConstraint('couponFreeShipping',    new Assert\NotBlank(array('message' => 'Select a free shipping option.')));
        $metadata->addPropertyConstraint('couponFreeShipping',    new Assert\Choice(array('choices' => array('true','false'),
                                                                                          'message' => 'Select a free shipping option.')));

        // Date added must be selected.
        $metadata->addPropertyConstraint('couponDateAdd', new Assert\NotBlank(array('message' => 'Addition time must be set.')));
        $metadata->addPropertyConstraint('couponDateAdd', new Assert\DateTime(array('message' => 'Date added is an invalid date.')));

        /**
         * Callback validations
         *  - If start/end dates are set, validate them (date_start/date_end).
         *  - One of percent or amount must be defined (percent/amount).
         */
        $metadata->addConstraint(new Assert\Callback(array('methods' => array('validateDates', 'validateValue'))));
    }

    /**
     * Callback validation of date start and date end.
     */
    public function validateDates(ExecutionContextInterface $context)
    {
        if ($this->couponDateStart) {
            $context->validateValue($this->couponDateStart,
                                        new Assert\DateTime(array('message' => 'Start date is not a valid date.')),
                                        'couponDateStart');
        }

        if ($this->couponDateEnd) {
            $context->validateValue($this->couponDateEnd, 
                                        new Assert\DateTime(array('message' => 'End date is not a valid date.')), 
                                        'couponDateEnd');
        }
    }

    /**
     * Callback validation of amount vs percentage.  The two values are exclusive or.
     */
    public function validateValue(ExecutionContextInterface $context)
    {
        if (!$this->couponPercent && !$this->couponAmount) {
            $context->addViolationAt('couponPercent', 'A discount percentage or discount amount must be selected.');
        }

        if ($this->couponPercent && $this->couponAmount) {
            $context->addViolationAt('couponPercent', 'Both percent and amount can not be set at the same time.');
        }
    }
    
    /**
     * Given a value, return how much the discount of this coupon is.
     *
     * @param   int     $cost       The basket total
     * @return  float
     */
    public function getDiscount($value)
    {
        $discount = 0;
        
        if ($this->couponAmount) {
            $discount = $this->couponAmount > $value ? $value : $this->couponAmount;
        } else if ($this->couponPercent) {
            $discount = round($value * ($this->couponPercent / 100), 2);
        }
        
        return $discount;
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
     * Set couponCode
     *
     * @param string $couponCode
     * @return Coupon
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
    
        return $this;
    }

    /**
     * Get couponCode
     *
     * @return string 
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * Set couponDateStart
     *
     * @param \DateTime $couponDateStart
     * @return Coupon
     */
    public function setCouponDateStart($couponDateStart)
    {
        $this->couponDateStart = $couponDateStart;
    
        return $this;
    }

    /**
     * Get couponDateStart
     *
     * @return \DateTime 
     */
    public function getCouponDateStart()
    {
        return $this->couponDateStart;
    }

    /**
     * Set couponDateEnd
     *
     * @param \DateTime $couponDateEnd
     * @return Coupon
     */
    public function setCouponDateEnd($couponDateEnd)
    {
        $this->couponDateEnd = $couponDateEnd;
    
        return $this;
    }

    /**
     * Get couponDateEnd
     *
     * @return \DateTime 
     */
    public function getCouponDateEnd()
    {
        return $this->couponDateEnd;
    }

    /**
     * Set couponPercent
     *
     * @param float $couponPercent
     * @return Coupon
     */
    public function setCouponPercent($couponPercent)
    {
        $this->couponPercent = $couponPercent;
    
        return $this;
    }

    /**
     * Get couponPercent
     *
     * @return float 
     */
    public function getCouponPercent()
    {
        return $this->couponPercent;
    }

    /**
     * Set couponAmount
     *
     * @param float $couponAmount
     * @return Coupon
     */
    public function setCouponAmount($couponAmount)
    {
        $this->couponAmount = $couponAmount;
    
        return $this;
    }

    /**
     * Get couponAmount
     *
     * @return float 
     */
    public function getCouponAmount()
    {
        return $this->couponAmount;
    }

    /**
     * Set couponQuantityLimited
     *
     * @param string $couponQuantityLimited
     * @return Coupon
     */
    public function setCouponQuantityLimited($couponQuantityLimited)
    {
        $this->couponQuantityLimited = $couponQuantityLimited;
    
        return $this;
    }

    /**
     * Get couponQuantityLimited
     *
     * @return string 
     */
    public function getCouponQuantityLimited()
    {
        return $this->couponQuantityLimited;
    }

    /**
     * Set couponQuantity
     *
     * @param integer $couponQuantity
     * @return Coupon
     */
    public function setCouponQuantity($couponQuantity)
    {
        $this->couponQuantity = $couponQuantity;
    
        return $this;
    }

    /**
     * Get couponQuantity
     *
     * @return integer 
     */
    public function getCouponQuantity()
    {
        return $this->couponQuantity;
    }

    /**
     * Set couponUnique
     *
     * @param string $couponUnique
     * @return Coupon
     */
    public function setCouponUnique($couponUnique)
    {
        $this->couponUnique = $couponUnique;
    
        return $this;
    }

    /**
     * Get couponUnique
     *
     * @return string 
     */
    public function getCouponUnique()
    {
        return $this->couponUnique;
    }

    /**
     * Set couponDateAdd
     *
     * @param \DateTime $couponDateAdd
     * @return Coupon
     */
    public function setCouponDateAdd($couponDateAdd)
    {
        $this->couponDateAdd = $couponDateAdd;
    
        return $this;
    }

    /**
     * Get couponDateAdd
     *
     * @return \DateTime 
     */
    public function getCouponDateAdd()
    {
        return $this->couponDateAdd;
    }

    /**
     * Set couponFreeShipping
     *
     * @param string $couponFreeShipping
     * @return Coupon
     */
    public function setCouponFreeShipping($couponFreeShipping)
    {
        $this->couponFreeShipping = $couponFreeShipping;
    
        return $this;
    }

    /**
     * Get couponFreeShipping
     *
     * @return string 
     */
    public function getCouponFreeShipping()
    {
        return $this->couponFreeShipping;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Coupon
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
