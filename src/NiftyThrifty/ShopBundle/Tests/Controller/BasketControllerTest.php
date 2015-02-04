<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;

class BasketControllerTest extends NiftyBaseTestCase
{
    /**
     * The basket count actions don't have routes, so these tests don't have their own route.  We 
     * load the home page and just test for the expected things.
     */
     
    /**
     * Load only the user data so there's no basket.  Ensure a basket was created as well as 
     * a zero count.
     * 
     * @covers BasketController::getBasketCountAction
     * @covers BasketController::_newBasket
     */
    public function testGetBasketCountActionLoggedInNoBasket()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertNull($basket);

        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        // There is no link, just a basket list item that has 0 items
        $this->assertEquals($crawler->filter('li#in_cart')->text(), '0 items');
        $this->assertEquals($crawler->filter('li#in_cart > span#items_in_cart')->text(), '0');
        $this->assertTrue($crawler->filter('li#in_cart > a')->count() == 0);
        
        $basket2 = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->findByUserOngoing(1);
        $this->assertNotNull($basket2);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket2);
    }

    /**
     * Load the basket with no items, verify zero count.
     * 
     * @covers BasketController::getBasketCountAction
     */
    public function testGetBasketCountActionLoggedInEmptyBasket()
    {
        $this->addFixture(new BasketData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertNotNull($basket);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        $this->assertCount(0, $basket->getBasketItems());
        
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('li#in_cart')->text(), '0 items');
        $this->assertEquals($crawler->filter('li#in_cart > span#items_in_cart')->text(), '0');
        $this->assertTrue($crawler->filter('li#in_cart > a')->count() == 0);
    }
    
    /**
     * Load a basket, check that one item works correctly
     *
     * @covers BasketController:getBasketCountAction
     */
    public function testGetBasketCountActionLoggedInOneItemBasket()
    {
        $this->addFixture(new BasketData);
        $this->addFixture(new ProductData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $product = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->find(1);

        $basketItem = new \NiftyThrifty\ShopBundle\Entity\BasketItem();
        $basketItem->setBasket($basket)
                   ->setProduct($product)
                   ->setBasketItemPrice(10)
                   ->setBasketItemDiscount(0);
        $this->em->persist($basketItem);
        $this->em->flush();
        
        $this->assertCount(1, $basket->getBasketItems());
        
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('li#in_cart')->text(), '1 item');
        $this->assertEquals($crawler->filter('span#items_in_cart')->text(), '1');
        $this->assertTrue($crawler->filter('li#in_cart > a')->count() == 1);
    }
    
    /**
     * Load a basket with items, check the count with more than one item.
     *
     * @covers BasketController::getBasketCountAction
     */
    public function testGetBasketCountActionLoggedInMoreThanOneItemBasket()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertNotNull($basket);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('li#in_cart')->text(), '3 items');
        $this->assertEquals($crawler->filter('span#items_in_cart')->text(), '3');
        $this->assertTrue($crawler->filter('li#in_cart > a')->count() == 1);
    }
    
    /**
     * If a user somehow gets here with no basket, a basket should be created and the empty
     * basket screen should be shown.
     *
     * @covers BasketController::showBasketAction
     * @covers BasketController::_newBasket
     */
    public function testShowBasketActionNoBasket()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertNull($basket);
        
        $crawler = $client->request('GET', '/basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue($crawler->filter('div#noBasketItems')->count() == 1);
        $this->assertTrue($crawler->filter('div#basketItemList')->count() == 0);
        $this->assertTrue($crawler->filter('div#basketCheckoutLink')->count() == 0);

        $basket2 = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->findByUserOngoing(1);
        $this->assertNotNull($basket2);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket2);
    }
    
    /**
     * If a user has no items in the basket, it should show the "no items" section of the template.
     *
     * @covers BasketController::showBasketAction
     */
    public function testShowBasketActionEmptyBasket()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        $this->assertCount(0, $basket->getBasketItems());
        
        $crawler = $client->request('GET', '/basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertTrue($crawler->filter('div#noBasketItems')->count() == 1);
        $this->assertTrue($crawler->filter('div#basketItemList')->count() == 0);
        $this->assertTrue($crawler->filter('div#basketCheckoutLink')->count() == 0);
    }
    
    /**
     * If a user has items, show them, and make sure the correct items were expired.
     *
     * @covers BasketController:showBasketAction
     */
    public function testShowBasketActionFullBasket()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        $this->assertGreaterThan(0, $basket->getBasketItems()->count());
        
        $crawler = $client->request('GET', '/basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($crawler->filter('div#noBasketItems')->count() == 0);

        $basketItemNodes = $crawler->filter('table#basket_table > tr.product');
        $this->assertCount(3, $basketItemNodes);

        $this->assertEquals($basketItemNodes->eq(0)->attr('id'), 'basket-item-2');
        $this->assertContains('Product Two',$crawler->filter('tr#basket-item-2 > td > div.description_name')->text());
        $this->assertContains('15.00',      $crawler->filter('tr#basket-item-2 > td > div.price_final')->text());
        $this->assertEquals($crawler->filter('tr#basket-item-2 > td > a')->eq(1)->attr('href'), '/basket/remove_item_from_basket/2');
        $this->assertEquals($crawler->filter('tr#basket-item-2 > td > a')->eq(0)->attr('href'), '/shop/show_item/product-two-2');

        $this->assertEquals($basketItemNodes->eq(1)->attr('id'), 'basket-item-1');
        $this->assertContains('Product One',$crawler->filter('tr#basket-item-1 > td > div.description_name')->text());
        $this->assertContains('10.00',      $crawler->filter('tr#basket-item-1 > td > div.price_final')->text());
        $this->assertEquals($crawler->filter('tr#basket-item-1 > td > a')->eq(1)->attr('href'), '/basket/remove_item_from_basket/1');
        $this->assertEquals($crawler->filter('tr#basket-item-1 > td > a')->eq(0)->attr('href'), '/shop/show_item/product-one-1');

        $this->assertEquals($basketItemNodes->eq(2)->attr('id'), 'basket-item-3');
        $this->assertContains('Product Three', $crawler->filter('tr#basket-item-3 > td > div.description_name')->text());
        $this->assertContains('12.00', $crawler->filter('tr#basket-item-3 > td > div.price_final')->text());
        $this->assertEquals($crawler->filter('tr#basket-item-3 > td > a')->eq(1)->attr('href'), '/basket/remove_item_from_basket/3');
        $this->assertEquals($crawler->filter('tr#basket-item-3 > td > a')->eq(0)->attr('href'), '/shop/show_item/product-three-3');

        $this->assertEquals(trim($crawler->filter('div#order_subtotal_value > span')->text()), '37.00');
    }

    /**
     * Add an item to a basket if the user has no basket.  It should create a basket for them and add
     * the item.
     *
     * Case covered: unloved item becomes loved
     * @covers BasketController:addToBasketAction
     */
    public function testAddToBasketNoBasket()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $this->assertNull($basket);
        $product = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->find(9);
        $this->assertEquals($product->getProductAvailability(), \NiftyThrifty\ShopBundle\Entity\Product::SALE);
        try {
            $loved = $this->em
                          ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                          ->findByUserAndProduct(1,9);
        } catch (\Doctrine\ORM\NoResultException $e) {}
        $this->assertInstanceOf('\Doctrine\ORM\NoResultException', $e);

        $crawler = $client->request('GET', '/basket/add_item_to_basket/9');
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->em->clear();

        $basket2 = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->findByUserOngoing(1);
        $product2 = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->find(9);
        $loved    = $this->em
                         ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                         ->findByUserAndProduct(1,9);

        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket2);
        $this->assertCount(1, $basket2->getBasketItems());
        $this->assertEquals($product2->getProductAvailability(), \NiftyThrifty\ShopBundle\Entity\Product::RESERVED);
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket2, $product2->getProductId());
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\BasketItem', $basketItem);
        $this->assertEquals($basketItem->getBasketItemStatus(), \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getProductId(), 9);
        $this->assertEquals($loved->getUserId(),    1);
        $this->assertEquals($loved->getLoveType(),  'basket');
    }

    /**
     * Test adding an item to an existing basket
     *
     * Case covered: A loved item that has been deleted becomes loved again.
     * @covers BasketCollection:addItemToBasket
     */
    public function testAddToBasketWithBasket()
    {
        $this->addFixture(new BasketData);
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        // Prechecks
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $product = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->find(1);
        $product->setProductAvailability(\NiftyThrifty\ShopBundle\Entity\Product::SALE);
        $this->em->flush();
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket, $product->getProductId());
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(1,1);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        $this->assertNull($basketItem);
        $this->assertCount(0, $basket->getBasketItems());
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getIsDeleted(), 1);

        // Requests
        $crawler = $client->request('GET', '/basket/add_item_to_basket/1');
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->em->clear();

        // Postchecks
        $basket2 = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->findByUserOngoing(1);
        $product2 = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->find(1);
        $basketItem2 = $this->em
                            ->getRepository('NiftyThriftyShopBundle:BasketItem')
                            ->findByBasketAndProduct($this->em, $basket2, $product2->getProductId());
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(1,1);
        $this->assertCount(1, $basket2->getBasketItems());
        $this->assertEquals($product2->getProductAvailability(), \NiftyThrifty\ShopBundle\Entity\Product::RESERVED);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\BasketItem', $basketItem2);
        $this->assertEquals($basketItem2->getBasketItemStatus(), \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID);
        $this->assertEquals($loved->getIsDeleted(), 0);
    }

    /**
     * Test adding an item to a basket with 'link' love type is changed to a 'basket' love type.
     */
    public function testAddToBasketLoveTypeOverride()
    {
        $this->addFixture(new BasketData);
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        // Prechecks
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(1,2);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getLoveType(), 'link');

        // Requests
        $crawler = $client->request('GET', '/basket/add_item_to_basket/2');
        $this->em->clear();

        // Postchecks
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(1,2);
        $this->assertEquals($loved->getLoveType(), 'basket');
    }

    /**
     * Adding an item reserved by another user should fail, but the item should still get loved.
     *
     * @covers BasketItemController:addItemToBasket
     */
    public function testAddToBasketReservedProduct()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Prechecks
        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->findByUserOngoing(1);
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket, 1);
        try {
            $loved = $this->em
                          ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                          ->findByUserAndProduct(2,1);
        } catch (\Doctrine\ORM\NoResultException $e) {}
        $this->assertInstanceOf('\Doctrine\ORM\NoResultException', $e);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        $this->assertEquals($basketItem->getProduct()->getProductAvailability(), \NiftyThrifty\ShopBundle\Entity\Product::RESERVED);
        $this->assertEquals($basketItem->getBasketItemStatus(), \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID);

        // Have the admin user try to add this item to the basket.
        $crawler = $client->request('GET', '/basket/add_item_to_basket/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains($crawler->text(), 'Product can not be reserved.');
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(2,1);
        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getProductId(), 1);
        $this->assertEquals($loved->getUserId(),    2);
        $this->assertEquals($loved->getLoveType(),  'basket');
    }

    /**
     * Removing an item from a basket if it's not in the basket.  Nothing should happen, just redirect.
     *
     * @covers BasketController:removeFromBasketAction
     */
    public function testRemoveFromBasketNotInBasket()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/basket/remove_item_from_basket/10');
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    /**
     * Test removing an item from a basket
     *
     * @covers BasketController:removeFromBasketAction
     */
    public function testRemoveFromBasketInBasket()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $basket = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Basket')
                       ->find(2);
        $basketItem = $this->em
                           ->getRepository('NiftyThriftyShopBundle:BasketItem')
                           ->findByBasketAndProduct($this->em, $basket, 1);

        $this->assertInstanceOf('NiftyThrifty\ShopBundle\Entity\BasketItem', $basketItem);
        $this->assertEquals($basketItem->getBasketItemId(), 1);
        $this->assertEquals($basket->getBasketId(), 2);
        $this->assertEquals($basketItem->getBasket()->getBasketId(), $basket->getBasketId());
        $this->assertEquals($basketItem->getBasketItemStatus(), \NiftyThrifty\ShopBundle\Entity\BasketItem::VALID);
        $this->assertEquals($basketItem->getProduct()->getProductAvailability(), \NiftyThrifty\ShopBundle\Entity\Product::RESERVED);
        $oldDate = $basket->getBasketDateUpdate();

        $crawler = $client->request('GET', '/basket/remove_item_from_basket/1');
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->em->clear();

        // Test that the item is no longer returned by basket/product.
        $testRemovedItem = $this->em
                                ->getRepository('NiftyThriftyShopBundle:BasketItem')
                                ->findByBasketAndProduct($this->em, $basket, 1);
        $this->assertNull($testRemovedItem);
        $basket2 = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Basket')
                        ->find(2);
        $basketItem2 = $this->em
                            ->getRepository('NiftyThriftyShopBundle:BasketItem')
                            ->findByBasketAndProduct($this->em, $basket2, 1);
        $basketItem2a = $this->em
                             ->getRepository('NiftyThriftyShopBundle:BasketItem')
                             ->find(1);
        $this->assertNull($basketItem2);
        $this->assertNull($basketItem2a);
    }
}
