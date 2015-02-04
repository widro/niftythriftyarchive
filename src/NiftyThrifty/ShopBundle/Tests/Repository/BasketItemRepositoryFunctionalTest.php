<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;

class BasketItemRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * Count the basket items in a user's basket
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository::getItemCountByBasket
     */
    public function testGetItemCountByBasketEmpty()
    {
        // Don't want any items, so just load the Basket fixture.
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);

        $itemCount = $this->em
                          ->getRepository('NiftyThriftyShopBundle:BasketItem')
                          ->getItemCountByBasket($basket, $this->em);
        $this->assertEquals($itemCount, 0);

    }

    /**
     * count the basket items in a user's basket when there are actual items.
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository:getItemCountByBasket
     */
    public function testGetItemCountByBasket()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);

        $itemCount = $this->em
                          ->getRepository('NiftyThriftyShopBundle:BasketItem')
                          ->getItemCountByBasket($basket, $this->em);
        $this->assertEquals($itemCount, 3);
    }

    /**
     * Test an empty basket
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository:findByBasket
     */
    public function testFindByBasketEmpty()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);

        $basketItems = $this->em
                            ->getRepository('NiftyThriftyShopBundle:BasketItem')
                            ->findByBasket($basket, $this->em);

        $this->assertCount(0, $basketItems);
    }

    /**
     * Test finding items in a basket
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository:findByBasket
     */
    public function testFindByBasket()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);

        $basketItems = $this->em
                            ->getRepository('NiftyThriftyShopBundle:BasketItem')
                            ->findByBasket($basket, $this->em);

        $this->assertCount(3, $basketItems);
        $this->assertEquals($basketItems[0]->getBasketItemId(), 2);
        $this->assertEquals($basketItems[1]->getBasketItemId(), 1);
        $this->assertEquals($basketItems[2]->getBasketItemId(), 3);
    }

    /**
     * Test finding a basket item that doesn't exist.
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository::findByBasketAndProduct
     */
    public function testFindByBasketAndProductNull()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket, 10);
        $this->assertNull($basketItem);
    }

    /**
     * Test find a basket item that that should be expired first
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository::findByBasketAndProduct
     */
    public function testFindByBasketAndProductExpiredItem()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->find(4);
        $nowTime = new \DateTime();
        $this->assertGreaterThan($basketItem->getBasketItemDateEnd(), $nowTime);
        $this->assertEquals($basketItem->getBasketItemStatus(), \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID);
        $this->assertEquals($basketItem->getProduct()->getProductAvailability(), \NiftyThrifty\ShopBundle\Entity\Product::RESERVED);

        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket, 4);
        $this->assertNull($basketItem);

        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->find(4);
        $this->assertNull($basketItem);
    }

    /**
     * Test finding a basket item
     *
     * @group Repository
     * @group BasketItem
     * @covers BasketItemRepository::findByBasketAndProduct
     */
    public function testFindByBasketAndProductFindItem()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket, 1);
        $this->assertEquals($basketItem->getBasketItemId(), 1);
    }
}
