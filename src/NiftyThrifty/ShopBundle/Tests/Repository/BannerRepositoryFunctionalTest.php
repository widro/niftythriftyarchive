<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;
use NiftyThrifty\ShopBundle\Tests\Fixture\MoreBannerData;

class BannerRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * Test the case with a single banner in the window with no default.
     */
    public function testFindDisplayByBannerTypeSingle()
    {
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(2);
        $banner->setIsDefault('no');
        $this->em->flush();
        $this->em->clear();
        
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findDisplayBannerByType('top_promotion');
        
        $this->assertEquals($banner->getBannerId(),     2);
        $this->assertEquals($banner->getIsDefault(),    'no');
    }
    
    /**
     * Test single banner in the banner if the default is also yes.
     */
    public function testFindDisplayByBannerTypeSingleWithDefault()
    {
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findDisplayBannerByType('top_promotion');
        
        $this->assertEquals($banner->getBannerId(),     2);
        $this->assertEquals($banner->getIsDefault(),    'yes');
    }
    
    /**
     * Test returning a single banner with multiple valid banners
     */
    public function testFindDisplayByBannerTypeMultiple()
    {
        $this->addFixture(new MoreBannerData);
        $this->executeFixtures();
        $banners = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findByBannerType('home_upper_right');
        $this->assertTrue(sizeof($banners) > 0);
        
        $bannersFound = array();
        
        // Run through the banner finder a bunch of times and presume both will be returned.
        for ($i=0; $i<100; $i++) {
            $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findDisplayBannerByType('home_upper_right');
            if (!(in_array($banner->getBannerId(), $bannersFound))) $bannersFound[] = $banner->getBannerId();
        }

        $this->assertTrue(in_array(1, $bannersFound));
        $this->assertTrue(in_array(6, $bannersFound));
    }
    
    /**
     * Test returning a banner that is expired but is the default.
     */
    public function testFindDisplayByBannerTypeGetsDefault()
    {
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(2);
        $expireTime = new \DateTime();
        $expireTime->modify("-4 days");
        $banner->setRotationEndTime($expireTime);
        $this->em->flush();
        $this->em->clear();
        
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findDisplayBannerByType('top_promotion');
        $nowTime = new \DateTime();

        $this->assertEquals($banner->getBannerId(), 2);
        $this->assertEquals($banner->getIsDefault(), 'yes');
        $this->assertTrue($nowTime > $banner->getRotationStartTime());
        $this->assertTrue($nowTime > $banner->getRotationEndTime());
    }
    
    public function testFindDefaultDisplayBannerTypeSingle()
    {
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findDefaultDisplayBannerByType('home_upper_right');

        $this->assertEquals($banner->getBannerId(), 5);
        $this->assertEquals($banner->getIsDefault(), 'yes');
    }
    
    public function testFindDefaultDisplayBannerTypeMultiple()
    {
        $this->addFixture(new MoreBannerData);
        $this->executeFixtures();

        $bannersFound = array();
        for ($i=0; $i<100; $i++) {
            $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findDefaultDisplayBannerByType('top_promotion');
            if (!(in_array($banner->getBannerId(), $bannersFound))) $bannersFound[] = $banner->getBannerId();
        }

        $this->assertTrue(in_array(2, $bannersFound));
        $this->assertTrue(in_array(7, $bannersFound));
    }
    
    public function testReturnRandomBannerSizeZero()
    {
        $banners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->assertNull($this->em->getRepository('NiftyThriftyShopBundle:Banner')->returnRandomBanner($banners));
    }
    
    public function testReturnRandomBannerSizeOne()
    {
        $this->addFixture(new BannerData);
        $this->executeFixtures();

        $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->find(1);
        $banners = new \Doctrine\Common\Collections\ArrayCollection();
        $banners->add($banner);
        $this->assertCount(1, $banners);

        $returnBanner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->returnRandomBanner($banners);
        $this->assertEquals(1, $returnBanner->getBannerId());
    }
    
    public function testReturnRandomBannerSizeTwo()
    {
        $this->addFixture(new MoreBannerData);
        $this->executeFixtures();
        $banners = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->findByBannerType('home_upper_right');
        $this->assertCount(5, $banners);
        $bannersFound = array();
        
        // Run through the banner finder a bunch of times and presume both will be returned.
        for ($i=0; $i<100; $i++) {
            $banner = $this->em->getRepository('NiftyThriftyShopBundle:Banner')->returnRandomBanner($banners);
            if (!(in_array($banner->getBannerId(), $bannersFound))) $bannersFound[] = $banner->getBannerId();
        }

        $this->assertTrue(in_array(1, $bannersFound));
        $this->assertTrue(in_array(3, $bannersFound));
        $this->assertTrue(in_array(4, $bannersFound));
        $this->assertTrue(in_array(5, $bannersFound));
        $this->assertTrue(in_array(6, $bannersFound));
    }
}
