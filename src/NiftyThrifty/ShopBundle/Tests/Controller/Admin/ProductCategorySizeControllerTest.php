<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategorySizeData;

class ProductCategorySizeControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_productcategorysizetype[productCategorySizeName]"  => 'Admin new size',
                                "niftythrifty_shopbundle_productcategorysizetype[productCategorySizeValue]" => 'size',
                                "niftythrifty_shopbundle_productcategorysizetype[productCategorySizeOrder]" => '5',
                                "niftythrifty_shopbundle_productcategorysizetype[productCategory]"          => '3');
        $this->update   = array("niftythrifty_shopbundle_productcategorysizetype[productCategorySizeName]"  => 'Admin new size edit',
                                "niftythrifty_shopbundle_productcategorysizetype[productCategorySizeValue]" => 'othersize',
                                "niftythrifty_shopbundle_productcategorysizetype[productCategorySizeOrder]" => '6',
                                "niftythrifty_shopbundle_productcategorysizetype[productCategory]"          => '2');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductCategorySizeData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/product_category_size_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new ProductCategorySize')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();
        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Productcategorysizeid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   10);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Productcategorysizename');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin new size');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Productcategorysizevalue');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'size');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Productcategorysizeorder');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   5);
        $this->assertEquals($children->eq(4)->filter('label')->text(),  'Productcategoryid');
        $this->assertEquals($children->eq(4)->filter('span')->text(),   3);

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorysizetype_productCategorySizeName')->attr('value'), 'Admin new size');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorysizetype_productCategorySizeValue')->attr('value'), 'size');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorysizetype_productCategorySizeOrder')->attr('value'), '5');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_productcategorysizetype_productCategory > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_productcategorysizetype_productCategory > option[selected]')->attr('value'), 3);

        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the edit properties
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorysizetype_productCategorySizeName')->attr('value'), 'Admin new size edit');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorysizetype_productCategorySizeValue')->attr('value'), 'othersize');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_productcategorysizetype_productCategorySizeOrder')->attr('value'), '6');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_productcategorysizetype_productCategory > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_productcategorysizetype_productCategory > option[selected]')->attr('value'), 2);

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Admin new size/', $client->getResponse()->getContent());
        $this->assertNotRegExp('/Update category/', $client->getResponse()->getContent());
    }
}
