<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Entity\Basket;
use NiftyThrifty\ShopBundle\Entity\Product;
use NiftyThrifty\ShopBundle\Entity\BasketItem;

class BasketTest extends NiftyBaseTestCase
{
    /**
     * Verify the expected constants haven't changed
     *
     * @group Basket
     */
    public function testBasketConstants()
    {
        $this->assertEquals(Basket::ONGOING,    'ongoing');
        $this->assertEquals(Basket::PURCHASED,  'purchased');
    }

    /**
     * Test prePersist with null values are set correctly
     *
     * @group Basket
     * @covers Basket::prePersist
     */
    public function testPrePersistSetCreationTimeWithNulls()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();

        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);

        $newBasket = new Basket();
        $newBasket->setUser($user);
        $this->assertNull($newBasket->getBasketStatus());
        $this->assertNull($newBasket->getBasketDateCreation());
        $this->assertNull($newBasket->getBasketDateUpdate());

        $this->em->persist($newBasket);
        $this->em->flush();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find($newBasket->getBasketId());

        $this->assertEquals($newBasket->getUserId(), $basket->getUserId());
        $this->assertEquals($basket->getBasketStatus(), Basket::ONGOING);
        $this->assertEquals($basket->getBasketStatus(), $newBasket->getBasketStatus());
        $this->assertNotNull($newBasket->getBasketDateCreation());
        $this->assertEquals($basket->getBasketDateCreation(), $newBasket->getBasketDateCreation());
        $this->assertNotNull($newBasket->getBasketDateUpdate());
        $this->assertEquals($basket->getBasketDateUpdate(), $newBasket->getBasketDateUpdate());
    }

    /**
     * Test prePersist ignores the three defaults when they are set
     *
     * @group Basket
     * @covers Basket::prePersist
     */
    public function testPrePersistSetCreationTimeWithoutNulls()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();

        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);

        $newBasket = new Basket();
        $newBasket->setUser($user);
        $nowDate = new \DateTime();
        $newBasket->setBasketStatus(Basket::PURCHASED);
        $newBasket->setBasketDateCreation($nowDate);
        $newBasket->setBasketDateUpdate($nowDate);
        $this->em->persist($newBasket);
        $this->em->flush();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find($newBasket->getBasketId());

        $this->assertEquals($newBasket->getUserId(), $basket->getUserId());
        $this->assertEquals($basket->getBasketStatus(), Basket::PURCHASED);
        $this->assertEquals($basket->getBasketDateCreation(), $nowDate);
        $this->assertEquals($basket->getBasketDateUpdate(), $nowDate);
    }

    /**
     * Test the baskets pre update function
     *
     * @group Basket
     * @covers Basket:preUpdate
     */
    public function testPreUpdateSetLastUpdateTime()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $oldDate = $basket->getBasketDateUpdate();
        $this->assertEquals($basket->getBasketStatus(), Basket::ONGOING);
        $basket->setBasketStatus(Basket::PURCHASED);
        $this->em->flush();
        $updatedBasket = $this->em
                              ->getRepository('NiftyThriftyShopBundle:Basket')
                              ->find(2);

        $this->assertGreaterThan($oldDate, $updatedBasket->getBasketDateUpdate());
        $this->assertEquals($basket->getBasketDateUpdate(), $updatedBasket->getBasketDateUpdate());
    }

    /**
     * Test the expire items function.
     *
     * @group Basket
     * @covers Basket::expireItems
     */
    public function testExpireItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $nowTime = new \DateTime();
        $basketItems = $basket->getBasketItems();
        $this->assertCount(4, $basketItems);

        // Check the pre-expired stuff
        $this->assertEquals($basketItems[0]->getBasketItemStatus(), BasketItem::VALID);
        $this->assertEquals($basketItems[1]->getBasketItemStatus(), BasketItem::VALID);
        $this->assertEquals($basketItems[2]->getBasketItemStatus(), BasketItem::VALID);
        $this->assertEquals($basketItems[3]->getBasketItemStatus(), BasketItem::VALID);

        $basket->expireItems($this->em);

        $updBasket = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Basket')
                          ->find(2);
        $updBasketItems = $updBasket->getBasketItems();
        $this->assertCount(3, $basketItems);

        // Verify the proper item only was expired
        $this->assertEquals($updBasketItems[0]->getBasketItemStatus(), BasketItem::VALID);
        $this->assertEquals($updBasketItems[1]->getBasketItemStatus(), BasketItem::VALID);
        $this->assertEquals($updBasketItems[2]->getBasketItemStatus(), BasketItem::VALID);
    }
    
    public function testGetBasketItemTotalEmpty()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $this->assertEquals($basket->getBasketItemTotal(), 0);
    }
    
    public function testGetBasketItemTotal()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $this->assertEquals($basket->getBasketItemTotal(), 47);
    }
    
    public function testGetProductListBlank()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $this->assertEmpty($basket->getOrderProductList());
    }
    
    public function testGetProductList()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);

        $expected = '1 | Product One | 10 <br> '
                        . '2 | Product Two | 15 <br> '
                        . '3 | Product Three | 12 <br> ' 
                        . '4 | Product Four | 17';
        $this->assertEquals($expected, $basket->getOrderProductList());
    }
    
    public function testCalculateSalesTaxNoTax()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $this->assertEquals(0, $basket->calculateSalesTax());
    }

    public function testCalculateSalesTaxOneItem()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();

        // Need to increase one item over the tax threshold
        $product= $this->em
                       ->getRepository('NiftyThriftyShopBundle:Product')
                       ->find(1);
        $product->setProductPrice(200);
        $this->em->flush();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $this->assertEquals(17.75, $basket->calculateSalesTax());
    }

    public function testCalculateSalesTaxTwoItems()
    {        
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        // Need to increase two items over the tax threshold
        $product    = $this->em
                           ->getRepository('NiftyThriftyShopBundle:Product')
                           ->find(1);
        $product->setProductPrice(200);
        $product2   = $this->em
                           ->getRepository('NiftyThriftyShopBundle:Product')
                           ->find(2);
        $product2->setProductPrice(150);
        $this->em->flush();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $this->assertEquals(31.06, $basket->calculateSalesTax());
    }

}
