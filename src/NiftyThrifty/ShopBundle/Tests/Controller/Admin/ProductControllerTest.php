<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
//use NiftyThrifty\ShopBundle\Form\Type\Admin\ProductType;

class ProductControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;

    public function __construct()
    {
        $this->settings = array("niftythrifty_shopbundle_producttype[productCode]"                          => 'TST1',
                                "niftythrifty_shopbundle_producttype[collection]"                           => 1,
                                "niftythrifty_shopbundle_producttype[productName]"                          => 'Admin Product',
                                "niftythrifty_shopbundle_producttype[designer]"                             => 1,
                                "niftythrifty_shopbundle_producttype[productDescription]"                   => 'New product via admin',
                                "niftythrifty_shopbundle_producttype[productTagsize]"                       => 'Large',
                                "niftythrifty_shopbundle_producttype[productCategorySize]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productMeasurements]"                  => '35", 25", 39"',
                                "niftythrifty_shopbundle_producttype[productPrice]"                         => '55',
                                "niftythrifty_shopbundle_producttype[productOldPrice]"                      => null,
                                "niftythrifty_shopbundle_producttype[productOverallCondition]"              => 'Good',
                                "niftythrifty_shopbundle_producttype[productDiscount]"                      => null,
                                "niftythrifty_shopbundle_producttype[productDetailedConditionValue]"        => 4,
                                "niftythrifty_shopbundle_producttype[productDetailedConditionDescription]"  => 'Spiffy',
                                "niftythrifty_shopbundle_producttype[productFabric]"                        => 'Cotton',
                                "niftythrifty_shopbundle_producttype[productAvailability]"                  => 'sale',
                                "niftythrifty_shopbundle_producttype[productHeavy]"                         => 'no',
                                "niftythrifty_shopbundle_producttype[productVisual1Large]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productVisual2Large]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productVisual3Large]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productTaxes]"                         => 3.5,
                                "niftythrifty_shopbundle_producttype[productTaxesActive]"                   => 'no');

        $this->update   = array("niftythrifty_shopbundle_producttype[productCode]"                          => 'TST2',
                                "niftythrifty_shopbundle_producttype[collection]"                           => 2,
                                "niftythrifty_shopbundle_producttype[productName]"                          => 'Admin Product edit',
                                "niftythrifty_shopbundle_producttype[designer]"                             => 2,
                                "niftythrifty_shopbundle_producttype[productDescription]"                   => 'New product via admin edit',
                                "niftythrifty_shopbundle_producttype[productTagsize]"                       => 'Small',
                                "niftythrifty_shopbundle_producttype[productCategorySize]"                  => 2,
                                "niftythrifty_shopbundle_producttype[productMeasurements]"                  => '36", 26", 36"',
                                "niftythrifty_shopbundle_producttype[productPrice]"                         => '45',
                                "niftythrifty_shopbundle_producttype[productOldPrice]"                      => '55',
                                "niftythrifty_shopbundle_producttype[productOverallCondition]"              => 'Bad',
                                "niftythrifty_shopbundle_producttype[productDiscount]"                      => '5',
                                "niftythrifty_shopbundle_producttype[productDetailedConditionValue]"        => 2,
                                "niftythrifty_shopbundle_producttype[productDetailedConditionDescription]"  => 'Not Spiffy',
                                "niftythrifty_shopbundle_producttype[productFabric]"                        => 'Rags',
                                "niftythrifty_shopbundle_producttype[productAvailability]"                  => 'sale',
                                "niftythrifty_shopbundle_producttype[productHeavy]"                         => 'yes',
                                "niftythrifty_shopbundle_producttype[productVisual1Large]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productVisual2Large]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productVisual3Large]"                  => 1,
                                "niftythrifty_shopbundle_producttype[productTaxes]"                         => 8.375,
                                "niftythrifty_shopbundle_producttype[productTaxesActive]"                   => 'no');
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new ProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/product_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new Product')->link());

        // Check the defined pre-selections
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productOverallCondition')->attr('value'), 'Vintage');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDetailedConditionValue')->attr('value'), '4');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDetailedConditionDescription')->attr('value'), 'Good condition');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productHeavy > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productHeavy > option[selected]')->attr('value'), 'no');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productTaxes')->attr('value'), '8.875');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productTaxesActive > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productTaxesActive > option[selected]')->attr('value'), 'yes');

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $form['niftythrifty_shopbundle_producttype[productTags][1]']->tick();
        $form['niftythrifty_shopbundle_producttype[productTags][3]']->tick();
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();

        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Productid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   10);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Productname');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin Product');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Productdescription');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'New product via admin');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Productcategorysizeid');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   1);
        $this->assertEquals($children->eq(4)->filter('label')->text(),  'Producttypeid');
        $this->assertEquals($children->eq(4)->filter('span')->text(),   '');
        $this->assertEquals($children->eq(5)->filter('label')->text(),  'Productoverallcondition');
        $this->assertEquals($children->eq(5)->filter('span')->text(),   'Good');
        $this->assertEquals($children->eq(6)->filter('label')->text(),  'Productprice');
        $this->assertEquals($children->eq(6)->filter('span')->text(),   55);
        $this->assertEquals($children->eq(7)->filter('label')->text(),  'Productoldprice');
        $this->assertEquals($children->eq(7)->filter('span')->text(),   '');
        $this->assertEquals($children->eq(8)->filter('label')->text(),  'Productdiscount');
        $this->assertEquals($children->eq(8)->filter('span')->text(),   '');
        $this->assertEquals($children->eq(9)->filter('label')->text(),  'Productdetailedconditionvalue');
        $this->assertEquals($children->eq(9)->filter('span')->text(),   4);
        $this->assertEquals($children->eq(10)->filter('label')->text(), 'Productdetailedconditiondescription');
        $this->assertEquals($children->eq(10)->filter('span')->text(),  'Spiffy');
        $this->assertEquals($children->eq(11)->filter('label')->text(), 'Productfabric');
        $this->assertEquals($children->eq(11)->filter('span')->text(),  'Cotton');
        $this->assertEquals($children->eq(12)->filter('label')->text(), 'Productmeasurements');
        $this->assertEquals($children->eq(12)->filter('span')->text(),  '35", 25", 39"');
        $this->assertEquals($children->eq(13)->filter('label')->text(), 'Productavailability');
        $this->assertEquals($children->eq(13)->filter('span')->text(),  'sale');
        $this->assertEquals($children->eq(14)->filter('label')->text(), 'Productheavy');
        $this->assertEquals($children->eq(14)->filter('span')->text(),  'no');
        $this->assertEquals($children->eq(15)->filter('label')->text(), 'Productvisual1');
        $this->assertEquals($children->eq(15)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(16)->filter('label')->text(), 'Productvisual1large');
        $this->assertEquals($children->eq(16)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(17)->filter('label')->text(), 'Productvisual2');
        $this->assertEquals($children->eq(17)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(18)->filter('label')->text(), 'Productvisual2large');
        $this->assertEquals($children->eq(18)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(19)->filter('label')->text(), 'Productvisual3');
        $this->assertEquals($children->eq(19)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(20)->filter('label')->text(), 'Productvisual3large');
        $this->assertEquals($children->eq(20)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(21)->filter('label')->text(), 'Collectionid');
        $this->assertEquals($children->eq(21)->filter('span')->text(),  1);
        $this->assertEquals($children->eq(22)->filter('label')->text(), 'Designerid');
        $this->assertEquals($children->eq(22)->filter('span')->text(),  1);
        $this->assertEquals($children->eq(23)->filter('label')->text(), 'Producthashtag');
        $this->assertEquals($children->eq(23)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(24)->filter('label')->text(), 'Productinstagrammediaidnifty');
        $this->assertEquals($children->eq(24)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(25)->filter('label')->text(), 'Productinstagrammediaidcustomer');
        $this->assertEquals($children->eq(25)->filter('span')->text(),  '');
        $this->assertEquals($children->eq(26)->filter('label')->text(), 'Producttaxes');
        $this->assertEquals($children->eq(26)->filter('span')->text(),  '3.5');
        $this->assertEquals($children->eq(27)->filter('label')->text(), 'Producttaxesactive');
        $this->assertEquals($children->eq(27)->filter('span')->text(),  'no');
        $this->assertEquals($children->eq(28)->filter('label')->text(), 'Productcode');
        $this->assertEquals($children->eq(28)->filter('span')->text(),  'TST1');
        $this->assertEquals($children->eq(29)->filter('label')->text(), 'Producttagsize');
        $this->assertEquals($children->eq(29)->filter('span')->text(),  'Large');
        
        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productCode')->attr('value'), 'TST1');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_collection > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_collection > option[selected]')->attr('value'), 1);
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productName')->attr('value'), 'Admin Product');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_designer > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_designer > option[selected]')->attr('value'), 1);
        $this->assertEquals($crawler->filter('textarea#niftythrifty_shopbundle_producttype_productDescription')->text(), 'New product via admin');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productTagsize')->attr('value'), 'Large');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productCategorySize > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productCategorySize > option[selected]')->attr('value'), 1);
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productMeasurements')->attr('value'), '35", 25", 39"');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productPrice')->attr('value'), '55');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productOldPrice')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productOverallCondition')->attr('value'), 'Good');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDiscount')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDetailedConditionValue')->attr('value'), '4');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDetailedConditionDescription')->attr('value'), 'Spiffy');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productFabric')->attr('value'), 'Cotton');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productAvailability > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productAvailability > option[selected]')->attr('value'), 'sale');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productHeavy > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productHeavy > option[selected]')->attr('value'), 'no');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productVisual1Large')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productVisual2Large')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productVisual3Large')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productTaxes')->attr('value'), '3.5');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productTaxesActive > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productTaxesActive > option[selected]')->attr('value'), 'no');
        $this->assertCount(1, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_1[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_2[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_3[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_4[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_5[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_6[checked]'));
        $this->assertCount(1, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_7[checked]'));

        // Submit the edit
        $form = $crawler->selectButton('Edit')->form($this->update);
        $form['niftythrifty_shopbundle_producttype[productTags][1]']->untick();
        $form['niftythrifty_shopbundle_producttype[productTags][3]']->untick();
        $form['niftythrifty_shopbundle_producttype[productTags][2]']->tick();
        $form['niftythrifty_shopbundle_producttype[productTags][4]']->tick();
        $client->submit($form);
        $crawler = $client->followRedirect();

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productCode')->attr('value'), 'TST2');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_collection > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_collection > option[selected]')->attr('value'), 2);
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productName')->attr('value'), 'Admin Product edit');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_designer > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_designer > option[selected]')->attr('value'), 2);
        $this->assertEquals($crawler->filter('textarea#niftythrifty_shopbundle_producttype_productDescription')->text(), 'New product via admin edit');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productTagsize')->attr('value'), 'Small');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productCategorySize > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productCategorySize > option[selected]')->attr('value'), 2);
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productMeasurements')->attr('value'), '36", 26", 36"');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productPrice')->attr('value'), '45');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productOldPrice')->attr('value'), '55');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productOverallCondition')->attr('value'), 'Bad');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDiscount')->attr('value'), '5');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDetailedConditionValue')->attr('value'), '2');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productDetailedConditionDescription')->attr('value'), 'Not Spiffy');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productFabric')->attr('value'), 'Rags');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productAvailability > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productAvailability > option[selected]')->attr('value'), 'sale');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productHeavy > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productHeavy > option[selected]')->attr('value'), 'yes');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productVisual1Large')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productVisual2Large')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productVisual3Large')->attr('value'), '');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_producttype_productTaxes')->attr('value'), '8.375');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_producttype_productTaxesActive > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_producttype_productTaxesActive > option[selected]')->attr('value'), 'no');
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_1[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_2[checked]'));
        $this->assertCount(1, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_3[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_4[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_5[checked]'));
        $this->assertCount(1, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_6[checked]'));
        $this->assertCount(0, $crawler->filter('input#niftythrifty_shopbundle_producttype_productTags_7[checked]'));

        // Click delete
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted on the list
        $this->assertNotRegExp('/Admin Product/', $client->getResponse()->getContent());
    }
}
