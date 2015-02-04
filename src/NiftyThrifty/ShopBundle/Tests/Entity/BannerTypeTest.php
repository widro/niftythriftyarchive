<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\BannerType;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class BannerTypeTest extends WebTestCase
{
    public $testBannerType;
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
        $this->testBannerType = new BannerType();
        $this->testBannerType->setName('new_Promo');
    }
    
    public function testBannerTypeValid()
    {
        $violationList = $this->validator->validate($this->testBannerType);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testBannerTypeNameBlank()
    {
        $this->testBannerType->setName(null);
        $violationList = $this->validator->validate($this->testBannerType);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'name');
    }
    
    public function testBannerTypeNameTooLong()
    {
        $this->testBannerType->setName(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testBannerType);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Name must be less than 50 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'name');
    }
    
    public function testBannerTypeInvalidCharacters()
    {
        $this->testBannerType->setName('new stuff');
        $violationList = $this->validator->validate($this->testBannerType);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Name may only contain letters or underscores.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'name');
    }
    
    public function testGetId()
    {
        $this->assertEquals('new_Promo', $this->testBannerType->getId());
    }
    
    public function testToString()
    {
        $this->assertEquals('new_Promo', $this->testBannerType);
    }
    
    public function testAssociationBanners()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new BannerData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $bannerType = $this->em->getRepository('NiftyThriftyShopBundle:BannerType')->find('home_upper_right');
        $banners = $bannerType->getBanners();
        
        $this->assertCount(4, $banners);
        $this->assertEquals($banners[0]->getBannerId(), 5);
        $this->assertEquals($banners[1]->getBannerId(), 3);
        $this->assertEquals($banners[2]->getBannerId(), 1);
        $this->assertEquals($banners[3]->getBannerId(), 4);
    }
}
