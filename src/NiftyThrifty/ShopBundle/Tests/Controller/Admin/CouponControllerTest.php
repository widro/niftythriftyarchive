<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\CouponData;

class CouponControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_coupontype[couponCode]"            => 'ADMINTEST',
                                "niftythrifty_shopbundle_coupontype[couponDateStart][month]"=> '10',
                                "niftythrifty_shopbundle_coupontype[couponDateStart][day]"  => '11',
                                "niftythrifty_shopbundle_coupontype[couponDateStart][year]" => '2015',
                                "niftythrifty_shopbundle_coupontype[couponDateEnd][month]"  => '11',
                                "niftythrifty_shopbundle_coupontype[couponDateEnd][day]"    => '12',
                                "niftythrifty_shopbundle_coupontype[couponDateEnd][year]"   => '2016',
                                "niftythrifty_shopbundle_coupontype[couponPercent]"         => null,
                                "niftythrifty_shopbundle_coupontype[couponAmount]"          => '15',
                                "niftythrifty_shopbundle_coupontype[couponQuantityLimited]" => 'false',
                                "niftythrifty_shopbundle_coupontype[couponQuantity]"        => null,
                                "niftythrifty_shopbundle_coupontype[couponUnique]"          => 'false',
                                "niftythrifty_shopbundle_coupontype[couponFreeShipping]"    => 'false');

        $this->update   = array("niftythrifty_shopbundle_coupontype[couponCode]"            => 'ADMINUPDATE',
                                "niftythrifty_shopbundle_coupontype[couponDateStart][month]"=> '3',
                                "niftythrifty_shopbundle_coupontype[couponDateStart][day]"  => '4',
                                "niftythrifty_shopbundle_coupontype[couponDateStart][year]" => '2014',
                                "niftythrifty_shopbundle_coupontype[couponDateEnd][month]"  => '5',
                                "niftythrifty_shopbundle_coupontype[couponDateEnd][day]"    => '6',
                                "niftythrifty_shopbundle_coupontype[couponDateEnd][year]"   => '2015',
                                "niftythrifty_shopbundle_coupontype[couponPercent]"         => '30',
                                "niftythrifty_shopbundle_coupontype[couponAmount]"          => null,
                                "niftythrifty_shopbundle_coupontype[couponQuantityLimited]" => 'true',
                                "niftythrifty_shopbundle_coupontype[couponQuantity]"        => 10,
                                "niftythrifty_shopbundle_coupontype[couponUnique]"          => 'true',
                                "niftythrifty_shopbundle_coupontype[couponFreeShipping]"    => 'true');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new CouponData);
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/coupon_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new Coupon')->link());

        // Check pre-selections
        $month  = date('n');
        $day    = date('j');
        $year   = date('Y');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponQuantityLimited > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponQuantityLimited > option[selected]')->attr('value'), 'false');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponUnique > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponUnique > option[selected]')->attr('value'), 'false');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponFreeShipping > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponFreeShipping > option[selected]')->attr('value'), 'false');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_month > option[selected]')->attr('value'), $month);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_day > option[selected]')->attr('value'), $day);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_year > option[selected]')->attr('value'), $year);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_month > option[selected]')->attr('value'), $month);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_day > option[selected]')->attr('value'), $day);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_year > option[selected]')->attr('value'), $year);

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();
        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Couponid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   5);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Couponcode');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'ADMINTEST');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Coupondatestart');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   '2015-10-11 00:00:00');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Coupondateend');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   '2016-11-12 00:00:00');
        $this->assertEquals($children->eq(4)->filter('label')->text(),  'Couponpercent');
        $this->assertEquals($children->eq(4)->filter('span')->text(),   '');
        $this->assertEquals($children->eq(5)->filter('label')->text(),  'Couponamount');
        $this->assertEquals($children->eq(5)->filter('span')->text(),   '15');
        $this->assertEquals($children->eq(6)->filter('label')->text(),  'Couponquantitylimited');
        $this->assertEquals($children->eq(6)->filter('span')->text(),   'false');
        $this->assertEquals($children->eq(7)->filter('label')->text(),  'Couponquantity');
        $this->assertEquals($children->eq(7)->filter('span')->text(),   '');
        $this->assertEquals($children->eq(8)->filter('label')->text(),  'Couponunique');
        $this->assertEquals($children->eq(8)->filter('span')->text(),   'false');
        $this->assertEquals($children->eq(9)->filter('label')->text(),  'Coupondateadd');
        $this->assertContains(date('Y-m-d'), $children->eq(9)->filter('span')->text());
        $this->assertEquals($children->eq(10)->filter('label')->text(),  'Couponfreeshipping');
        $this->assertEquals($children->eq(10)->filter('span')->text(),   'false');
        $this->assertEquals($children->eq(11)->filter('label')->text(),  'Userid');
        $this->assertEquals($children->eq(11)->filter('span')->text(),   '');
        
        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // Check the proper stuff is pre-selected
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponCode')->attr('value'), 'ADMINTEST');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponPercent')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponAmount')->attr('value'), '15');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponQuantity')->attr('value'), '');
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponQuantityLimited > option[selected]')->attr('value'), 'false');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponUnique > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponUnique > option[selected]')->attr('value'), 'false');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponFreeShipping > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponFreeShipping > option[selected]')->attr('value'), 'false');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_month > option[selected]')->attr('value'), '10');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_day > option[selected]')->attr('value'), '11');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_year > option[selected]')->attr('value'), '2015');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_month > option[selected]')->attr('value'), '11');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_day > option[selected]')->attr('value'), '12');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_year > option[selected]')->attr('value'), '2016');

        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponCode')->attr('value'), 'ADMINUPDATE');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponPercent')->attr('value'), '30');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponAmount')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_coupontype_couponQuantity')->attr('value'), '10');
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponQuantityLimited > option[selected]')->attr('value'), 'true');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponUnique > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponUnique > option[selected]')->attr('value'), 'true');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponFreeShipping > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponFreeShipping > option[selected]')->attr('value'), 'true');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_month > option[selected]')->attr('value'), '3');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_day > option[selected]')->attr('value'), '4');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateStart_year > option[selected]')->attr('value'), '2014');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_month > option[selected]')->attr('value'), '5');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_day > option[selected]')->attr('value'), '6');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_coupontype_couponDateEnd_year > option[selected]')->attr('value'), '2015');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/ADMINUPDATE/', $client->getResponse()->getContent());
        $this->assertNotRegExp('/ADMINTEST/', $client->getResponse()->getContent());
        $this->assertRegExp('/PERCENT/', $client->getResponse()->getContent());
    }
}
