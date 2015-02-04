<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\CouponData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;

class CouponRepositoryFunctionalTest extends NiftyBaseTestCase
{

    /**
     * Test finding by code
     *
     * @group Coupon
     */
    public function testFindUnexpiredByCouponCode()
    {
        $this->addFixture(new CouponData);
        $this->executeFixtures();
        $coupon = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Coupon')
                       ->findUnexpiredByCouponCode('PERCENT');
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Coupon', $coupon);
        $this->assertEquals($coupon->getCouponid(), 1);
    }

    /**
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function testFindUnexpiredByCouponCodeExpired()
    {
        $this->addFixture(new CouponData);
        $this->executeFixtures();
        $coupon = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Coupon')
                       ->findUnexpiredByCouponCode('EXPIRED');
        
    }

    public function testFindUnexpiredByCouponId()
    {
        $this->addFixture(new CouponData);
        $this->executeFixtures();
        $coupon = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Coupon')
                       ->findUnexpiredByCouponId(2);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Coupon', $coupon);
        $this->assertEquals($coupon->getCouponId(), 2);
    }

    /**
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function testFindUnexpiredByCouponIdExpired()
    {
        $this->addFixture(new CouponData);
        $this->executeFixtures();
        $coupon = $this->em
                       ->getRepository('NiftyThriftyShopBundle:Coupon')
                       ->findUnexpiredByCouponId(3);

    }
}
