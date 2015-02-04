<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\CollectionData;

class CollectionControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_collectiontype[collectionName]"                    => 'Create admin collection',
                                "niftythrifty_shopbundle_collectiontype[collectionCode]"                    => 'ADM',
                                "niftythrifty_shopbundle_collectiontype[isShop]"                            => 'no',
                                "niftythrifty_shopbundle_collectiontype[collectionDescription]"             => 'Created via admin',
                                "niftythrifty_shopbundle_collectiontype[collectionType]"                    => 'Women',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][date][month]"  => '4',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][date][day]"    => '15',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][date][year]"   => '2015',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][time][hour]"   => '11',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][time][minute]" => '15',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][date][month]"    => '5',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][date][day]"      => '12',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][date][year]"     => '2015',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][time][hour]"     => '14',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][time][minute]"   => '15',
                                "niftythrifty_shopbundle_collectiontype[collectionActive]"                  => 'no',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualMainPanel]"         => '1',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualMainPanelBw]"       => '1',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualHomeHero]"          => '1',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualSaleHero]"          => '1');

        $this->update   = array("niftythrifty_shopbundle_collectiontype[collectionName]"                    => 'Create admin collection edit',
                                "niftythrifty_shopbundle_collectiontype[collectionCode]"                    => 'ADE',
                                "niftythrifty_shopbundle_collectiontype[isShop]"                            => 'yes',
                                "niftythrifty_shopbundle_collectiontype[collectionDescription]"             => 'Created via admin edit',
                                "niftythrifty_shopbundle_collectiontype[collectionType]"                    => 'Men',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][date][month]"  => '3',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][date][day]"    => '14',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][date][year]"   => '2014',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][time][hour]"   => '10',
                                "niftythrifty_shopbundle_collectiontype[collectionDateStart][time][minute]" => '14',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][date][month]"    => '6',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][date][day]"      => '13',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][date][year]"     => '2016',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][time][hour]"     => '15',
                                "niftythrifty_shopbundle_collectiontype[collectionDateEnd][time][minute]"   => '16',
                                "niftythrifty_shopbundle_collectiontype[collectionActive]"                  => 'yes',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualMainPanel]"         => '1',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualMainPanelBw]"       => '1',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualHomeHero]"          => '1',
                                "niftythrifty_shopbundle_collectiontype[collectionVisualSaleHero]"          => '1');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/collection_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new Collection')->link());

        // Check the defined pre-selections
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionType > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionType > option[selected]')->attr('value'), 'Women');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionActive > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionActive > option[selected]')->attr('value'), 'no');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_isShop > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_isShop > option[selected]')->attr('value'), 'no');

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();

        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Collectionid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   14);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Collectioncode');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'ADM');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Isshop');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'no');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Collectionname');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   'Create admin collection');
        $this->assertEquals($children->eq(4)->filter('label')->text(),  'Collectiondescription');
        $this->assertEquals($children->eq(4)->filter('span')->text(),   'Created via admin');
        $this->assertEquals($children->eq(5)->filter('label')->text(),  'Collectiontype');
        $this->assertEquals($children->eq(5)->filter('span')->text(),   'Women');
        $this->assertEquals($children->eq(6)->filter('label')->text(),  'Collectiondatestart');
        $this->assertEquals($children->eq(6)->filter('span')->text(),   '2015-04-15 11:15:00');
        $this->assertEquals($children->eq(7)->filter('label')->text(),  'Collectiondateend');
        $this->assertEquals($children->eq(7)->filter('span')->text(),   '2015-05-12 14:15:00');
        $this->assertEquals($children->eq(8)->filter('label')->text(),  'Collectionactive');
        $this->assertEquals($children->eq(8)->filter('span')->text(),   'no');
        
        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_collectiontype_collectionName')->attr('value'), 'Create admin collection');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_collectiontype_collectionCode')->attr('value'), 'ADM');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_isShop > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_isShop > option[selected]')->attr('value'), 'no');
        $this->assertEquals($crawler->filter('textarea#niftythrifty_shopbundle_collectiontype_collectionDescription')->text(), 'Created via admin');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionType > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionType > option[selected]')->attr('value'), 'Women');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionActive > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionActive > option[selected]')->attr('value'), 'no');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_month > option[selected]')->attr('value'), 4);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_day > option[selected]')->attr('value'), 15);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_year > option[selected]')->attr('value'), 2015);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_hour > option[selected]')->attr('value'), 11);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_minute > option[selected]')->attr('value'), 15);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_month > option[selected]')->attr('value'), 5);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_day > option[selected]')->attr('value'), 12);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_year > option[selected]')->attr('value'), 2015);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_hour > option[selected]')->attr('value'), 14);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_minute > option[selected]')->attr('value'), 15);

        // Submit the edit
        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_collectiontype_collectionName')->attr('value'), 'Create admin collection edit');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_collectiontype_collectionCode')->attr('value'), 'ADE');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_isShop > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_isShop > option[selected]')->attr('value'), 'yes');
        $this->assertEquals($crawler->filter('textarea#niftythrifty_shopbundle_collectiontype_collectionDescription')->text(), 'Created via admin edit');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionType > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionType > option[selected]')->attr('value'), 'Men');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionActive > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionActive > option[selected]')->attr('value'), 'yes');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_month > option[selected]')->attr('value'), 3);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_day > option[selected]')->attr('value'), 14);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_date_year > option[selected]')->attr('value'), 2014);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_hour > option[selected]')->attr('value'), 10);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateStart_time_minute > option[selected]')->attr('value'), 14);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_month > option[selected]')->attr('value'), 6);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_day > option[selected]')->attr('value'), 13);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_date_year > option[selected]')->attr('value'), 2016);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_hour > option[selected]')->attr('value'), 15);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_collectiontype_collectionDateEnd_time_minute > option[selected]')->attr('value'), 16);

        // Click delete
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted on the list
        $this->assertNotRegExp('/Create admin collection/', $client->getResponse()->getContent());
    }
}
