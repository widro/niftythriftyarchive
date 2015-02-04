<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityNotFoundException;
use NiftyThrifty\ShopBundle\Entity\BasketItem;

/**
 * NiftyThrifty\ShopBundle\Entity\Basket
 *
 * @ORM\Table(name="basket")
 * @ORM\Entity
 */
class Basket
{
    /**
     * @var integer $basketId
     *
     * @ORM\Column(name="basket_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $basketId;

    /**
     * @var \DateTime $basketDateCreation
     *
     * @ORM\Column(name="basket_date_creation", type="datetime", nullable=false)
     */
    private $basketDateCreation;

    /**
     * @var \DateTime $basketDateUpdate
     *
     * @ORM\Column(name="basket_date_update", type="datetime", nullable=false)
     */
    private $basketDateUpdate;

    /**
     * @var string $basketStatus
     *
     * @ORM\Column(name="basket_status", type="string", nullable=false)
     */
    private $basketStatus;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var NiftyThrifty\ShopBundle\Entity\User
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $basketItems;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $products;
    
    /**
     * Enum basket.basket_status legal values
     */
    const ONGOING   = 'ongoing';
    const PURCHASED = 'purchased';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->basketItems = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Before inserting a new basket, set the basket creation time.
     *
     * @prePersist()
     */
    public function setCreationTime()
    {
        if (!$this->basketDateCreation) $this->basketDateCreation   = new \DateTime("now");
        if (!$this->basketDateUpdate)   $this->basketDateUpdate     = new \DateTime("now");
        if (!$this->basketStatus)       $this->basketStatus         = self::ONGOING;
    }
    
    /**
     * Before updating a basket, set the last modified time to now.
     *
     * @preUpdate()
     */
    public function setLastUpdateTime()
    {
        $this->basketDateUpdate = new \DateTime("now");
    }

    /**
     * Step through a basket's items and expire the ones that have been expired.
     *
     * Update 10/14/2013: Instead of expiring items and keeping them in the table forever, delete
     *      the item from the table.  It is persisted in user_loved_products
     */
    public function expireItems($em)
    {
        foreach ($this->getBasketItems() as $basketItem) {
            $nowTime = new \DateTime("now");

            /**
             * If the basket item is expired and the status is valid, expire the item.  If the
             * item is in payment or deleted, don't expire it.
             */
            if ($basketItem->getBasketItemDateEnd() < $nowTime) {
                if ($basketItem->getBasketItemStatus() == \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID) {
                    /**
                     * It shouldn't happen that an item being expired has a product id that doesn't exist, but if
                     * it does, we don't need to explode.  Just set the state to deleted instead of expired.
                     */
                    try {
                        //$basketItem->getProduct()->setProductAvailability(Product::SALE);
                        $product = $basketItem->getProduct();
                        $product->setProductAvailability(Product::SALE);
                        $em->persist($product);
                        $em->remove($basketItem);
                    } catch (EntityNotFoundException $e) {
                        $em->remove($basketItem);
                    }
                }
            }
        }
        $em->flush();
    }
    
    /**
     * Step through the items array and return the total.
     *
     * @return integer
     */
    public function getBasketItemTotal()
    {
        $total = 0;
        foreach ($this->getBasketItems() as $item) {
            if ($item->getBasketItemStatus() == BasketItem::VALID) {
                $total = $total + $item->getBasketItemPrice();
            }
        }
        return $total;
    }

    /**
     * As a backup, the order saves the list of items that it has, so we want to generate that here in a specific format.
     *
     * @return pipe-separated values
     */
    public function getOrderProductList()
    {
        $itemDetails = array();
        foreach ($this->basketItems as $item) {
            if ($item->getBasketItemStatus() == \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID) {
                $itemDetails[] = $item->getProduct()->getProductId()
                                    . ' | ' . $item->getProduct()->getProductName()
                                    . ' | ' . $item->getProduct()->getProductPrice();
            }
        }
        return implode(" <br> ", $itemDetails);
    }

    /**
     * The current sales tax rule is only calculate sales tax if an item is over $110.  If it's not being
     * delivered to an address in New York, tax is not calculated.  That should be checked by the calling 
     * controller as State is not a property of the basket.
     */
    public function calculateSalesTax()
    {
        $value = 0.0;

        foreach ($this->basketItems as $item) {
            if (($item->getBasketItemStatus() == \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID) && ($item->getProduct()->getProductPrice() >= 110)) {
                $value = $value + ($item->getProduct()->getProductPrice() * 0.08875);
            }
        }
        
        return round($value, 2);
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
     * Set basketDateCreation
     *
     * @param \DateTime $basketDateCreation
     * @return Basket
     */
    public function setBasketDateCreation($basketDateCreation)
    {
        $this->basketDateCreation = $basketDateCreation;
    
        return $this;
    }

    /**
     * Get basketDateCreation
     *
     * @return \DateTime 
     */
    public function getBasketDateCreation()
    {
        return $this->basketDateCreation;
    }

    /**
     * Set basketDateUpdate
     *
     * @param \DateTime $basketDateUpdate
     * @return Basket
     */
    public function setBasketDateUpdate($basketDateUpdate)
    {
        $this->basketDateUpdate = $basketDateUpdate;
    
        return $this;
    }

    /**
     * Get basketDateUpdate
     *
     * @return \DateTime 
     */
    public function getBasketDateUpdate()
    {
        return $this->basketDateUpdate;
    }

    /**
     * Set basketStatus
     *
     * @param string $basketStatus
     * @return Basket
     */
    public function setBasketStatus($basketStatus)
    {
        $this->basketStatus = $basketStatus;
    
        return $this;
    }

    /**
     * Get basketStatus
     *
     * @return string 
     */
    public function getBasketStatus()
    {
        return $this->basketStatus;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Basket
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
    
    /**
     * Set user
     *
     * @param NiftyThrifty\ShopBundle\Entity\User $user
     * @return Basket
     */
    public function setUser(\NiftyThrifty\ShopBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return NiftyThrifty\ShopBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add basketItem
     *
     * @param NiftyThrifty\ShopBundle\Entity\BasketItem $basketItem
     * @return Basket
     */
    public function addBasketItem(\NiftyThrifty\ShopBundle\Entity\BasketItem $basketItem)
    {
        $this->basketItem[] = $basketItem;
    
        return $this;
    }

    /**
     * Remove basketItem
     *
     * @param NiftyThrifty\ShopBundle\Entity\BasketItem $basketItem
     */
    public function removeBasketItem(\NiftyThrifty\ShopBundle\Entity\BasketItem $basketItem)
    {
        $this->basketItem->removeElement($basketItem);
    }

    /**
     * Get basketItems
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBasketItems()
    {
        return $this->basketItems;
    }

    /**
     * Add products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     * @return Basket
     */
    public function addProduct(\NiftyThrifty\ShopBundle\Entity\Product $products)
    {
        $this->products[] = $products;
    
        return $this;
    }

    /**
     * Remove products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     */
    public function removeProduct(\NiftyThrifty\ShopBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
}
