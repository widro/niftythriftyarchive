<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagtypeData;

class ProductTagtypeControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_producttagtypetype[productTagtypeName]" => 'Admin create tagtype');
        $this->update   = array("niftythrifty_shopbundle_producttagtypetype[productTagtypeName]" => 'Tagtype update');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductTagtypeData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/product_tagtype_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new ProductTagtype')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();
        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Producttagtypeid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   6);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Producttagtypename');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin create tagtype');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttagtypetype_productTagtypeName')->attr('value'), 'Admin create tagtype');

        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttagtypetype_productTagtypeName')->attr('value'), 'Tagtype update');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Admin create tagtype/', $client->getResponse()->getContent());
        $this->assertNotRegExp('/Tagtype update/', $client->getResponse()->getContent());
        $this->assertRegExp('/Color/', $client->getResponse()->getContent());
    }
}
