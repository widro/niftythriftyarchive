<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserViewedProductData;

class ShopControllerTest extends NiftyBaseTestCase
{
    /**
     * Show categories with results.
     */
    public function testShowCategoryItemsResults()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/category/jumpers-1');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(4, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 4', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Jumpers');
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $products->eq(0)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->text(), '$46');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(0, $products->eq(0)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(0)->filter('div.sold'));

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-three-3');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Three');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$12');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(1)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(1)->filter('div.sold'));

        $this->assertEquals($products->eq(2)->filter('div.img > a')->attr('href'), '/shop/show_item/product-two-2');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.product_name')->text(), 'Product Two');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(2)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(2)->filter('div.infos > div.price')->text(), '$15');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.size > div.size_value')->text(), 'M');
        $this->assertCount(1, $products->eq(2)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(2)->filter('div.sold'));

        $this->assertEquals($products->eq(3)->filter('div.img > a')->attr('href'), '/shop/show_item/product-one-1');
        $this->assertEquals($products->eq(3)->filter('div.infos > div.product_name')->text(), 'Product One');
        $this->assertEquals(trim($products->eq(3)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertCount(0, $products->eq(3)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(3)->filter('div.infos > div.price')->text(), '$10');
        $this->assertEquals($products->eq(3)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(3)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(3)->filter('div.sold'));
    }

    /**
     * Show category with no results.
     */
    public function testShowCategoryItemsNoResults()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/category/shoes-5');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Tops');
    }

    /**
     * Show category that doesn't exist.
     */
    public function testShowCategoryBadCategory()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/category/dinkers-1234');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Category was not found');
    }

    /**
     * Show items by size.
     */
    public function testShowSizeWithResults()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/size/jumpers-s-1');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(3, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 3', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Jumper - S');
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $products->eq(0)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->text(), '$46');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(0, $products->eq(0)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(0)->filter('div.sold'));

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-one-1');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product One');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$10');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(1)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(1)->filter('div.sold'));

        $this->assertEquals($products->eq(2)->filter('div.img > a')->attr('href'), '/shop/show_item/product-three-3');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.product_name')->text(), 'Product Three');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(2)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(2)->filter('div.infos > div.price')->text(), '$12');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(2)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(2)->filter('div.sold'));
    }

    /**
     * Show by size with no results.
     */
    public function testShowSizeNoResults()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/size/something-7');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Shoes - 8');
    }

    public function testShowSizeBadSize()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/size/dinkers-1234');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Size was not found');
    }

    /**
     * Showing list of items by designer with items.
     *
     * @covers ShopController:showDesignerItems
     */
    public function testShowDesignerItemsWithResults()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/designer/coach-1');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(3, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 3', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Prada');
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-four-4');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Four');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.old')->text(), '$25');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->eq(1)->text(), '$17');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), '8');
        $this->assertCount(0, $products->eq(0)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(0)->filter('div.sold'));

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-three-3');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Three');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$12');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(1)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(1)->filter('div.sold'));

        $this->assertEquals($products->eq(2)->filter('div.img > a')->attr('href'), '/shop/show_item/product-two-2');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.product_name')->text(), 'Product Two');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(2)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(2)->filter('div.infos > div.price')->text(), '$15');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.size > div.size_value')->text(), 'M');
        $this->assertCount(1, $products->eq(2)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(2)->filter('div.sold'));
    }

    /**
    }

    /**
     * Showing list of items by designer no results
     *
     * @covers ShopController:showDesignerItems
     */
    public function testShowDesignerItemsNoResults()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/designer/starter-3');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Starter');
    }

    /**
     * Showing list of items by designer bad url.
     *
     * @covers ShopController:showDesignerItems
     */
    public function testShowDesignerItemsBadDesigner()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/designer/dinkers-1234');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Designer was not found');
    }

    /**
     * Showing a list of items by collection.
     *
     * @covers ShopController:showCollectionItems
     */
    public function testShowCollectionItemsWithItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/collection/active-not-ending-soon-1');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(6, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 6', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Active Not Ending Soon One');
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $products->eq(0)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->text(), '$46');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(0, $products->eq(0)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(0)->filter('div.sold'));

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-five-5');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Five');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$20');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        // Five is loaded reserved but time expired, so it should become sale 
        $this->assertCount(0, $products->eq(1)->filter('div.reserved'));
        $this->assertCount(1, $products->eq(1)->filter('div.sold'));

        $this->assertEquals($products->eq(2)->filter('div.img > a')->attr('href'), '/shop/show_item/product-one-1');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.product_name')->text(), 'Product One');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertCount(0, $products->eq(2)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(2)->filter('div.infos > div.price')->text(), '$10');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(2)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(2)->filter('div.sold'));

        $this->assertEquals($products->eq(3)->filter('div.img > a')->attr('href'), '/shop/show_item/product-seven-7');
        $this->assertEquals($products->eq(3)->filter('div.infos > div.product_name')->text(), 'Product Seven');
        $this->assertEquals(trim($products->eq(3)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(3)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(3)->filter('div.infos > div.price')->text(), '$35');
        $this->assertEquals($products->eq(3)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(0, $products->eq(3)->filter('div.reserved'));
        $this->assertCount(1, $products->eq(3)->filter('div.sold'));

        $this->assertEquals($products->eq(4)->filter('div.img > a')->attr('href'), '/shop/show_item/product-six-6');
        $this->assertEquals($products->eq(4)->filter('div.infos > div.product_name')->text(), 'Product Six');
        $this->assertEquals(trim($products->eq(4)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertEquals($products->eq(4)->filter('div.infos > div.old')->text(), '$20');
        $this->assertEquals($products->eq(4)->filter('div.infos > div.price')->eq(1)->text(), '$12');
        $this->assertEquals($products->eq(4)->filter('div.infos > div.size > div.size_value')->text(), '8');
        $this->assertCount(0, $products->eq(4)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(4)->filter('div.sold'));
        
        $this->assertEquals($products->eq(5)->filter('div.img > a')->attr('href'), '/shop/show_item/product-three-3');
        $this->assertEquals($products->eq(5)->filter('div.infos > div.product_name')->text(), 'Product Three');
        $this->assertEquals(trim($products->eq(5)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(5)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(5)->filter('div.infos > div.price')->text(), '$12');
        $this->assertEquals($products->eq(5)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(5)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(5)->filter('div.sold'));
    }

    /**
     * Showing a no items by collection
     *
     * @covers ShopController:showCollectionItems
     */
    public function testShowCollectionItemsNoItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/collection/active-ending-soon-4');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Active Ending Soon One');
    }

    /**
     * Show bad collection
     *
     * @covers ShopController:showCollectionItems
     */
    public function testShowCollectionItemsBadCollection()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/collection/dinkers-1234');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Collection was not found.');
    }

    /**
     * Show all the items that have a certain tag.
     *
     * @covers ShopController:showTagItems
     */
    public function testShowTagItemsWithItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_tag/look-classic-8');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(2, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 2', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Look Classic');
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-four-4');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Four');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.old')->text(), '$25');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->eq(1)->text(), '$17');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), '8');
        $this->assertCount(0, $products->eq(0)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(0)->filter('div.sold'));

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-two-2');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Two');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$15');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'M');
        $this->assertCount(1, $products->eq(1)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(1)->filter('div.sold'));
    }

    /**
     * Show an empty tag.
     *
     * @covers ShopController:showTagItems
     */
    public function testShowTagItemsNoItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_tag/red-1');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'red');
    }

    /**
     * Show an invalid tag
     *
     * @covers ShopController:showTagItems
     */
    public function testShowTagInvalidTag()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_tag/dinkers-1234');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Tag was not found');
    }

    /**
     * Show a single item
     *
     * @covers ShopController:showSingleItem
     */
    public function testShowSingleItemFound()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        try { 
            $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(1,4);
        } catch (\Doctrine\ORM\NoResultException $e) {}
        $this->assertInstanceOf('\Doctrine\ORM\NoResultException', $e);

        $crawler = $client->request('GET', '/shop/show_item/product-four-4');
        $this->assertEquals($crawler->filter('div#product_name')->text(), 'Product Four');
        $this->assertEquals($crawler->filter('div#product_designer')->text(), 'Prada');
        $this->assertEquals($crawler->filter('div#size_value')->text(), '8');
        
        $this->em->clear();
        $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(1,4);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserViewedProduct', $viewed);
        $this->assertEquals($viewed->getUserId(),   1);
        $this->assertEquals($viewed->getProductId(),4);
    }
    
    /**
     * Show a single item if the viewed record already exists.
     */
    public function testShowSingleItemDuplicateViewed()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new UserViewedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(1,1);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserViewedProduct', $viewed);
        $this->assertEquals($viewed->getUserId(),   1);
        $this->assertEquals($viewed->getProductId(),1);

        $crawler = $client->request('GET', '/shop/show_item/product-one-1');
        $this->assertEquals($crawler->filter('div#product_name')->text(), 'Product One');
        $this->em->clear();

        $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(1,1);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserViewedProduct', $viewed);
        $this->assertEquals($viewed->getUserId(),   1);
        $this->assertEquals($viewed->getProductId(),1);
    }

    /**
     * Show single item not found
     *
     * @covers ShopController:showSingleItem
     */
    public function testShowSingleItemNotFound()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_item/dinkers-1234');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Showing a list of items by collection.
     *
     * @covers ShopController:showSingleCollection
     */
    public function testShowSingleCollectionWithItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_collection/active-not-ending-soon-1');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(6, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 6', $crawler->text());
        $this->assertCount(1, $crawler->filter('div#collection_categories'));
        $this->assertCount(1, $crawler->filter('div#collection_category_sizes'));

        $this->assertCount(6, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 6', $crawler->text());
        $this->assertEquals($crawler->filter('title')->text(), 'Nifty Thrifty - Rare Finds Everyday');
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $products->eq(0)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->text(), '$46');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(0, $products->eq(0)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(0)->filter('div.sold'));

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-seven-7');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Seven');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$35');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(0, $products->eq(1)->filter('div.reserved'));
        $this->assertCount(1, $products->eq(1)->filter('div.sold'));

        $this->assertEquals($products->eq(2)->filter('div.img > a')->attr('href'), '/shop/show_item/product-six-6');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.product_name')->text(), 'Product Six');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.old')->text(), '$20');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.price')->eq(1)->text(), '$12');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.size > div.size_value')->text(), '8');
        $this->assertCount(0, $products->eq(2)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(2)->filter('div.sold'));

        $this->assertEquals($products->eq(3)->filter('div.img > a')->attr('href'), '/shop/show_item/product-five-5');
        $this->assertEquals($products->eq(3)->filter('div.infos > div.product_name')->text(), 'Product Five');
        $this->assertEquals(trim($products->eq(3)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(3)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(3)->filter('div.infos > div.price')->text(), '$20');
        $this->assertEquals($products->eq(3)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        // Five is loaded reserved but time expired, so it should become sale 
        $this->assertCount(0, $products->eq(3)->filter('div.reserved'));
        $this->assertCount(1, $products->eq(3)->filter('div.sold'));

        $this->assertEquals($products->eq(4)->filter('div.img > a')->attr('href'), '/shop/show_item/product-three-3');
        $this->assertEquals($products->eq(4)->filter('div.infos > div.product_name')->text(), 'Product Three');
        $this->assertEquals(trim($products->eq(4)->filter('div.infos > div.product_designer')->text()), 'Prada');
        $this->assertCount(0, $products->eq(4)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(4)->filter('div.infos > div.price')->text(), '$12');
        $this->assertEquals($products->eq(4)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(4)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(4)->filter('div.sold'));

        $this->assertEquals($products->eq(5)->filter('div.img > a')->attr('href'), '/shop/show_item/product-one-1');
        $this->assertEquals($products->eq(5)->filter('div.infos > div.product_name')->text(), 'Product One');
        $this->assertEquals(trim($products->eq(5)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertCount(0, $products->eq(5)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(5)->filter('div.infos > div.price')->text(), '$10');
        $this->assertEquals($products->eq(5)->filter('div.infos > div.size > div.size_value')->text(), 'S');
        $this->assertCount(1, $products->eq(5)->filter('div.reserved'));
        $this->assertCount(0, $products->eq(5)->filter('div.sold'));
    }

    /**
     * Showing a no items by collection
     *
     * @covers ShopController:showSingleCollection
     */
    public function testShowSingleCollectionNoItems()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_collection/active-ending-soon-4');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 0', $crawler->text());

        // There is one child due to the the "clear" div
        $this->assertCount(1, $crawler->filter('div#collection_categories'));
        $this->assertCount(1, $crawler->filter('div#collection_category_sizes'));
    }

    /**
     * Show bad collection
     *
     * @covers ShopController:showSingleCollection
     */
    public function testShowSingleCollectionBadCollection()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/show_collection/dinkers-1234');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    /**
     * Test the lookbook splash page
     */
    public function testShowLookbookSplash()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/lookbook');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(3, $crawler->filter('div.current_cell_lookbook'));
        $collections = $crawler->filter('div#lookbook_collections')->children();

        // 3 collections + 1 clear div
        $this->assertCount(4, $collections);

        $this->assertEquals($collections->eq(0)->filter('a')->attr('href'),                  '/shop/show_collection/active-not-ending-soon-three-3');
        $this->assertEquals($collections->eq(0)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-not-ending-soon-three-3');
        $this->assertEquals($collections->eq(0)->filter('div.current_cell_bar > p')->text(), 'Summer collection');

        $this->assertEquals($collections->eq(1)->filter('a')->attr('href'),                  '/shop/show_collection/active-not-ending-soon-two-2');
        $this->assertEquals($collections->eq(1)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-not-ending-soon-two-2');
        $this->assertEquals($collections->eq(1)->filter('div.current_cell_bar > p')->text(), 'Fall collection');

        $this->assertEquals($collections->eq(2)->filter('a')->attr('href'),                  '/shop/show_collection/active-not-ending-soon-one-1');
        $this->assertEquals($collections->eq(2)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-not-ending-soon-one-1');
        $this->assertEquals($collections->eq(2)->filter('div.current_cell_bar > p')->text(), 'Winter collection');
    }

    /**
     * Test shops splash page.
     */
    public function testShowShopSplash()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/shops');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(trim($crawler->filter('div.splash_header')->text()), 'Welcome to the Shops');

        // All categories should be in the vintage staples section.
        $categories = $crawler->filter('div.shop_left_cell');
        $this->assertCount(4, $categories);
        $this->assertEquals($categories->eq(0)->filter('a')->text(), 'Dresses');
        $this->assertEquals($categories->eq(1)->filter('a')->text(), 'Jumpers');
        $this->assertEquals($categories->eq(2)->filter('a')->text(), 'Rompers');
        $this->assertEquals($categories->eq(3)->filter('a')->text(), 'Shoes');

        // Two collections are shops
        $shops = $crawler->filter('div.current_cell');
        $this->assertCount(2, $shops);

        $this->assertEquals($shops->eq(0)->filter('a')->attr('href'),                        '/shop/show_collection/vintage-staples-12');
        $this->assertEquals($shops->eq(0)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/vintage-staples-12');
        $this->assertEquals(trim($shops->eq(0)->filter('div.current_cell_bar > a')->text()), 'Vintage Staples');

        $this->assertEquals($shops->eq(1)->filter('a')->attr('href'),                        '/shop/show_collection/featured-shop-13');
        $this->assertEquals($shops->eq(1)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/featured-shop-13');
        $this->assertEquals(trim($shops->eq(1)->filter('div.current_cell_bar > a')->text()), 'Featured Shop');
    }

    /**
     * Test collections splash.
     */
    public function testShowCollectionsSplash()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/collections');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(trim($crawler->filter('div.splash_header')->text()), 'Presenting the Collections');

        // Ending soon collections
        $shops = $crawler->filter('div#ending_soon_collections > div.current_cell');
        $this->assertCount(3, $shops);

        $this->assertEquals($shops->eq(0)->filter('a')->attr('href'),                        '/shop/show_collection/active-ending-soon-two-5');
        $this->assertEquals($shops->eq(0)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-ending-soon-two-5');
        $this->assertEquals($shops->eq(0)->filter('div.current_cell_bar > a')->text(),       'Active Ending Soon Two');

        $this->assertEquals($shops->eq(1)->filter('a')->attr('href'),                        '/shop/show_collection/active-ending-soon-one-4');
        $this->assertEquals($shops->eq(1)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-ending-soon-one-4');
        $this->assertEquals($shops->eq(1)->filter('div.current_cell_bar > a')->text(),       'Active Ending Soon One');

        $this->assertEquals($shops->eq(2)->filter('a')->attr('href'),                        '/shop/show_collection/active-ending-soon-three-6');
        $this->assertEquals($shops->eq(2)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-ending-soon-three-6');
        $this->assertEquals($shops->eq(2)->filter('div.current_cell_bar > a')->text(),       'Active Ending Soon Three');

        // Regular collections
        $shops = $crawler->filter('div#current_sale_collections > div.current_cell');
        $this->assertCount(3, $shops);

        $this->assertEquals($shops->eq(0)->filter('a')->attr('href'),                        '/shop/show_collection/active-not-ending-soon-one-1');
        $this->assertEquals($shops->eq(0)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-not-ending-soon-one-1');
        $this->assertEquals($shops->eq(0)->filter('div.current_cell_bar > a')->text(),       'Active Not Ending Soon One');

        $this->assertEquals($shops->eq(1)->filter('a')->attr('href'),                        '/shop/show_collection/active-not-ending-soon-three-3');
        $this->assertEquals($shops->eq(1)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-not-ending-soon-three-3');
        $this->assertEquals($shops->eq(1)->filter('div.current_cell_bar > a')->text(),       'Active Not Ending Soon Three');

        $this->assertEquals($shops->eq(2)->filter('a')->attr('href'),                        '/shop/show_collection/active-not-ending-soon-two-2');
        $this->assertEquals($shops->eq(2)->filter('div.current_cell_bar > a')->attr('href'), '/shop/show_collection/active-not-ending-soon-two-2');
        $this->assertEquals($shops->eq(2)->filter('div.current_cell_bar > a')->text(),       'Active Not Ending Soon Two');
    }
}
