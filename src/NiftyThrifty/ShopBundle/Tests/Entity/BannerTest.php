<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Banner;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerTypeData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class BannerTest extends WebTestCase
{
    public $testBanner;
    public $validator;
    public $em;
    public $container;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->em        = $kernel->getContainer()->get('doctrine')->getManager();
        $this->container = $kernel->getContainer();
        $this->testBanner = new Banner();
        
        // Copy files
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop.jpg ' .
         ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop_temp.jpg');
        system('cp /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel.jpg ' .
         ' /var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg');

        // Images we'll use.
        $this->bannerImage = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop_temp.jpg',
                                      'newslettertop_temp.jpg');
        $this->textFile    = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/testFile.txt', 
                                      'testFile.txt');
        
        $this->testBanner->setDescription('Test Banner')
                         ->setUrl('http://www.niftythrifty.com')
                         ->setBannerType('home_upper_right')
                         ->setIsDefault('yes')
                         ->setBannerImage($this->bannerImage)
                         ->setRotationStartTime(new \DateTime())
                         ->setRotationEndTime(new \DateTime());
    }

    public function tearDown()
    {
        // If these temp files still exist, remove them.
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/newslettertop_temp.jpg');
        @unlink('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg');

        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }
    
    public function testValidBanner()
    {
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testDescriptionBlank()
    {
        $this->testBanner->setDescription(null);
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Description can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'description');
    }
    
    public function testDescriptionTooLong()
    {
        $this->testBanner->setDescription(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Description must be less than 50 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'description');
    }
    
    public function testBlankUrlPasses()
    {
        $this->testBanner->setUrl(null);
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testUrlNotURL()
    {
        $this->testBanner->setUrl('dinkers');
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'url is not valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'url');
    }
    
    public function testUrlTooLong()
    {
        $this->testBanner->setUrl('http://www.niftythrifty.com/' . str_repeat('x', 255));
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Url must be less than 255 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'url');
    }
    
    public function testBannerTypeTooLong()
    {
        $this->testBanner->setBannerType(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(2, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Banner type must be less than 255 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'bannerType');
        $this->assertEquals($violationList[1]->getMessage(),        'Banner type not selected.');
        $this->assertEquals($violationList[1]->getPropertyPath(),   'bannerType');
    }
    
    public function testBannerTypeInvalidChoice()
    {
        $this->testBanner->setBannerType('dinkers');
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Banner type not selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'bannerType');
    }
    
    public function testIsDefaultNoPass()
    {
        $this->testBanner->setIsDefault('no');
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testIsDefaultBlank()
    {
        $this->testBanner->setIsDefault(null);
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Default status can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'isDefault');
    }
    
    public function testIsDefaultInvalidChoice()
    {
        $this->testBanner->setIsDefault('dinkers');
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Default status must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'isDefault');
    }
    
    public function testBannerImageNotImage()
    {
        $this->testBanner->setBannerImage($this->textFile);
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Banner image is not an image file.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'bannerImage');
    }
    
    public function testRotationStartTimeBlank()
    {
        $this->testBanner->setRotationStartTime(null);
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Start time must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'rotationStartTime');
    }
    
    public function testRotationStartTimeNotTimestamp()
    {
        $this->testBanner->setRotationStartTime('dinkers');
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Start time is not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'rotationStartTime');
    }
    
    public function testRotationEndTimeBlank()
    {
        $this->testBanner->setRotationEndTime(null);
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'End time must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'rotationEndTime');
    }
    
    public function testRotationEndTimeNotTimestamp()
    {
        $this->testBanner->setRotationEndTime('dinkers');
        $violationList = $this->validator->validate($this->testBanner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'End time is not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'rotationEndTime');
    }
    
    public function testGetId()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new BannerData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);
        $this->assertEquals($banner->getId(),       1);
        $this->assertEquals($banner->getBannerId(), 1);
    }
    
    public function testAssociationBannerTypeEntity()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new BannerData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\BannerType', $banner->getBannerTypeEntity());
        $this->assertEquals($banner->getBannerType(), 'home_upper_right');
        $this->assertEquals($banner->getBannerType(), $banner->getBannerTypeEntity()->getName());
    }

    /**
     * Test creating, updating, and editing all the banner image fields.
     *
     * @covers  Banner::processImages
     * @covers  Banner::upload
     * @covers  Banner::checkImages
     * @covers  Banner::checkUpload
     * @covers  Banner::deleteFile
     */
    public function testBannerImageProcessing()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new BannerTypeData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        $filepath   = '/var/www/Symfony/web/';
        $year       = date("Y");
        $month      = date("m");

        $bannerTypeEntity = $this->em->getRepository('NiftyThriftyShopBundle:BannerType')->find($this->testBanner->getBannerType());
                
        // Persist banner, test that the files were created.
        $this->testBanner->setBannerTypeEntity($bannerTypeEntity);
        $this->em->persist($this->testBanner);
        $this->em->flush();

        $this->assertEquals(2, sizeof($this->testBanner->getFiles()));
        $this->assertTrue(array_key_exists('fileBannerImage', $this->testBanner->getFiles()));
        $this->assertTrue(array_key_exists('tempBannerImage', $this->testBanner->getFiles()));
        $this->assertContains("images/uploads/$year/$month",    $this->testBanner->getBannerImage());
        $this->assertFileExists($filepath . $this->testBanner->getBannerImage());
        $this->em->clear();
        
        // Fetch the Banner, update the files and make sure things were updated and deleted.
        $updateImage  = new File('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg', 
                                 'collectionpanel_temp.jpg');
        $this->assertFileExists('/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/collectionpanel_temp.jpg');
        $banner  = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);

        // Test the file array is set properly
        $this->assertEquals(0, sizeof($banner->getFiles()));
        $this->assertEquals($banner->getBannerImage(),  $this->testBanner->getBannerImage());
        $banner->setBannerImage($updateImage);
        $this->assertEquals(1, sizeof($banner->getFiles()));
        $this->assertTrue(array_key_exists('oldBannerImage', $banner->getFiles()));
        $this->em->flush();
        
        // The old files should be deleted.  The new files should exist.
        $this->assertNotEquals($banner->getBannerImage(),  $this->testBanner->getBannerImage());
        $this->assertFileExists($filepath . $banner->getBannerImage());
        $this->assertFileNotExists($filepath . $this->testBanner->getBannerImage());
        
        // Delete the entity, the old files should be gone.
        $this->em->remove($banner);
        $this->em->flush();
        $this->assertFileNotExists($filepath . $banner->getBannerImage());
        
        // Verify the banner was deleted
        $this->em->clear();
        $deletedBanner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);
        $this->assertNull($deletedBanner);
    }
}
