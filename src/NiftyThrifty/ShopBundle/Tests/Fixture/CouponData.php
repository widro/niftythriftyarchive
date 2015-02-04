<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Coupon;

class CouponData extends AbstractFixture 
{
    public function load(ObjectManager $manager)
    {
        $nowTime    = new \DateTime();
        $expireTime = new \DateTime();
        $laterTime  = new \DateTime();
        $expireTime->modify("-10 days");
        $laterTime->modify("+10 days");


        $coupon1 = new Coupon();
        $coupon1->setCouponCode('PERCENT')
                ->setCouponPercent(25)
                ->setCouponQuantityLimited('false')
                ->setCouponUnique('false')
                ->setCouponDateAdd($nowTime)
                ->setCouponDateStart($nowTime)
                ->setCouponDateEnd($laterTime)
                ->setCouponFreeShipping('false');
        $manager->persist($coupon1);
        $this->addReference('coupon-1', $coupon1);

        $coupon2 = new Coupon();
        $coupon2->setCouponCode('AMOUNT')
                ->setCouponAmount(35)
                ->setCouponQuantityLimited('false')
                ->setCouponUnique('false')
                ->setCouponDateAdd($nowTime)
                ->setCouponDateStart($nowTime)
                ->setCouponDateEnd($laterTime)
                ->setCouponFreeShipping('false');
        $manager->persist($coupon2);

        $coupon3 = new Coupon();
        $coupon3->setCouponCode('EXPIRED')
                ->setCouponPercent(10)
                ->setCouponQuantityLimited('false')
                ->setCouponUnique('false')
                ->setCouponFreeShipping('false')
                ->setCouponDateAdd($expireTime)
                ->setCouponDateStart($expireTime)
                ->setCouponDateEnd($expireTime);
        $manager->persist($coupon3);

        $coupon4 = new Coupon();
        $coupon4->setCouponCode('EMPLOYEE')
                ->setCouponPercent(30)
                ->setCouponQuantityLimited('false')
                ->setCouponUnique('false')
                ->setCouponFreeShipping('true')
                ->setCouponDateAdd($nowTime)
                ->setCouponDateStart($nowTime)
                ->setCouponDateEnd($laterTime);
        $manager->persist($coupon4);

        $manager->flush();        
    }
}
