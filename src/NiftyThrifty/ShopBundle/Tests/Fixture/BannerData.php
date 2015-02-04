<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Banner;

class BannerData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\BannerTypeData');
    }

    public function load(ObjectManager $manager)
    {
        // Active banner with a URL.
        $banner1 = new Banner();
        $startTime1 = new \DateTime();
        $startTime1->modify('-5 days');
        $endTime1 = new \DateTime();
        $endTime1->modify('+5 days');
        $banner1->setDescription('Active banner with URL')
                ->setUrl('https://www.niftythrifty.com/shop/show_tag/1')
                ->setBannerImage('test')
                ->setBannerType('home_upper_right')
                ->setIsDefault('no')
                ->setRotationStartTime($startTime1)
                ->setRotationEndTime($endTime1)
                ->setBannerTypeEntity($this->getReference('banner-type-1'));
        $manager->persist($banner1);

        // Active banner without a URL.
        $banner2 = new Banner();
        $startTime2 = new \DateTime();
        $startTime2->modify('-5 days');
        $endTime2 = new \DateTime();
        $endTime2->modify('+5 days');
        $banner2->setDescription('Active banner without URL')
                ->setBannerType('top_promotion')
                ->setBannerImage('test')
                ->setIsDefault('yes')
                ->setRotationStartTime($startTime2)
                ->setRotationEndTime($endTime2)
                ->setBannerTypeEntity($this->getReference('banner-type-2'));
        $manager->persist($banner2);

        // Expired banner.
        $banner3 = new Banner();
        $startTime3 = new \DateTime();
        $startTime3->modify('-10 days');
        $endTime3 = new \DateTime();
        $endTime3->modify('-5 days');
        $banner3->setDescription('Inactive banner expired')
                ->setUrl('https://www.niftythrifty.com/shop/show_tag/2')
                ->setBannerImage('test')
                ->setIsDefault('no')
                ->setBannerType('home_upper_right')
                ->setRotationStartTime($startTime3)
                ->setRotationEndTime($endTime3)
                ->setBannerTypeEntity($this->getReference('banner-type-1'));
        $manager->persist($banner3);

        // Banner that isn't live yet.
        $banner4 = new Banner();
        $startTime4 = new \DateTime();
        $startTime4->modify('+2 days');
        $endTime4 = new \DateTime();
        $endTime4->modify('+5 days');
        $banner4->setDescription('Banner that is not active')
                ->setUrl('https://www.niftythrifty.com/shop/show_tag/3')
                ->setBannerImage('test')
                ->setBannerType('home_upper_right')
                ->setIsDefault('no')
                ->setRotationStartTime($startTime4)
                ->setRotationEndTime($endTime4)
                ->setBannerTypeEntity($this->getReference('banner-type-1'));
        $manager->persist($banner4);

        // Default banner
        $banner5 = new Banner();
        $startTime5 = new \DateTime();
        $startTime5->modify('-40 days');
        $endTime5 = new \DateTime();
        $endTime5->modify('-35 days');
        $banner5->setDescription('Default banner')
                ->setUrl('https://www.niftythrifty.com/shop/default')
                ->setBannerImage('test')
                ->setIsDefault('yes')
                ->setBannerType('home_upper_right')
                ->setRotationStartTime($startTime5)
                ->setRotationEndTime($endTime5)
                ->setBannerTypeEntity($this->getReference('banner-type-1'));
        $manager->persist($banner5);

        $manager->flush();
    }
}
