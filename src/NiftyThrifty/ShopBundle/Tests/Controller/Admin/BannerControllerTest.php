<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerTypeData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class BannerControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;
    public $testFile;
    public $createdFiles;
    
    public function __construct()
    {
        $imagePath  = '/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop.jpg';

        $this->createdFiles = array();
        $this->testTop      = new File($imagePath, 'newslettertop.jpg');
        $this->testEdit     = new File($imagePath, 'newslettertop.jpg');
        $this->settings = array("niftythrifty_shopbundle_bannertype[description]"                       => 'New description',
                                "niftythrifty_shopbundle_bannertype[url]"                               => 'http://www.niftythrifty.com',
                                "niftythrifty_shopbundle_bannertype[bannerImage]"                       => $this->testTop,
                                "niftythrifty_shopbundle_bannertype[isDefault]"                         => 'yes',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][date][month]"    => '4',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][date][day]"      => '15',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][date][year]"     => '2015',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][time][hour]"     => '11',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][time][minute]"   => '15',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][date][month]"      => '5',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][date][day]"        => '16',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][date][year]"       => '2017',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][time][hour]"       => '12',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][time][minute]"     => '20',
                                "niftythrifty_shopbundle_bannertype[bannerTypeEntity]"                  => 'home_upper_right');

        /**
         * the update here should do the normal stuff, but leave collection image alone, 
         * change the product 1 image, and delete the product 2 image.
         */
        $this->update   = array("niftythrifty_shopbundle_bannertype[description]"                       => 'Edited description',
                                "niftythrifty_shopbundle_bannertype[url]"                               => null,
                                "niftythrifty_shopbundle_bannertype[bannerImage]"                       => $this->testEdit,
                                "niftythrifty_shopbundle_bannertype[isDefault]"                         => 'no',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][date][month]"    => '3',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][date][day]"      => '14',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][date][year]"     => '2014',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][time][hour]"     => '2',
                                "niftythrifty_shopbundle_bannertype[rotationStartTime][time][minute]"   => '12',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][date][month]"      => '2',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][date][day]"        => '17',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][date][year]"       => '2018',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][time][hour]"       => '11',
                                "niftythrifty_shopbundle_bannertype[rotationEndTime][time][minute]"     => '12',
                                "niftythrifty_shopbundle_bannertype[bannerTypeEntity]"                  => 'top_promotion');
    }
    
    /**
     * Delete any files we created that weren't cleaned up automatically.
     */
    public function tearDown()
    {
        $path = '/var/www/Symfony/web/';
        foreach ($this->createdFiles as $filepath) {
            @unlink($path . $filepath);
        }
    }

    public function testCompleteScenario()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/banner_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new Banner')->link());

        // Check the defined pre-selections
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_bannerTypeEntity > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_bannerTypeEntity > option[selected]')->attr('value'), 'home_upper_right');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_isDefault > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_isDefault > option[selected]')->attr('value'), 'no');

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();
        
        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();

        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Bannerid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   6);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Description');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'New description');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Url');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'http://www.niftythrifty.com');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Isdefault');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   'yes');
        $this->assertEquals($children->eq(4)->filter('label')->text(),  'Bannerimage');
        $this->assertContains('images/uploads', $children->eq(4)->filter('span')->text());
        $this->update['existing_niftythrifty_shopbundle_bannertype[bannerImage]'] = $children->eq(4)->filter('span')->text();
        $this->createdFiles[] = $children->eq(4)->filter('span')->text();
        $this->assertFileExists('/var/www/Symfony/web/' . $this->createdFiles[0]);
        $this->assertEquals($children->eq(5)->filter('label')->text(),  'Bannertype');
        $this->assertEquals($children->eq(5)->filter('span')->text(),   'home_upper_right');
        $this->assertEquals($children->eq(6)->filter('label')->text(),  'Rotationstarttime');
        $this->assertEquals($children->eq(6)->filter('span')->text(),   '2015-04-15 11:15:00');
        $this->assertEquals($children->eq(7)->filter('label')->text(),  'Rotationendtime');
        $this->assertEquals($children->eq(7)->filter('span')->text(),   '2017-05-16 12:20:00');
        $this->assertFileExists('/var/www/Symfony/web/' . $this->createdFiles[0]);


        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_bannertype_description')->attr('value'),            'New description');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_bannertype_url')->attr('value'),                    'http://www.niftythrifty.com');
        $this->assertEquals($crawler->filter('img#niftythrifty_shopbundle_bannertype_bannerImage_img')->attr('src'),            '/' . $this->createdFiles[0]);
        $this->assertEquals($crawler->filter('input#existing_niftythrifty_shopbundle_bannertype_bannerImage')->attr('value'),   $this->createdFiles[0]);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_isDefault > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_isDefault > option[selected]')->attr('value'),   'yes');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_month > option[selected]')->attr('value'),   4);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_day > option[selected]')->attr('value'),     15);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_year > option[selected]')->attr('value'),    2015);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_hour > option[selected]')->attr('value'),    11);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_minute > option[selected]')->attr('value'),  15);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_month > option[selected]')->attr('value'),     5);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_day > option[selected]')->attr('value'),       16);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_year > option[selected]')->attr('value'),      2017);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_hour > option[selected]')->attr('value'),      12);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_minute > option[selected]')->attr('value'),    20);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_bannerTypeEntity > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_bannerTypeEntity > option[selected]')->attr('value'), 'home_upper_right');

        // Submit the edit
        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();
        
        // Check files
        $newPath  = $crawler->filter('input#existing_niftythrifty_shopbundle_bannertype_bannerImage')->attr('value');
        $this->createdFiles[] = $newPath;
        $this->assertEquals(2, sizeof($this->createdFiles));
        $this->assertNotEquals($this->createdFiles[0], $this->createdFiles[1]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[0]);
        $this->assertFileExists('/var/www/Symfony/web/' . $this->createdFiles[1]);
        
        // check edited stuff.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_bannertype_description')->attr('value'),            'Edited description');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_bannertype_url')->attr('value'),                    '');
        $this->assertEquals($crawler->filter('img#niftythrifty_shopbundle_bannertype_bannerImage_img')->attr('src'),            '/' . $this->createdFiles[1]);
        $this->assertEquals($crawler->filter('input#existing_niftythrifty_shopbundle_bannertype_bannerImage')->attr('value'),   $this->createdFiles[1]);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_isDefault > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_isDefault > option[selected]')->attr('value'),   'no');
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_month > option[selected]')->attr('value'),   3);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_day > option[selected]')->attr('value'),     14);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_date_year > option[selected]')->attr('value'),    2014);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_hour > option[selected]')->attr('value'),    2);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationStartTime_time_minute > option[selected]')->attr('value'),  12);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_month > option[selected]')->attr('value'),     2);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_day > option[selected]')->attr('value'),       17);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_date_year > option[selected]')->attr('value'),      2018);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_hour > option[selected]')->attr('value'),      11);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_rotationEndTime_time_minute > option[selected]')->attr('value'),    12);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_bannertype_bannerTypeEntity > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_bannertype_bannerTypeEntity > option[selected]')->attr('value'), 'top_promotion');

        // Click delete
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Edited description/', $client->getResponse()->getContent());
        $this->assertNotRegExp('/New description/', $client->getResponse()->getContent());
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[0]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[1]);
    }
}
