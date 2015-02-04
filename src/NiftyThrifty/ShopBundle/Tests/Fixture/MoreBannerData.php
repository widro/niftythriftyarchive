<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Banner;

/**
 * Exists to test the randomization of multiple banners
 */
class MoreBannerData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\BannerData');
    }

    public function load(ObjectManager $manager)
    {
        // Active banner with a URL.
        $banner6 = new Banner();
        $startTime6 = new \DateTime();
        $startTime6->modify('-6 days');
        $endTime6 = new \DateTime();
        $endTime6->modify('+6 days');
        $banner6->setDescription('Additional active banner with URL')
                ->setUrl('https://www.niftythrifty.com/shop/additional')
                ->setBannerImage('test')
                ->setBannerType('home_upper_right')
                ->setIsDefault('no')
                ->setRotationStartTime($startTime6)
                ->setRotationEndTime($endTime6)
                ->setBannerTypeEntity($this->getReference('banner-type-1'));
        $manager->persist($banner6);

        // Expired banner, multiple defaults
        $banner7 = new Banner();
        $startTime7 = new \DateTime();
        $startTime7->modify('-5 days');
        $endTime7 = new \DateTime();
        $endTime7->modify('-10 days');
        $banner7->setDescription('Multiple default banner')
                ->setBannerType('top_promotion')
                ->setBannerImage('test')
                ->setIsDefault('yes')
                ->setRotationStartTime($startTime7)
                ->setRotationEndTime($endTime7)
                ->setBannerTypeEntity($this->getReference('banner-type-2'));
        $manager->persist($banner7);
        
        $manager->flush();
    }
}
