<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use NiftyThrifty\ShopBundle\Entity\BannerType;

class BannerTypeData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $bannerType1 = new BannerType();
        $bannerType1->setName('home_upper_right');
        $this->addReference('banner-type-1', $bannerType1);
        $manager->persist($bannerType1);

        $bannerType2 = new BannerType();
        $bannerType2->setName('top_promotion', $bannerType2);
        $this->addReference('banner-type-2', $bannerType2);
        $manager->persist($bannerType2);

        $manager->flush();
    }
}
