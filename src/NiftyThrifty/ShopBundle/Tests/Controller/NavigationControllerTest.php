<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\CollectionData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserCreditsData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;

/**
 * None of the functions in this controller are routed, so basically we have to make one
 * overall request to the front page, and verify all the unrouted functions were called
 * correctly and all the partial templates rendered the expected stuff.
 *
 * This whole file is one unit test.
 */
class NavigationControllerTest extends NiftyBaseTestCase
{
    /**
     * loading the BasketItem fixture basically includes everything we need.  BasketItem includes
     * Basket and Product.  Basket includes User.  Product includes all Product Stuff.  
     * We could theoretically test this against any route.
     */
    public function testNavigationWrapper()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new CollectionData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        // Break the assertions up in to different functions for readability
        /** These have been hardcoded in to navigation 
        $this->_assertCategories($crawler);
        $this->_assertShops($crawler);**/
        $this->_assertActiveCollections($crawler);
        $this->_assertEndingSoonCollections($crawler);
        $this->_assertCreditCount($crawler);
        $this->_assertBanner($crawler);
    }
    
    /**
     * Categories are considered "Vintage Staples" shops on the front end.  The IDs reflect this.
     *
     * @covers Navigation::categoriesAction
     */
    private function _assertCategories($crawler)
    {
        $this->assertCount(1, $crawler->filter('div#hovernav > div#category-staples'));
        $this->assertEquals($crawler->filter('div#category-staples > h3')->text(), 'CLOTHING');
        $this->assertCount(1, $crawler->filter('div#category-staples > ul#categoriesList'));
        $categoryNodes = $crawler->filter('ul#categoriesList')->children();
        $this->assertTrue($categoryNodes->count() > 0);
        
        /** This is hardcoded now, so we don't need to check that the database is functional
        $this->assertEquals($categoryNodes->eq(0)->attr('id'),  'cat2');
        $this->assertCount(1, $categoryNodes->eq(0)->children());
        $this->assertEquals($categoryNodes->eq(0)->children()->eq(0)->attr('href'), '/shop/category/dresses-2');
        $this->assertEquals($categoryNodes->eq(0)->children()->eq(0)->text(), 'Dresses');

        $this->assertEquals($categoryNodes->eq(1)->attr('id'),  'cat1');
        $this->assertCount(1, $categoryNodes->eq(1)->children());
        $this->assertEquals($categoryNodes->eq(1)->children()->eq(0)->attr('href'), '/shop/category/jumpers-1');
        $this->assertEquals($categoryNodes->eq(1)->children()->eq(0)->text(), 'Jumpers');

        $this->assertEquals($categoryNodes->eq(2)->attr('id'),  'cat3');
        $this->assertCount(1, $categoryNodes->eq(2)->children());
        $this->assertEquals($categoryNodes->eq(2)->children()->eq(0)->attr('href'), '/shop/category/rompers-3');
        $this->assertEquals($categoryNodes->eq(2)->children()->eq(0)->text(), 'Rompers');

        $this->assertEquals($categoryNodes->eq(3)->attr('id'),  'cat4');
        $this->assertCount(1, $categoryNodes->eq(3)->children());
        $this->assertEquals($categoryNodes->eq(3)->children()->eq(0)->attr('href'), '/shop/category/shoes-4');
        $this->assertEquals($categoryNodes->eq(3)->children()->eq(0)->text(), 'Shoes');
        **/
    }
    
    /**
     * @covers NavigationController::shopsAction
     */
    private function _assertShops($crawler)
    {
        $this->assertCount(1, $crawler->filter('div#hovernav > div#featured-shops'));
        $this->assertEquals($crawler->filter('div#featured-shops > h3')->text(), 'FEATURED');
        $this->assertCount(1, $crawler->filter('div#featured-shops > ul#shopsList'));
        $shopNodes = $crawler->filter('ul#shopsList')->children();
        $this->assertTrue($shopNodes->count() > 0);
        
        /** This is hardcoded now
        $this->assertEquals($shopNodes->eq(0)->attr('id'),  'shp13');
        $this->assertCount(1, $shopNodes->eq(0)->children());
        $this->assertEquals($shopNodes->eq(0)->children()->eq(0)->attr('href'), '/shop/show_collection/featured-shop-13');
        $this->assertEquals($shopNodes->eq(0)->children()->eq(0)->text(), 'Featured Shop');

        $this->assertEquals($shopNodes->eq(1)->attr('id'),  'shp12');
        $this->assertCount(1, $shopNodes->eq(1)->children());
        $this->assertEquals($shopNodes->eq(1)->children()->eq(0)->attr('href'), '/shop/show_collection/vintage-staples-12');
        $this->assertEquals($shopNodes->eq(1)->children()->eq(0)->text(), 'Vintage Staples');
        **/
    }
    
    /**
     * @covers NavigationController::activeCollectionsAction
     */
    private function _assertActiveCollections($crawler)
    {
        $this->assertCount(1, $crawler->filter('div#hovernav2 > div#collections-hover'));
        $this->assertEquals($crawler->filter('div#collections-hover > h3')->text(), 'CURRENT SALES');
        $this->assertCount(1, $crawler->filter('div#collections-hover > ul#current-sales'));
        $collectionNodes = $crawler->filter('ul#current-sales')->children();
        $this->assertCount(8, $collectionNodes);

        $this->assertEquals($collectionNodes->eq(0)->attr('id'),  'coll13');
        $this->assertCount(1, $collectionNodes->eq(0)->children());
        $this->assertEquals($collectionNodes->eq(0)->children()->eq(0)->attr('href'), '/shop/show_collection/featured-shop-13');
        $this->assertEquals($collectionNodes->eq(0)->children()->eq(0)->text(), 'Featured Shop');

        $this->assertEquals($collectionNodes->eq(1)->attr('id'),  'coll12');
        $this->assertCount(1, $collectionNodes->eq(1)->children());
        $this->assertEquals($collectionNodes->eq(1)->children()->eq(0)->attr('href'), '/shop/show_collection/vintage-staples-12');
        $this->assertEquals($collectionNodes->eq(1)->children()->eq(0)->text(), 'Vintage Staples');

        $this->assertEquals($collectionNodes->eq(2)->attr('id'),  'coll6');
        $this->assertCount(1, $collectionNodes->eq(2)->children());
        $this->assertEquals($collectionNodes->eq(2)->children()->eq(0)->attr('href'), '/shop/show_collection/active-ending-soon-three-6');
        $this->assertEquals($collectionNodes->eq(2)->children()->eq(0)->text(), 'Active Ending Soon Three');

        $this->assertEquals($collectionNodes->eq(3)->attr('id'),  'coll5');
        $this->assertCount(1, $collectionNodes->eq(3)->children());
        $this->assertEquals($collectionNodes->eq(3)->children()->eq(0)->attr('href'), '/shop/show_collection/active-ending-soon-two-5');
        $this->assertEquals($collectionNodes->eq(3)->children()->eq(0)->text(), 'Active Ending Soon Two');

        $this->assertEquals($collectionNodes->eq(4)->attr('id'),  'coll4');
        $this->assertCount(1, $collectionNodes->eq(4)->children());
        $this->assertEquals($collectionNodes->eq(4)->children()->eq(0)->attr('href'), '/shop/show_collection/active-ending-soon-one-4');
        $this->assertEquals($collectionNodes->eq(4)->children()->eq(0)->text(), 'Active Ending Soon One');

        $this->assertEquals($collectionNodes->eq(5)->attr('id'),  'coll1');
        $this->assertCount(1, $collectionNodes->eq(5)->children());
        $this->assertEquals($collectionNodes->eq(5)->children()->eq(0)->attr('href'), '/shop/show_collection/active-not-ending-soon-one-1');
        $this->assertEquals($collectionNodes->eq(5)->children()->eq(0)->text(), 'Active Not Ending Soon One');

        $this->assertEquals($collectionNodes->eq(6)->attr('id'),  'coll2');
        $this->assertCount(1, $collectionNodes->eq(6)->children());
        $this->assertEquals($collectionNodes->eq(6)->children()->eq(0)->attr('href'), '/shop/show_collection/active-not-ending-soon-two-2');
        $this->assertEquals($collectionNodes->eq(6)->children()->eq(0)->text(), 'Active Not Ending Soon Two');

        $this->assertEquals($collectionNodes->eq(7)->attr('id'),  'coll3');
        $this->assertCount(1, $collectionNodes->eq(7)->children());
        $this->assertEquals($collectionNodes->eq(7)->children()->eq(0)->attr('href'), '/shop/show_collection/active-not-ending-soon-three-3');
        $this->assertEquals($collectionNodes->eq(7)->children()->eq(0)->text(), 'Active Not Ending Soon Three');
    }
    
    private function _assertEndingSoonCollections($crawler)
    {
        $this->assertCount(1, $crawler->filter('div#hovernav2 > div#ending-hover'));
        $this->assertEquals($crawler->filter('div#ending-hover > h3')->text(), 'ENDING SOON');
        $this->assertCount(1, $crawler->filter('div#ending-hover > ul#ending-soon'));
        $collectionNodes = $crawler->filter('ul#ending-soon')->children();
        $this->assertCount(7, $collectionNodes);

        $this->assertEquals($collectionNodes->eq(0)->attr('id'),  'coll3');
        $this->assertCount(1, $collectionNodes->eq(0)->children());
        $this->assertEquals($collectionNodes->eq(0)->children()->eq(0)->attr('href'), '/shop/show_collection/active-not-ending-soon-three-3');
        $this->assertEquals($collectionNodes->eq(0)->children()->eq(0)->text(), 'Active Not Ending Soon Three');

        $this->assertEquals($collectionNodes->eq(1)->attr('id'),  'coll2');
        $this->assertCount(1, $collectionNodes->eq(1)->children());
        $this->assertEquals($collectionNodes->eq(1)->children()->eq(0)->attr('href'), '/shop/show_collection/active-not-ending-soon-two-2');
        $this->assertEquals($collectionNodes->eq(1)->children()->eq(0)->text(), 'Active Not Ending Soon Two');

        $this->assertEquals($collectionNodes->eq(2)->attr('id'),  'coll1');
        $this->assertCount(1, $collectionNodes->eq(2)->children());
        $this->assertEquals($collectionNodes->eq(2)->children()->eq(0)->attr('href'), '/shop/show_collection/active-not-ending-soon-one-1');
        $this->assertEquals($collectionNodes->eq(2)->children()->eq(0)->text(), 'Active Not Ending Soon One');
        
        $this->assertEquals($collectionNodes->eq(3)->attr('id'),  'coll4');
        $this->assertCount(1, $collectionNodes->eq(3)->children());
        $this->assertEquals($collectionNodes->eq(3)->children()->eq(0)->attr('href'), '/shop/show_collection/active-ending-soon-one-4');
        $this->assertEquals($collectionNodes->eq(3)->children()->eq(0)->text(), 'Active Ending Soon One');

        $this->assertEquals($collectionNodes->eq(4)->attr('id'),  'coll5');
        $this->assertCount(1, $collectionNodes->eq(4)->children());
        $this->assertEquals($collectionNodes->eq(4)->children()->eq(0)->attr('href'), '/shop/show_collection/active-ending-soon-two-5');
        $this->assertEquals($collectionNodes->eq(4)->children()->eq(0)->text(), 'Active Ending Soon Two');

        $this->assertEquals($collectionNodes->eq(5)->attr('id'),  'coll6');
        $this->assertCount(1, $collectionNodes->eq(5)->children());
        $this->assertEquals($collectionNodes->eq(5)->children()->eq(0)->attr('href'), '/shop/show_collection/active-ending-soon-three-6');
        $this->assertEquals($collectionNodes->eq(5)->children()->eq(0)->text(), 'Active Ending Soon Three');

        $this->assertEquals($collectionNodes->eq(6)->attr('id'),  'coll12');
        $this->assertCount(1, $collectionNodes->eq(6)->children());
        $this->assertEquals($collectionNodes->eq(6)->children()->eq(0)->attr('href'), '/shop/show_collection/vintage-staples-12');
        $this->assertEquals($collectionNodes->eq(6)->children()->eq(0)->text(), 'Vintage Staples');
    }

    private function _assertCreditCount($crawler)
    {
        $this->assertCount(1, $crawler->filter('li#navcredits > span'));
        $this->assertEquals(14, $crawler->filter('li#navcredits > span')->text());
    }

    private function _assertBanner($crawler)
    {
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);

        $this->assertEquals($crawler->filter('img.home_upper_right')->attr('src'), $banner->getBannerImage());
        $this->assertEquals($crawler->filter('a#home_upper_right_link')->attr('href'), $banner->getUrl());
        $this->assertCount(1, $crawler->filter('a#home_upper_right_link > img.home_upper_right'));
    }

    /**
     * Same as above, but remove the URL in the banner and ensure the banner is still displayed.
     */
    public function testLinklessBannerImage()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new CollectionData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);
        $banner->setUrl(null);
        $this->em->flush();

        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->_assertActiveCollections($crawler);
        $this->_assertEndingSoonCollections($crawler);
        $this->_assertCreditCount($crawler);

        $this->assertEquals($crawler->filter('img.home_upper_right')->attr('src'), $banner->getBannerImage());
        $this->assertCount(0, $crawler->filter('a#home_upper_right_link'));
    }

    /** THE SIDEBAR HAS BEEN HARD-CODED.  THIS DOESN'T NEED TO BE TESTED AT THE MOMENT.
     * This is verified by a partial template on the show collection page.  So grab a collection and
     * verify the propery categories and sizes are displayed.
    public function testFiltersAllSidebarAction()
    {

        $this->addFixture(new ProductData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/shop/category/dresses-2');
        $this->assertTrue($client->getResponse()->isSuccessful());

        // The categories list + 1 clear div
        $this->assertCount(1, $crawler->filter('div#filters-category-list'));
        $categories = $crawler->filter('div#filters-category-list')->children();
        $this->assertCount(6, $categories);
        $this->assertEquals($categories->eq(0)->filter('span.label')->text(),       'Dresses');
        $this->assertEquals($categories->eq(0)->filter('span.value')->text(),       '2');
        $this->assertEquals($crawler->filter('div.selected > span.label')->text(),  'Dresses');
        $this->assertEquals($categories->eq(1)->filter('span.label')->text(),       'Jumpers');
        $this->assertEquals($categories->eq(1)->filter('span.value')->text(),       '1');
        $this->assertEquals($categories->eq(2)->filter('span.label')->text(),       'Rompers');
        $this->assertEquals($categories->eq(2)->filter('span.value')->text(),       '3');
        $this->assertEquals($categories->eq(3)->filter('span.label')->text(),       'Shoes');
        $this->assertEquals($categories->eq(3)->filter('span.value')->text(),       '4');
        $this->assertEquals($categories->eq(4)->filter('span.label')->text(),       'Tops');
        $this->assertEquals($categories->eq(4)->filter('span.value')->text(),       '5');

        // The sizes.  Add 1 to all child counts for a "clear" class.
        $this->assertCount(1, $crawler->filter('div#filters-category-sizes-list'));
        $sizeCategories = $crawler->filter('div#filters-category-sizes-list')->children();
        $this->assertCount(6, $sizeCategories);

        // Dress Sizes
        $this->assertEquals($sizeCategories->eq(0)->filter('span.optionGroup_title')->text(), 'Dresses');
        $dressSizes = $sizeCategories->eq(0)->filter('div.product_category_2')->children();
        $this->assertCount(4, $dressSizes);
        $this->assertEquals($dressSizes->eq(0)->filter('span.label')->text(),   '6');
        $this->assertEquals($dressSizes->eq(0)->filter('span.value')->text(),   '6');
        $this->assertEquals($dressSizes->eq(1)->filter('span.label')->text(),   '8');
        $this->assertEquals($dressSizes->eq(1)->filter('span.value')->text(),   '3');
        $this->assertEquals($dressSizes->eq(2)->filter('span.label')->text(),   '10');
        $this->assertEquals($dressSizes->eq(2)->filter('span.value')->text(),   '9');

        // Jumper sizes
        $this->assertEquals($sizeCategories->eq(1)->filter('span.optionGroup_title')->text(), 'Jumpers');
        $jumperSizes = $sizeCategories->eq(1)->filter('div.product_category_1')->children();
        $this->assertCount(3, $jumperSizes);
        $this->assertEquals($jumperSizes->eq(0)->filter('span.label')->text(),  'S');
        $this->assertEquals($jumperSizes->eq(0)->filter('span.value')->text(),  '1');
        $this->assertEquals($jumperSizes->eq(1)->filter('span.label')->text(),  'M');
        $this->assertEquals($jumperSizes->eq(1)->filter('span.value')->text(),  '2');

        // Romper sizes
        $this->assertEquals($sizeCategories->eq(2)->filter('span.optionGroup_title')->text(), 'Rompers');
        $romperSizes = $sizeCategories->eq(2)->filter('div.product_category_3')->children();
        $this->assertCount(3, $romperSizes);
        $this->assertEquals($romperSizes->eq(0)->filter('span.label')->text(),  'M');
        $this->assertEquals($romperSizes->eq(0)->filter('span.value')->text(),  '4');
        $this->assertEquals($romperSizes->eq(1)->filter('span.label')->text(),  'L');
        $this->assertEquals($romperSizes->eq(1)->filter('span.value')->text(),  '5');

        // Shoe sizes
        $this->assertEquals($sizeCategories->eq(3)->filter('span.optionGroup_title')->text(), 'Shoes');
        $shoeSizes = $sizeCategories->eq(3)->filter('div.product_category_4')->children();
        $this->assertCount(3, $shoeSizes);
        $this->assertEquals($shoeSizes->eq(0)->filter('span.label')->text(),    '8');
        $this->assertEquals($shoeSizes->eq(0)->filter('span.value')->text(),    '7');
        $this->assertEquals($shoeSizes->eq(1)->filter('span.label')->text(),    '9');
        $this->assertEquals($shoeSizes->eq(1)->filter('span.value')->text(),    '8');

        $this->assertEquals($sizeCategories->eq(4)->filter('span.optionGroup_title')->text(), 'Tops');
    }**/
}
