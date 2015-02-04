<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Coupon;

/**
 * Tests for the validator methods.
 */
class CouponTest extends WebTestCase
{
    public $testCoupon;
    public $validator;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->testCoupon = new Coupon();
        $nowTime = new \DateTime();
        $this->testCoupon->setCouponCode('TEST1')
                         ->setCouponPercent(35)
                         ->setCouponQuantityLimited('true')
                         ->setCouponUnique('true')
                         ->setCouponDateAdd($nowTime)
                         ->setCouponFreeShipping('true');

    }

    public function testCouponValid()
    {
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCouponCodeBlank()
    {
        $this->testCoupon->setCouponCode(null);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Coupon code can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponCode');
    }

    public function testCouponCodeTooLong()
    {
        $this->testCoupon->setCouponCode(str_repeat('x', 16));
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Coupon code may only be 15 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponCode');
    }

    public function testQuantityLimitedFalsePass()
    {
        $this->testCoupon->setCouponQuantityLimited('false');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testQuantityLimitedBadValue()
    {
        $this->testCoupon->setCouponQuantityLimited('tom');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select a limited quantity option.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponQuantityLimited');
    }

    public function testQuantityLimitedBlank()
    {
        $this->testCoupon->setCouponQuantityLimited(null);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select a limited quantity option.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponQuantityLimited');
    }

    public function testCouponUniqueFalsePass()
    {
        $this->testCoupon->setCouponUnique('false');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCouponUniqueBadValue()
    {
        $this->testCoupon->setCouponUnique('tom');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select a unique coupon option.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponUnique');
    }

    public function testCouponUniqueBlank()
    {
        $this->testCoupon->setCouponUnique(null);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select a unique coupon option.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponUnique');
    }

    public function testCouponFreeShippingFalsePass()
    {
        $this->testCoupon->setCouponFreeShipping('false');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCouponFreeShippingBadValue()
    {
        $this->testCoupon->setCouponFreeShipping('tom');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select a free shipping option.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponFreeShipping');
    }

    public function testCouponFreeShippingBlank()
    {
        $this->testCoupon->setCouponFreeShipping(null);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select a free shipping option.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponFreeShipping');
    }

    public function testCouponDateAddBadDate()
    {
        $this->testCoupon->setCouponDateAdd('tom');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date added is an invalid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponDateAdd');
    }

    public function testCouponDateAddBlank()
    {
        $this->testCoupon->setCouponDateAdd(null);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Addition time must be set.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponDateAdd');
    }

    public function testCouponDateStartValid()
    {
        $nowTime = new \DateTime();
        $this->testCoupon->setCouponDateStart($nowTime);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCouponDateStartBadValue()
    {
        $this->testCoupon->setCouponDateStart('tom');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Start date is not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponDateStart');
    }

    public function testCouponDateEndValid()
    {
        $nowTime = new \DateTime();
        $this->testCoupon->setCouponDateEnd($nowTime);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCouponDateEndBadValue()
    {
        $this->testCoupon->setCouponDateEnd('tom');
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'End date is not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponDateEnd');
    }

    public function testCouponAmountSetPasses()
    {
        $this->testCoupon->setCouponPercent(null)
                         ->setCouponAmount(34);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCouponAmountAndPercentNullFails()
    {
        $this->testCoupon->setCouponPercent(null)
                         ->setCouponAmount(null);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'A discount percentage or discount amount must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponPercent');
    }

    public function testCouponAmountAndPercentBothSetFail()
    {
        $this->testCoupon->setCouponPercent(39)
                         ->setCouponAmount(12);
        $violationList = $this->validator->validate($this->testCoupon);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Both percent and amount can not be set at the same time.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponPercent');
    }

    public function testGetDiscountPercent()
    {
        $this->testCoupon->setCouponPercent(25)
                         ->setCouponAmount(null);
        $this->assertEquals(25, $this->testCoupon->getDiscount(100));
    }

    public function testGetDiscountAmount()
    {
        $this->testCoupon->setCouponPercent(null)
                         ->setCouponAmount(30);
        $this->assertEquals(30, $this->testCoupon->getDiscount(100));
    }

    public function testGetDiscountAmountGreaterThanTotal()
    {
        $this->testCoupon->setCouponPercent(null)
                         ->setCouponAmount(30);
        $this->assertEquals(10, $this->testCoupon->getDiscount(10));
    }
}
