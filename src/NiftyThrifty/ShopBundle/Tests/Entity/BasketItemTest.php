<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;
use NiftyThrifty\ShopBundle\Entity\BasketItem;

class BasketItemTest extends NiftyBaseTestCase
{

    /**
     * Verify the site-wide constants haven't changed
     */
    public function testBasketItemConstants()
    {
        $this->assertEquals(BasketItem::VALID,   'valid');
        $this->assertEquals(BasketItem::PAYMENT, 'payment');
    }

    /**
     * Test the prepersist function sets items correcty.
     */
    public function testPrePersistNulls()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new BasketData);
        $this->executeFixtures();

        $product = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->find(1);
        $basket  = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->find(2);

        $newBasketItem = new BasketItem();
        $newBasketItem->setBasket($basket);
        $newBasketItem->setProduct($product);
        $newBasketItem->setBasketItemPrice($product->getProductPrice());
        $newBasketItem->setBasketItemDiscount(0);
        $this->assertNull($newBasketItem->getBasketItemDateAdd());
        $this->assertNull($newBasketItem->getBasketItemDateEnd());
        $this->assertNull($newBasketItem->getBasketItemStatus());

        $this->em->persist($newBasketItem);
        $this->em->flush();

        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->find($newBasketItem->getBasketItemId());

        $this->assertEquals($newBasketItem->getProductId(), $basketItem->getProductId());
        $this->assertEquals($newBasketItem->getBasketId(), $basketItem->getBasketId());
        $this->assertEquals($basketItem->getBasketItemStatus(), BasketItem::VALID);
        $this->assertNotNull($newBasketItem->getBasketItemDateAdd());
        $this->assertEquals($basketItem->getBasketItemDateAdd(), $newBasketItem->getBasketItemDateAdd());
        $this->assertNotNull($newBasketItem->getBasketItemDateEnd());
        $this->assertEquals($basketItem->getBasketItemDateEnd(), $newBasketItem->getBasketItemDateEnd());
        $this->assertEquals($basketItem->getBasketItemDateAdd()->modify("+10 minutes"), $basketItem->getBasketItemDateEnd());
    }

    /**
     * Test the prepersist function skips resetting items that aren't null.
     */
    public function testPrePersistNoNulls()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new BasketData);
        $this->executeFixtures();

        $product = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->find(1);
        $basket  = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->find(2);

        $newBasketItem = new BasketItem();
        $newBasketItem->setBasket($basket);
        $newBasketItem->setProduct($product);
        $newBasketItem->setBasketItemPrice($product->getProductPrice());
        $newBasketItem->setBasketItemDiscount(0);
        $newBasketItem->setBasketItemStatus(BasketItem::VALID);
        $dateTime = new \DateTime();
        $newBasketItem->setBasketItemDateAdd($dateTime);
        $newBasketItem->setBasketItemDateEnd($dateTime);

        $this->em->persist($newBasketItem);
        $this->em->flush();

        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->find($newBasketItem->getBasketItemId());

        $this->assertEquals($newBasketItem->getProductId(), $basketItem->getProductId());
        $this->assertEquals($newBasketItem->getBasketId(), $basketItem->getBasketId());
        $this->assertEquals($basketItem->getBasketItemStatus(), BasketItem::VALID);
        $this->assertEquals($basketItem->getBasketItemDateAdd(), $dateTime);
        $this->assertEquals($basketItem->getBasketItemDateEnd(), $dateTime);
    }
}
