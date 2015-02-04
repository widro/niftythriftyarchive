<?php

namespace NiftyThrifty\ShopBundle\Service;

class ShippingCostServiceException extends \Exception {}

class ShippingCostService
{
    private $_sitewideFreeShipping;
    private $_cartTotalFreeShipping;
    private $_classicShippingCost;
    private $_expressShippingCost;
    private $_user;
    private $_orderTotal;
    private $_itemCount;
    private $_coupon;

    const CLASSIC_SHIPPING = 'classic';
    const EXPRESS_SHIPPING = 'express';

    /**
     * The constructor sets whether or not sitewide free shipping is on and what the threshold to
     * get free shipping on an individual order is.
     */
    public function __construct($sitewideFreeShipping, $cartTotalFreeShipping, $classicShippingCost, $expressShippingCost)
    {
        $this->_sitewideFreeShipping    = $sitewideFreeShipping == 'yes';
        $this->_cartTotalFreeShipping   = $cartTotalFreeShipping;
        $this->_classicShippingCost     = $sitewideFreeShipping ? $classicShippingCost : 0;
        $this->_expressShippingCost     = $expressShippingCost;
        $this->_orderTotal              = 0.0;
        $this->_itemCount               = 0;
        $this->_coupon                  = null;
    }

    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }

    public function setOrderTotal($amount)
    {
        $this->_orderTotal = $amount;
        return $this;
    }

    public function setItemCount($itemCount)
    {
        $this->_itemCount = $itemCount;
        return $this;
    }
    
    public function setCoupon($coupon)
    {
        $this->_coupon = $coupon;
        return $this;
    }

    /**
     * Check if the sitewide free shipping flag is turned on.
     *
     * @return boolean
     */
    public function isSitewideFreeShipping()
    {
        return $this->_sitewideFreeShipping;
    }

    /**
     * Translate a shipping selection to it's cost.
     *
     * @param   string      classic or express.
     * @return  float
     */
    public function getShippingCost($shippingType)
    {
        if ($shippingType == self::CLASSIC_SHIPPING) {
            return $this->isFreeShipping() ? 0.00 : $this->getClassicShippingCost();
        } else if ($shippingType == self::EXPRESS_SHIPPING) {
            return $this->_expressShippingCost;
        } else {
            throw new ShippingCostServiceException('Invalid shipping method select.  Valid methods are classic or express.');
        }
    }

    /**
     * The classic shipping cost changes dependent on the number of items in a cart.  I'm writing
     * this as an offset, even though I don't think this is the correct way to do this in the long term.
     *
     * @param void.
     * @return float
     */
    public function getClassicShippingCost()
    {
        if ($this->isFreeShipping()) {
            return '0.00';
        } else {
            if (!is_numeric($this->_itemCount)) throw new ShippingCostServiceException('Item count is required to get shipping cost.');

            if (($this->_itemCount == 1) || ($this->_itemCount == 0)) {
                return $this->_classicShippingCost;
            } else if ($this->_itemCount == 2) {
                return $this->_classicShippingCost+1;
            } else {
                return $this->_classicShippingCost+2;
            }
        }
    }

    /**
     * Check all ways shipping may be free, and return true if it is.
     *
     * @param   float                           The total amount of a shopping cart.
     * @param   NiftyThriftyShopBundle:User     The user, currently unused.
     * @return  boolean
     */
    public function isFreeShipping()
    {
        // If sitewide free shipping is on, it's just free
        if ($this->_sitewideFreeShipping) {
            return true;

        // Otherwise, check other conditions.
        } else {
            
            // If a coupon has been set, check if it includes free shipping.
            if ($this->_coupon && $this->_coupon->getCouponFreeShipping() == 'true') {
                return true;
            }

            // The only current condition is that the order total is over $70
            return $this->_orderTotal >= $this->_cartTotalFreeShipping;
        }
    }

    /**
     * Returns an array for shipping options in the order form menu.
     */
    public function getShippingChoices()
    {
        return array(self::CLASSIC_SHIPPING => $this->isFreeShipping() ? 'Free: $0.00' : 'Classic: $' . $this->getClassicShippingCost(),
                     self::EXPRESS_SHIPPING => 'Express: $' . $this->_expressShippingCost);
    }
}
