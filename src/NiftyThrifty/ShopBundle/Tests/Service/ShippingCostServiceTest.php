<?php

namespace NiftyThrifty\ShopBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use NiftyThrifty\ShopBundle\Service\ShippingCostService;
use NiftyThrifty\ShopBundle\Entity\Coupon;

/**
 * This is currently being managed by two shipping configurations in services_test.yml.  The
 * two configurations are
 *      - shipping_manager:         "no", 70, 7, 19
 *      - shipping_manager_free:    "yes", 10, 15, 20
 * This is to have one manager that mimics site-wide free shipping on and another that doesn't.
 */
class ShippingCostServiceTest extends WebTestCase
{
    public $shippingService;
    public $freeShippingService;
    public $testCoupon;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->shippingService      = $kernel->getContainer()->get('shipping_manager');
        $this->freeShippingService  = $kernel->getContainer()->get('shipping_manager_free');
        $this->testCoupon = new Coupon();
        $this->testCoupon->setCouponCode('EMPLOYEE')
                         ->setCouponPercent(30)
                         ->setCouponQuantityLimited('false')
                         ->setCouponUnique('false')
                         ->setCouponFreeShipping('true')
                         ->setCouponDateAdd(new \DateTime())
                         ->setCouponDateStart(new \DateTime())
                         ->setCouponDateEnd(new \DateTime("+10 days"));
    }

    /**
     * Check if the constructor properly sets the information with site-wide free
     * shipping turned off.
     *
     * @covers ShippingCostService::__construct
     * @covers ShippingCostService::getShippingChoices
     * @covers ShippingCostService::isSitewideFreeShipping
     * @covers ShippingCostService::isFreeShipping
     * @covers ShippingCostService::getClassicShippingCost
     * @covers ShippingCostService::setItemCount
     */
    public function testContainerConstructSitewideOffOneItem()
    {
        $this->shippingService->setItemCount(1);
        $expected = array('classic' => 'Classic: $7',
                          'express' => 'Express: $19');
        $this->assertEquals($expected, $this->shippingService->getShippingChoices());
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertFalse($this->shippingService->isFreeShipping());
    }

    /**
     * @covers ShippingCostService::__construct
     * @covers ShippingCostService::getShippingChoices
     * @covers ShippingCostService::isSitewideFreeShipping
     * @covers ShippingCostService::isFreeShipping
     * @covers ShippingCostService::getClassicShippingCost
     * @covers ShippingCostService::setItemCount
     */
    public function testContainerConstructSitewideOffTwoItems()
    {
        $this->shippingService->setItemCount(2);
        $expected = array('classic' => 'Classic: $8',
                          'express' => 'Express: $19');
        $this->assertEquals($expected, $this->shippingService->getShippingChoices());
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertFalse($this->shippingService->isFreeShipping());
    }

    /**
     * @covers ShippingCostService::__construct
     * @covers ShippingCostService::getShippingChoices
     * @covers ShippingCostService::isSitewideFreeShipping
     * @covers ShippingCostService::isFreeShipping
     * @covers ShippingCostService::getClassicShippingCost
     * @covers ShippingCostService::setItemCount
     */
    public function testContainerConstructSitewideOffThreeItems()
    {
        $this->shippingService->setItemCount(3);
        $expected = array('classic' => 'Classic: $9',
                          'express' => 'Express: $19');
        $this->assertEquals($expected, $this->shippingService->getShippingChoices());
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertFalse($this->shippingService->isFreeShipping());
    }

    /**
     * @covers ShippingCostService::__construct
     * @covers ShippingCostService::getShippingChoices
     * @covers ShippingCostService::isSitewideFreeShipping
     * @covers ShippingCostService::isFreeShipping
     * @covers ShippingCostService::getClassicShippingCost
     * @covers ShippingCostService::setItemCount
     */
    public function testContainerConstructSitewideOffZeroItems()
    {
        $this->shippingService->setItemCount(0);
        $expected = array('classic' => 'Classic: $7',
                          'express' => 'Express: $19');
        $this->assertEquals($expected, $this->shippingService->getShippingChoices());
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertFalse($this->shippingService->isFreeShipping());
    }

    /**
     * @expectedException \NiftyThrifty\ShopBundle\Service\ShippingCostServiceException
     */
    public function testContainerConstructSitewideOffUndefinedItem()
    {
        $this->shippingService->setItemCount(null);
        $this->shippingService->getShippingChoices();
    }

    /**
     * Test that free shipping is enabled when the order total is over the set value.
     *
     * @covers ShippingCostService::isFreeShipping
     * @covers ShippingCostService::setOrderTotal
     */
    public function testContainerConstructorSitewideOffCartFree()
    {
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertFalse($this->shippingService->isFreeShipping());
        $this->shippingService->setOrderTotal(71);
        $this->assertTrue($this->shippingService->isFreeShipping());
    }

    /**
     * Check if the constructor sets everything for site-wide free shipping on.
     *
     * @covers ShippingCostService::__construct
     * @covers ShippingCostService::getShippingChoices
     * @covers ShippingCostService::isSitewideFreeShipping
     * @covers ShippingCostService::isFreeShipping
     */
    public function testNaturalConstructSiteWideOn()
    {
        $expected = array('classic' => 'Free: $0.00',
                          'express' => 'Express: $20');
        $this->assertEquals($expected, $this->freeShippingService->getShippingChoices());
        $this->assertTrue($this->freeShippingService->isSitewideFreeShipping());
        $this->assertTrue($this->freeShippingService->isFreeShipping());
    }
    
    public function testFreeShippingViaCoupon()
    {
        $this->shippingService->setItemCount(3)
                              ->setCoupon($this->testCoupon);
        $expected = array('classic' => 'Free: $0.00',
                          'express' => 'Express: $19');
        $this->assertEquals($expected, $this->shippingService->getShippingChoices());
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertTrue($this->shippingService->isFreeShipping());
    }
    
    public function testRegularShippingWithCoupon()
    {
        $this->testCoupon->setCouponFreeShipping('false');
        $this->shippingService->setItemCount(3)
                              ->setCoupon($this->testCoupon);
        $expected = array('classic' => 'Classic: $9',
                          'express' => 'Express: $19');
        $this->assertEquals($expected, $this->shippingService->getShippingChoices());
        $this->assertFalse($this->shippingService->isSitewideFreeShipping());
        $this->assertFalse($this->shippingService->isFreeShipping());
    }
        
    
    public function testConstants()
    {
        $this->assertEquals(ShippingCostService::CLASSIC_SHIPPING, 'classic');
        $this->assertEquals(ShippingCostService::EXPRESS_SHIPPING, 'express');
    }

    public function testGetShippingCostClassicSitewideFree()
    {
        $this->assertEquals($this->freeShippingService->getShippingCost('classic'), 0);
    }

    public function testGetShippingCostClassicSitewideNotFree()
    {
        $this->assertEquals($this->shippingService->getShippingCost('classic'), 7);
    }

    public function testGetShippingCostClassicSitewideNotFreeCartFree()
    {
        $this->shippingService->setOrderTotal(71);
        $this->assertEquals($this->shippingService->getShippingCost('classic'), 0);
    }

    public function testGetShippingCostExpressFree()
    {
        $this->assertEquals($this->freeShippingService->getShippingCost('express'), 20);
    }

    public function testGetShippingCostExpressNotFree()
    {
        $this->assertEquals($this->shippingService->getShippingCost('express'), 19);
    }
    
    /**
     * @expectedException \NiftyThrifty\ShopBundle\Service\ShippingCostServiceException
     */
    public function testGetShippingCostException()
    {
        $this->shippingService->getShippingCost('something');
    }
}
