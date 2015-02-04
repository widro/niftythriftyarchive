<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategoryData;

class ProductCategoryControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_productcategorytype[productCategoryName]"  => 'Admin create category',
                                "niftythrifty_shopbundle_productcategorytype[inNavigation]"         => 'no',
                                "niftythrifty_shopbundle_productcategorytype[navigationOrder]"      => '10');
        $this->update   = array("niftythrifty_shopbundle_productcategorytype[productCategoryName]"  => 'Update category',
                                "niftythrifty_shopbundle_productcategorytype[inNavigation]"         => 'yes',
                                "niftythrifty_shopbundle_productcategorytype[navigationOrder]"      => '2');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductCategoryData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/product_category_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new ProductCategory')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();
        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Productcategoryid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   6);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Productcategoryname');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin create category');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Innavigation');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'no');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Navigationorder');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   '10');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorytype_productCategoryName')->attr('value'), 'Admin create category');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_productcategorytype_inNavigation > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_productcategorytype_inNavigation > option[selected]')->attr('value'), 'no');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorytype_navigationOrder')->attr('value'), '10');

        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the edit properties
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorytype_productCategoryName')->attr('value'), 'Update category');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_productcategorytype_inNavigation > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_productcategorytype_inNavigation > option[selected]')->attr('value'), 'yes');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorytype_navigationOrder')->attr('value'), '2');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Admin create category/', $client->getResponse()->getContent());
        $this->assertNotRegExp('/Update category/', $client->getResponse()->getContent());
        $this->assertRegExp('/Rompers/', $client->getResponse()->getContent());
    }
}
