<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagData;

class ProductTagControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_producttagtype[productTagName]"    => 'Admin tag',
                                "niftythrifty_shopbundle_producttagtype[productTagSlug]"    => 'admin-tag',
                                "niftythrifty_shopbundle_producttagtype[productTagtype]"    => '3');
        $this->update   = array("niftythrifty_shopbundle_producttagtype[productTagName]"    => 'Admin tag edit',
                                "niftythrifty_shopbundle_producttagtype[productTagSlug]"    => 'admin-tag-edit',
                                "niftythrifty_shopbundle_producttagtype[productTagtype]"    => '2');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductTagData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/product_tag_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new ProductTag')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();
        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Producttagid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   11);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Producttagname');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin tag');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Producttagslug');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'admin-tag');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Producttagtypeid');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   3);

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttagtype_productTagName')->attr('value'), 'Admin tag');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttagtype_productTagSlug')->attr('value'), 'admin-tag');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttagtype_productTagtype > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttagtype_productTagtype > option[selected]')->attr('value'), 3);

        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the edit properties
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttagtype_productTagName')->attr('value'), 'Admin tag edit');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttagtype_productTagSlug')->attr('value'), 'admin-tag-edit');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttagtype_productTagtype > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttagtype_productTagtype > option[selected]')->attr('value'), 2);

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Admin tag/', $client->getResponse()->getContent());
    }
}
