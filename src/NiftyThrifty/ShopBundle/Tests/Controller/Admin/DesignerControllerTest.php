<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\DesignerData;

class DesignerControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_designertype[designerName]" => 'Admin created designer');
        $this->update   = array("niftythrifty_shopbundle_designertype[designerName]" => 'Designer update');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new DesignerData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/designer/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new Designer')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();
        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Designerid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   5);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Designername');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin created designer');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_designertype_designerName')->attr('value'), 'Admin created designer');

        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_designertype_designerName')->attr('value'), 'Designer update');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Admin created designer/', $client->getResponse()->getContent());
        $this->assertNotRegExp('/Designer update/', $client->getResponse()->getContent());
        $this->assertRegExp('/Coach/', $client->getResponse()->getContent());
    }
}
