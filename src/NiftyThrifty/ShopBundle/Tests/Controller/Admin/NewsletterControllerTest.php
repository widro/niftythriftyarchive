<?php

namespace NiftyThrifty\ShopBundle\Tests\Controller\Admin;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class NewsletterControllerTest extends NiftyBaseTestCase
{
    public $settings;
    public $update;
    public $testFile;
    public $createdFiles;

    public function __construct()
    {
        $imagePath  = '/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop.jpg';
        $imagePath2 = '/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom1.jpg';
        $imagePath3 = '/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom2.jpg';
        $this->createdFiles = array();
        $this->testTop      = new File($imagePath, 'newslettertop.jpg');
        $this->testProd1    = new File($imagePath, 'newslettertop.jpg');
        $this->testProd2    = new File($imagePath, 'newslettertop.jpg');
        $this->settings = array("niftythrifty_shopbundle_newslettertype[newsletterName]"                            => 'Admin create newsletter',
                                "niftythrifty_shopbundle_newslettertype[newsletterTitle]"                           => 'New newsletter',
                                "niftythrifty_shopbundle_newslettertype[newsletterLink]"                            => 'www.niftythrifty.com',
                                "niftythrifty_shopbundle_newslettertype[newsletterCollectionImg]"                   => $this->testTop,
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct1Img]"                     => $this->testProd1,
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct1Link]"                    => 'one.niftythrifty.com',
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct2Link]"                    => 'two.niftythrifty.com',
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct2Img]"                     => $this->testProd2,
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastId]"                         => 1,
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][date][month]"  => '4',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][date][day]"    => '15',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][date][year]"   => '2015',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][time][hour]"   => '11',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][time][minute]" => '15');

        /**
         * the update here should do the normal stuff, but leave collection image alone, 
         * change the product 1 image, and delete the product 2 image.
         */
        $this->update   = array("niftythrifty_shopbundle_newslettertype[newsletterName]"                            => 'Admin create newsletter edit',
                                "niftythrifty_shopbundle_newslettertype[newsletterTitle]"                           => 'Edit newsletter',
                                "niftythrifty_shopbundle_newslettertype[newsletterLink]"                            => 'staging.niftythrifty.com',
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct1Img]"                     => $this->testProd2,
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct1Link]"                    => 'oneedit.niftythrifty.com',
                                "niftythrifty_shopbundle_newslettertype[newsletterProduct2Link]"                    => 'twoedit.niftythrifty.com',
                                "delete_niftythrifty_shopbundle_newslettertype[newsletterProduct2Img]"              => 1,
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastId]"                         => 2,
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][date][month]"  => '5',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][date][day]"    => '16',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][date][year]"   => '2016',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][time][hour]"   => '13',
                                "niftythrifty_shopbundle_newslettertype[newsletterBlastScheduleTime][time][minute]" => '10');
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
        $this->executeFixtures();
        $client = $this->getLoggedInAdminTestClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/admin/newsletter_admin/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new Newsletter')->link());

        // Default
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterLink')->attr('value'), 'https://www.niftythrifty.com');

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form($this->settings);
        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $children = $crawler->filter('fieldset')->children();

        $this->assertEquals($children->eq(0)->filter('label')->text(),  'Newsletterid');
        $this->assertEquals($children->eq(0)->filter('span')->text(),   1);
        $this->assertEquals($children->eq(1)->filter('label')->text(),  'Newslettername');
        $this->assertEquals($children->eq(1)->filter('span')->text(),   'Admin create newsletter');
        $this->assertEquals($children->eq(2)->filter('label')->text(),  'Newslettertitle');
        $this->assertEquals($children->eq(2)->filter('span')->text(),   'New newsletter');
        $this->assertEquals($children->eq(3)->filter('label')->text(),  'Newsletterlink');
        $this->assertEquals($children->eq(3)->filter('span')->text(),   'http://www.niftythrifty.com');
        
        $this->assertEquals($children->eq(4)->filter('label')->text(),  'Newslettercollectionimg');
        $this->assertContains('images/uploads', $children->eq(4)->filter('span')->text());
        $this->update['existing_niftythrifty_shopbundle_newslettertype[newsletterCollectionImg]'] = $children->eq(4)->filter('span')->text();
        $this->createdFiles[] = $children->eq(4)->filter('span')->text();
        $this->assertFileExists('/var/www/Symfony/web/' . $this->createdFiles[0]);

        $this->assertEquals($children->eq(5)->filter('label')->text(),  'Newsletterproduct1img');
        $this->assertContains('images/uploads', $children->eq(5)->filter('span')->text());
        $this->update['existing_niftythrifty_shopbundle_newslettertype[newsletterProduct1Img]'] = $children->eq(5)->filter('span')->text();
        $this->createdFiles[] = $children->eq(5)->filter('span')->text();
        $this->assertFileExists('/var/www/Symfony/web/' . $this->createdFiles[1]);

        $this->assertEquals($children->eq(7)->filter('label')->text(),  'Newsletterproduct2img');
        $this->assertContains('images/uploads', $children->eq(7)->filter('span')->text());
        $this->update['existing_niftythrifty_shopbundle_newslettertype[newsletterProduct2Img]'] = $children->eq(7)->filter('span')->text();
        $this->createdFiles[] = $children->eq(7)->filter('span')->text();
        $this->assertFileExists('/var/www/Symfony/web/' . $this->createdFiles[2]);

        $this->assertEquals($children->eq(6)->filter('label')->text(),  'Newsletterproduct1link');
        $this->assertEquals($children->eq(6)->filter('span')->text(),   'http://one.niftythrifty.com');
        $this->assertEquals($children->eq(8)->filter('label')->text(),  'Newsletterproduct2link');
        $this->assertEquals($children->eq(8)->filter('span')->text(),   'http://two.niftythrifty.com');
        $this->assertEquals($children->eq(9)->filter('label')->text(),  'Newsletterblastid');
        $this->assertEquals($children->eq(9)->filter('span')->text(),   1);
        $this->assertEquals($children->eq(10)->filter('label')->text(),  'Newsletterblastscheduletime');
        $this->assertEquals($children->eq(10)->filter('span')->text(),   '2015-04-15 11:15:00');
        
        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterName')->attr('value'), 'Admin create newsletter');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterTitle')->attr('value'), 'New newsletter');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterLink')->attr('value'), 'http://www.niftythrifty.com');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterProduct1Link')->attr('value'), 'http://one.niftythrifty.com');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterProduct2Link')->attr('value'), 'http://two.niftythrifty.com');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterBlastId')->attr('value'), 1);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_month > option[selected]')->attr('value'), 4);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_day > option[selected]')->attr('value'), 15);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_year > option[selected]')->attr('value'), 2015);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_hour > option[selected]')->attr('value'), 11);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_minute > option[selected]')->attr('value'), 15);

        // Submit the edit
        $form = $crawler->selectButton('Edit')->form($this->update);
        $client->submit($form);
        $crawler = $client->followRedirect();
        
        // Check file maintenance
        $exPath  = $crawler->filter('input#existing_niftythrifty_shopbundle_newslettertype_newsletterCollectionImg')->attr('value');
        $newPath = $crawler->filter('input#existing_niftythrifty_shopbundle_newslettertype_newsletterProduct1Img')->attr('value');
        $this->createdFiles[] = $newPath;
        $this->assertEquals($exPath,        $this->createdFiles[0]);
        $this->assertNotEquals($newPath,    $this->createdFiles[1]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[1]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[2]);

        // check the proper stuff is preselected.
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterName')->attr('value'), 'Admin create newsletter edit');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterTitle')->attr('value'), 'Edit newsletter');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterLink')->attr('value'), 'http://staging.niftythrifty.com');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterProduct1Link')->attr('value'), 'http://oneedit.niftythrifty.com');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterProduct2Link')->attr('value'), 'http://twoedit.niftythrifty.com');
        $this->assertEquals($crawler->filter('input#niftythrifty_shopbundle_newslettertype_newsletterBlastId')->attr('value'), 2);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_month > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_month > option[selected]')->attr('value'), 5);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_day > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_day > option[selected]')->attr('value'), 16);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_year > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_date_year > option[selected]')->attr('value'), 2016);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_hour > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_hour > option[selected]')->attr('value'), 13);
        $this->assertCount(1, $crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_minute > option[selected]'));
        $this->assertEquals($crawler->filter('select#niftythrifty_shopbundle_newslettertype_newsletterBlastScheduleTime_time_minute > option[selected]')->attr('value'), 10);

        // Click delete
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been deleted on the list
        $this->assertNotRegExp('/Admin create newsletter/', $client->getResponse()->getContent());
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[0]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[1]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[2]);
        $this->assertFileNotExists('/var/www/Symfony/web/' . $this->createdFiles[3]);
    }
}
