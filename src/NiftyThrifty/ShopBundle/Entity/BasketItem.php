<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NiftyThrifty\ShopBundle\Entity\BasketItem
 *
 * @ORM\Table(name="basket_item")
 * @ORM\Entity
 */
class BasketItem
{
    /**
     * @var integer $basketItemId
     *
     * @ORM\Column(name="basket_item_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $basketItemId;

    /**
     * @var integer $basketId
     *
     * @ORM\Column(name="basket_id", type="bigint", nullable=false)
     */
    private $basketId;

    /**
     * @var integer $productId
     *
     * @ORM\Column(name="product_id", type="bigint", nullable=false)
     */
    private $productId;

    /**
     * @var \DateTime $basketItemDateAdd
     *
     * @ORM\Column(name="basket_item_date_add", type="datetime", nullable=false)
     */
    private $basketItemDateAdd;

    /**
     * @var \DateTime $basketItemDateEnd
     *
     * @ORM\Column(name="basket_item_date_end", type="datetime", nullable=false)
     */
    private $basketItemDateEnd;

    /**
     * @var integer $basketItemPrice
     *
     * @ORM\Column(name="basket_item_price", type="integer", nullable=false)
     */
    private $basketItemPrice;

    /**
     * @var integer $basketItemDiscount
     *
     * @ORM\Column(name="basket_item_discount", type="integer", nullable=false)
     */
    private $basketItemDiscount;

    /**
     * @var string $basketItemStatus
     *
     * @ORM\Column(name="basket_item_status", type="string", nullable=false)
     */
    private $basketItemStatus;

    /**
     * @var NiftyThrifty\ShopBundle\Entity\Product
     */
    private $product;

    /**
     * @var NiftyThrifty\ShopBundle\Entity\Basket
     */
    private $basket;

    /**
     * Enum basket_item.basket_item_status legal values
     */
    const VALID     = 'valid';
    const PAYMENT   = 'payment';

    public function getBasketItemTimeRemaining($format='s')
    {
        $nowTime = new \DateTime();

        $interval = $this->basketItemDateEnd->diff($nowTime);

        return $interval->format("%$format");
    }

    /**
     * When creating a new basket item, set the date it was added to the basket, when it
     * will expire from the basket, and the last update time of the basket it's going in to.
     * Also verify the item is not saved actively in another user's cart.
     *
     * Note: in Doctrine 2.4, this function should take one parameter PrePersistEventArgs $event
     * and the item availability check in NiftyThriftyEventListener should be moved here.
     * @prePersist()
     *
     * The stuff here checks for null values and then sets defaults.  This is so unit tests can
     * set dummy data correctly.
     */
    public function validateItem()
    {
        if (!$this->basketItemDateAdd) {
            $this->basketItemDateAdd = new \DateTime("now");
        }

        if (!$this->basketItemDateEnd) {
            $this->basketItemDateEnd = new \DateTime();
            $this->basketItemDateEnd->modify("+10 minutes");
        }

        if (!$this->basketItemStatus) {
            $this->basketItemStatus = self::VALID;
        }
    }


    /**
     * Get basketItemId
     *
     * @return integer
     */
    public function getBasketItemId()
    {
        return $this->basketItemId;
    }

    /**
     * Set basketId
     *
     * @param integer $basketId
     * @return BasketItem
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
     * Set productId
     *
     * @param integer $productId
     * @return BasketItem
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set basketItemDateAdd
     *
     * @param \DateTime $basketItemDateAdd
     * @return BasketItem
     */
    public function setBasketItemDateAdd($basketItemDateAdd)
    {
        $this->basketItemDateAdd = $basketItemDateAdd;

        return $this;
    }

    /**
     * Get basketItemDateAdd
     *
     * @return \DateTime
     */
    public function getBasketItemDateAdd()
    {
        return $this->basketItemDateAdd;
    }

    /**
     * Set basketItemDateEnd
     *
     * @param \DateTime $basketItemDateEnd
     * @return BasketItem
     */
    public function setBasketItemDateEnd($basketItemDateEnd)
    {
        $this->basketItemDateEnd = $basketItemDateEnd;

        return $this;
    }

    /**
     * Get basketItemDateEnd
     *
     * @return \DateTime
     */
    public function getBasketItemDateEnd()
    {
        return $this->basketItemDateEnd;
    }

    /**
     * Set basketItemPrice
     *
     * @param integer $basketItemPrice
     * @return BasketItem
     */
    public function setBasketItemPrice($basketItemPrice)
    {
        $this->basketItemPrice = $basketItemPrice;

        return $this;
    }

    /**
     * Get basketItemPrice
     *
     * @return integer
     */
    public function getBasketItemPrice()
    {
        return $this->basketItemPrice;
    }

    /**
     * Set basketItemDiscount
     *
     * @param integer $basketItemDiscount
     * @return BasketItem
     */
    public function setBasketItemDiscount($basketItemDiscount)
    {
        $this->basketItemDiscount = $basketItemDiscount;

        return $this;
    }

    /**
     * Get basketItemDiscount
     *
     * @return integer
     */
    public function getBasketItemDiscount()
    {
        return $this->basketItemDiscount;
    }

    /**
     * Set basketItemStatus
     *
     * @param string $basketItemStatus
     * @return BasketItem
     */
    public function setBasketItemStatus($basketItemStatus)
    {
        $this->basketItemStatus = $basketItemStatus;

        return $this;
    }

    /**
     * Get basketItemStatus
     *
     * @return string
     */
    public function getBasketItemStatus()
    {
        return $this->basketItemStatus;
    }

    /**
     * Set product
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $product
     * @return BasketItem
     */
    public function setProduct(\NiftyThrifty\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return NiftyThrifty\ShopBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set basket
     *
     * @param NiftyThrifty\ShopBundle\Entity\Basket $basket
     * @return BasketItem
     */
    public function setBasket(\NiftyThrifty\ShopBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get basket
     *
     * @return NiftyThrifty\ShopBundle\Entity\Basket
     */
    public function getBasket()
    {
        return $this->basket;
    }



    /**
     * The basket item time remaining.
     */
    public function findBasketItemTimeRemaining()
    {
		$date_current = time();
		$date_end_val = $this->getBasketItemDateEnd()->format('Y-m-d H:i:s');
		$date_end = strtotime($date_end_val);

		$diff = $date_end-$date_current;
		$min = floor($diff/60);
		$sec = $diff-$min*60;
		return $min.':'.$sec;
    }


}
