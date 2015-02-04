<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Newsletter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class NewsletterTest extends WebTestCase
{
    public $testNewsletter;
    public $validator;
    public $em;
    public $container;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $nowTime              = new \DateTime();
        $this->validator      = $kernel->getContainer()->get('validator');
        $this->em             = $kernel->getContainer()->get('doctrine')->getManager();
        $this->container      = $kernel->getContainer();
        $this->testNewsletter = new Newsletter();

        // Make copies of the test files being used so we won't blow them when things are unlinked
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop.jpg ' .
                 ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop_temp.jpg');
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom1.jpg ' .
                 ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom1_temp.jpg');
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom2.jpg ' .
                 ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom2_temp.jpg');
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel.jpg ' .
                 ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg');
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionlargehero.jpg ' .
                 ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionlargehero_temp.jpg');
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpagehero.jpg ' .
                 ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpagehero_temp.jpg');

        /**
         * Create files we will use in most cases.  We will use the other files in only some cases 
         * so we'll create them as we need them.
         */
        $this->newsletterTopImage     = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop_temp.jpg',
                                                 'newslettertop_temp.jpg');
        $this->newsletterBottomImage  = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom1_temp.jpg',
                                                 'newsletterbottom1_temp.jpg');
        $this->newsletterBottom2Image = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom2_temp.jpg',
                                                 'newsletterbottom2_temp.jpg');
        $this->textFile               = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/testFile.txt', 'testFile.txt');

        $this->testNewsletter->setNewsletterName('Newsletter One')
                             ->setNewsletterTitle('Newsletter Title')
                             ->setNewsletterLink('http://www.niftythrifty.com')
                             ->setNewsletterCollectionImg($this->newsletterTopImage)
                             ->setNewsletterBlastId(1)
                             ->setNewsletterBlastScheduleTime($nowTime);
    }

    public function tearDown()
    {
        // If these temp files still exist, remove them.
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop_temp.jpg');
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom1_temp.jpg');
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newsletterbottom2_temp.jpg');
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg');
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionlargehero_temp.jpg');
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpagehero_temp.jpg');

        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidNewsletter()
    {
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testNewsletterNameBlank()
    {
        $this->testNewsletter->setNewsletterName(null);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Newsletter name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterName');
    }

    public function testNewsletterNameTooLong()
    {
        $this->testNewsletter->setNewsletterName(str_repeat('x', 65));
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Newsletter name must be less than 64 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterName');
    }

    public function testNewsletterImageNotAnImage()
    {
        $this->testNewsletter->setNewsletterCollectionImg($this->textFile);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Newsletter image is not an image file.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterCollectionImg');
    }
    
    public function testNewsletterLinkNotBlank()
    {
        $this->testNewsletter->setNewsletterLink(null);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Newsletter url can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterLink');
    }
    
    public function testNewsletterLinkBadUrl()
    {
        $this->testNewsletter->setNewsletterLink('tom');
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Newsletter link must be a valid URL.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterLink');
    }

    public function testProduct1ImageNotAnImage()
    {
        $this->testNewsletter->setNewsletterProduct1Img($this->textFile);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product 1 image is not an image file.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterProduct1Img');
    }
    
    public function testProduct1ValidImagePass()
    {
        $this->testNewsletter->setNewsletterProduct1Img($this->newsletterBottomImage);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testProduct2ImageNotAnImage()
    {
        $this->testNewsletter->setNewsletterProduct2Img($this->textFile);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product 2 image is not an image file.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterProduct2Img');
    }
    
    public function testProduct2ValidImagePass()
    {
        $this->testNewsletter->setNewsletterProduct2Img($this->newsletterBottom2Image);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testProduct1ValidLinkPass()
    {
        $this->testNewsletter->setNewsletterProduct1Link('http://www.niftythrifty.com');
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProduct1LinkBadLink()
    {
        $this->testNewsletter->setNewsletterProduct1Link('tom');
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product link 1 must be a valid URL.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterProduct1Link');
    }
    
    public function testProduct2ValidLinkPass()
    {
        $this->testNewsletter->setNewsletterProduct2Link('http://www.niftythrifty.com');
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProduct2LinkBadLink()
    {
        $this->testNewsletter->setNewsletterProduct2Link('tom');
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product link 2 must be a valid URL.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterProduct2Link');
    }
    
    public function testBlastScheduleTimeBlankPass()
    {
        $this->testNewsletter->setNewsletterBlastScheduleTime(null);
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testBlastScheduleTimeBadTime()
    {
        $this->testNewsletter->setNewsletterBlastScheduleTime('tom');
        $violationList = $this->validator->validate($this->testNewsletter);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Schedule time must be a valid date/time.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'newsletterBlastScheduleTime');
    }

    /**
     * Test creating, updating, and editing all the newsletter image fields.
     *
     * @covers  Newsletter::processImages
     * @covers  Newsletter::upload
     * @covers  Newsletter::checkImages
     * @covers  Newsletter::checkUpload
     * @covers  Newsletter::deleteFile
     */
    public function testNewsletterImageStuff()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        $filepath   = '/var/www/Symfony/web/';
        $year       = date("Y");
        $month      = date("m");
                
        // Presumes that setNewsletterCollectionImg($this->newsletterTopImage) was run in __construct or setUp
        $this->testNewsletter->setNewsletterProduct1Img($this->newsletterBottomImage)
                             ->setNewsletterProduct2Img($this->newsletterBottom2Image);
        $this->assertEquals(0, sizeof($this->testNewsletter->getFiles()));
        
        // Persist newsletter, test that the files were created.
        $this->em->persist($this->testNewsletter);
        $this->em->flush();
        $this->assertEquals(6, sizeof($this->testNewsletter->getFiles()));
        $this->assertTrue(array_key_exists('fileCollectionImg', $this->testNewsletter->getFiles()));
        $this->assertTrue(array_key_exists('fileProduct1Img',   $this->testNewsletter->getFiles()));
        $this->assertTrue(array_key_exists('fileProduct2Img',   $this->testNewsletter->getFiles()));
        $this->assertTrue(array_key_exists('tempCollectionImg', $this->testNewsletter->getFiles()));
        $this->assertTrue(array_key_exists('tempProduct1Img',   $this->testNewsletter->getFiles()));
        $this->assertTrue(array_key_exists('tempProduct2Img',   $this->testNewsletter->getFiles()));
        $this->assertContains("images/uploads/$year/$month",    $this->testNewsletter->getNewsletterCollectionImg());
        $this->assertContains("images/uploads/$year/$month",    $this->testNewsletter->getNewsletterProduct1Img());
        $this->assertContains("images/uploads/$year/$month",    $this->testNewsletter->getNewsletterProduct2Img());
        $this->assertFileExists($filepath . $this->testNewsletter->getNewsletterCollectionImg());
        $this->assertFileExists($filepath . $this->testNewsletter->getNewsletterProduct1Img());
        $this->assertFileExists($filepath . $this->testNewsletter->getNewsletterProduct2Img());
        $this->em->clear();
        
        // Fetch the Newsletter, update the files and make sure things were updated and deleted.
        $updateImage  = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg', 
                                 'collectionpanel_temp.jpg');
        $updateImage2 = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionlargehero_temp.jpg', 
                                 'collectionlargehero_temp.jpg');
        $updateImage3 = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpagehero_temp.jpg', 
                                 'collectionpagehero_temp.jpg');
        $this->assertFileExists('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg');
        $this->assertFileExists('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionlargehero_temp.jpg');
        $this->assertFileExists('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpagehero_temp.jpg');
        $newsletter  = $this->em->getRepository('NiftyThriftyShopBundle:Newsletter')->find(1);

        // Test the file array is set properly
        $this->assertEquals(0, sizeof($newsletter->getFiles()));
        $this->assertEquals($newsletter->getNewsletterCollectionImg(),  $this->testNewsletter->getNewsletterCollectionImg());
        $this->assertEquals($newsletter->getNewsletterProduct1Img(),    $this->testNewsletter->getNewsletterProduct1Img());
        $this->assertEquals($newsletter->getNewsletterProduct2Img(),    $this->testNewsletter->getNewsletterProduct2Img());
        $newsletter->setNewsletterCollectionImg($updateImage)
                   ->setNewsletterProduct1Img($updateImage2)
                   ->setNewsletterProduct2Img($updateImage3);
        $this->assertEquals(3, sizeof($newsletter->getFiles()));
        $this->assertTrue(array_key_exists('oldCollectionImg', $newsletter->getFiles()));
        $this->assertTrue(array_key_exists('oldProduct1Img',   $newsletter->getFiles()));
        $this->assertTrue(array_key_exists('oldProduct2Img',   $newsletter->getFiles()));
        $this->em->flush();
        
        // The old files should be deleted.  The new files should exist.
        $this->assertNotEquals($newsletter->getNewsletterCollectionImg(),  $this->testNewsletter->getNewsletterCollectionImg());
        $this->assertNotEquals($newsletter->getNewsletterProduct1Img(),    $this->testNewsletter->getNewsletterProduct1Img());
        $this->assertNotEquals($newsletter->getNewsletterProduct2Img(),    $this->testNewsletter->getNewsletterProduct2Img());
        $this->assertFileExists($filepath . $newsletter->getNewsletterCollectionImg());
        $this->assertFileExists($filepath . $newsletter->getNewsletterProduct1Img());
        $this->assertFileExists($filepath . $newsletter->getNewsletterProduct2Img());
        $this->assertFileNotExists($filepath . $this->testNewsletter->getNewsletterCollectionImg());
        $this->assertFileNotExists($filepath . $this->testNewsletter->getNewsletterProduct1Img());
        $this->assertFileNotExists($filepath . $this->testNewsletter->getNewsletterProduct2Img());
        
        // Delete the entity, the old files should be gone.
        $this->em->remove($newsletter);
        $this->em->flush();
        $this->assertFileNotExists($filepath . $newsletter->getNewsletterCollectionImg());
        $this->assertFileNotExists($filepath . $newsletter->getNewsletterProduct1Img());
        $this->assertFileNotExists($filepath . $newsletter->getNewsletterProduct2Img());
        
        // Verify the newsletter was deleted
        $this->em->clear();
        $deletedNewsletter = $this->em->getRepository('NiftyThriftyShopBundle:Newsletter')->find(1);
        $this->assertNull($deletedNewsletter);
    }
}
