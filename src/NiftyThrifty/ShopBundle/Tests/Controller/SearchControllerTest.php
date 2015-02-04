<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;

class SearchControllerTest extends NiftyBaseTestCase
{
    /**
     * This test is for the text search of Products.
     *
     * @group Search
     * @covers SearchController::getItemsBySearchAction
     */
    public function testGetItemsBySearchActionOneItem()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_search/eight');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(1, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 1;', $crawler->text());
        $this->assertEquals($crawler->filter('div.product > div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($crawler->filter('div.product > div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($crawler->filter('div.product > div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $crawler->filter('div.product > div.infos > div.old'));
        $this->assertEquals($crawler->filter('div.product > div.infos > div.price')->text(), '$46');
        $this->assertEquals($crawler->filter('div.product > div.infos > div.size > div.size_value')->text(), 'S');
    }

    /**
     * Verify the search is case-insensitive
     *
     * @group Search
     * @covers SearchController::getItemsBySearchAction
     */
    public function testGetItemsBySearchActionCaseInsensitive()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_search/EIGHT');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(1, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 1;', $crawler->text());
        $this->assertEquals($crawler->filter('div.product > div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($crawler->filter('div.product > div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($crawler->filter('div.product > div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $crawler->filter('div.product > div.infos > div.old'));
        $this->assertEquals($crawler->filter('div.product > div.infos > div.price')->text(), '$46');
        $this->assertEquals($crawler->filter('div.product > div.infos > div.size > div.size_value')->text(), 'S');
    }

    /**
     * Test returning multiple items.
     *
     * @group Search
     * @covers SearchController::getItemsBySearchAction
     */
    public function testGetItemsBySearchActionMultiItem()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_search/product');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(3, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 3;', $crawler->text());
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-nine-9');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Nine');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Under Armour');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->text(), '$25');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'L');

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$46');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');

        $this->assertEquals($products->eq(2)->filter('div.img > a')->attr('href'), '/shop/show_item/product-six-6');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.product_name')->text(), 'Product Six');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.old')->text()), '$20');
        $this->assertEquals(trim($products->eq(2)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.price')->eq(1)->text(), '$12');
        $this->assertEquals($products->eq(2)->filter('div.infos > div.size > div.size_value')->text(), '8');
    }

    /**
     * Test a multiple word search.
     *
     * @group Search
     * @covers SearchController::getItemsBySearchAction
     */
    public function testGetItemsBySearchMultipleWords()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_search/product eight');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(1, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 1;', $crawler->text());
        $this->assertEquals($crawler->filter('div.product > div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($crawler->filter('div.product > div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertEquals(trim($crawler->filter('div.product > div.infos > div.product_designer')->text()), 'Coach');
        $this->assertCount(0, $crawler->filter('div.product > div.infos > div.old'));
        $this->assertEquals($crawler->filter('div.product > div.infos > div.price')->text(), '$46');
        $this->assertEquals($crawler->filter('div.product > div.infos > div.size > div.size_value')->text(), 'S');
    }

    /**
     * Test returning no results
     *
     * @group Search
     * @covers SearchController::getItemsBySearchAction
     */
    public function testGetItemsBySearchActionNoResults()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_search/wordsandstuff');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
    }

    /**
     * Test the over/under function with under.
     *
     * @group Controller
     * @group Search
     * @covers SearchController::getItemsByValueAction
     */
    public function testGetItemsByValueActionUnder()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(9);
        $product->setProductPrice(17)
                ->setProductOldPrice(25);
        $this->em->flush();

        $crawler = $client->request('GET', '/search/get_items_by_value/under-19');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(2, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 2;', $crawler->text());
        $this->assertContains('Results: Under $19', $crawler->text());
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-six-6');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Six');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.old')->text()), '$20');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->eq(1)->text(), '$12');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), '8');

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-nine-9');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Nine');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Under Armour');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.old')->text(), '$25');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->eq(1)->text(), '$17');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'L');
    }
    
    /**
     * Test the over/under function with over.
     *
     * @group Controller
     * @group Search
     * @covers SearchController::getItemsByValueAction
     */
    public function testGetItemsByValueActionOver()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_value/over-15');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(2, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 2;', $crawler->text());
        $this->assertContains('Results: Over $15', $crawler->text());
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-nine-9');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Nine');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Under Armour');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->text(), '$25');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'L');

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-eight-8');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Eight');
        $this->assertCount(0, $products->eq(1)->filter('div.infos > div.old'));
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), 'Coach');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->text(), '$46');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), 'S');
    }
    
    /**
     * Test the over/under function with a bad url.
     *
     * @group Controller
     * @group Search
     * @covers SearchController::getItemsByValueAction
     */
    public function testGetItemsByValueNotFound()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $crawler = $client->request('GET', '/search/get_items_by_value/xxxxx-xxx');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    /**
     * Test the over/under function when no results are found.
     *
     * @group Controller
     * @group Search
     * @covers SearchController::getItemsByValueAction
     */
    public function testGetItemsByValueActionNoResults()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/search/get_items_by_value/under-5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#noResults'));
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertContains('Results: Under $5', $crawler->text());
    }

    /**
     * Test the clearance link... which gets everything with a sale price.
     *
     * @group Search
     * @covers SearchController::getItemsBySale
     */
    public function testGetItemsBySale()
    {
        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(9);
        $product->setProductPrice(17)
                ->setProductOldPrice(25);
        $this->em->flush();

        $client  = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/search/get_items_by_sale');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(0, $crawler->filter('div#noResults'));
        $this->assertCount(2, $crawler->filter('div.product'));
        $this->assertContains('var productSearchCount = 2;', $crawler->text());
        $products = $crawler->filter('div.product');

        $this->assertEquals($products->eq(0)->filter('div.img > a')->attr('href'), '/shop/show_item/product-nine-9');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.product_name')->text(), 'Product Nine');
        $this->assertEquals(trim($products->eq(0)->filter('div.infos > div.product_designer')->text()), 'Under Armour');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.old')->text(), '$25');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.price')->eq(1)->text(), '$17');
        $this->assertEquals($products->eq(0)->filter('div.infos > div.size > div.size_value')->text(), 'L');

        $this->assertEquals($products->eq(1)->filter('div.img > a')->attr('href'), '/shop/show_item/product-six-6');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.product_name')->text(), 'Product Six');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.old')->text(), '$20');
        $this->assertEquals(trim($products->eq(1)->filter('div.infos > div.product_designer')->text()), '');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.price')->eq(1)->text(), '$12');
        $this->assertEquals($products->eq(1)->filter('div.infos > div.size > div.size_value')->text(), '8');
    }
}
