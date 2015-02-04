<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Order;
use NiftyThrifty\ShopBundle\Tests\Fixture\OrderData;
use NiftyThrifty\ShopBundle\Tests\Fixture\CouponData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class OrderTest extends WebTestCase
{
    public $shipping;
    public $testOrder;
    public $validator;
    public $em;
    public $container;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em        = $kernel->getContainer()->get('doctrine')->getManager();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->shipping  = $kernel->getContainer()->get('shipping_manager');
        $this->container = $kernel->getContainer();
        $nowTime         = new \DateTime();

        $this->testOrder = new Order();
        $this->testOrder->setBasketId(1)
              ->setOrderStatus('unpaid')
              ->setOrderDateCreation($nowTime)
              ->setOrderDateEnd($nowTime)
              ->setOrderUserFirstName('Tom')
              ->setOrderUserLastName('Phillips')
              ->setOrderUserEmail('tom@niftythrifty.com')
              ->setOrderAmount(100)
              ->setOrderAmountCoupon(35)
              ->setOrderAmountVat(1)
              ->setOrderAmountShipping(7.95)
              ->setOrderAmountCredits(10)
              ->setOrderAmountTotal(63.95)
              ->setOrderProducts("product list")
              ->setOrderShippingMethod('classic', $this->shipping)
              ->setOrderShippingAddressFirstName('Buffy')
              ->setOrderShippingAddressLastName('Summers')
              ->setOrderShippingAddressStreet('123 Hellmouth Street')
              ->setOrderShippingAddressCity('Sunnydale')
              ->setOrderShippingAddressState('CA')
              ->setOrderShippingAddressZipcode('99666')
              ->setOrderShippingAddressCountry('USA')
              ->setOrderBillingAddressFirstName('Rupert')
              ->setOrderBillingAddressLastName('Giles')
              ->setOrderBillingAddressStreet('456 Watcher Ct')
              ->setOrderBillingAddressCity('Watcherside')
              ->setOrderBillingAddressState('NY')
              ->setOrderBillingAddressZipcode('12345')
              ->setOrderBillingAddressCountry('USA')
              ->setOrderUserIpAddress('50.57.94.160')
              ->setCouponId(null);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testConstants()
    {
        $this->assertEquals(Order::STATUS_PAID,   'paid');
        $this->assertEquals(Order::STATUS_UNPAID, 'unpaid');
        $this->assertEquals(Order::STATUS_EXPIRED,'expired');
    }

    public function testValidOrder()
    {
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(0, $violationList->count());
    }

    public function testOrderBasketIdBlank()
    {
        $this->testOrder->setBasketId(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Basket can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'basketId');
    }

    /**
     * Coupon data is not loaded so any coupon will be invalid
     *
     * @covers Order::validateCouponId
     */
    public function testOrderInvalidCouponId()
    {
        $this->testOrder->setCouponId(1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The entered coupon is invalid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponId');

    }

    /**
     * @covers Order::validateCouponId
     */
    public function testOrderExpiredCoupon()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new CouponData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testOrder->setCouponId(3);
        $coupon = $this->em->getRepository('NiftyThriftyShopBundle:Coupon')->find(3);
        $this->testOrder->setCoupon($coupon);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The entered coupon is expired.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'couponId');
    }

    /**
     * @covers Order::validateCouponId
     */
    public function testOrderValidCoupon()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new CouponData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testOrder->setCouponId(1);
        $coupon = $this->em->getRepository('NiftyThriftyShopBundle:Coupon')->find(1);
        $this->testOrder->setCoupon($coupon);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(0, $violationList->count());
    }

    public function testOrderStatusBlank()
    {
        $this->testOrder->setOrderStatus(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order status can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderStatus');
    }

    public function testOrderStatusBadValue()
    {
        $this->testOrder->setOrderStatus('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order status string is not valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderStatus');
    }

    public function testOrderShippingMethodBlank()
    {
        $this->testOrder->setOrderShippingMethod(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping method can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingMethod');
    }

    public function testOrderShippingMethodBadValue()
    {
        $this->testOrder->setOrderShippingMethod('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping method is not valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingMethod');
    }

    public function testOrderDateCreateBlank()
    {
        $this->testOrder->setOrderDateCreation(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date creation can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderDateCreation');
    }

    public function testOrderDateCreateBadDate()
    {
        $this->testOrder->setOrderDateCreation('tom');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date creation is not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderDateCreation');
    }

    public function testOrderDateEndBlank()
    {
        $this->testOrder->setOrderDateEnd(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date end can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderDateEnd');
    }

    public function testOrderDateEndBadDate()
    {
        $this->testOrder->setOrderDateEnd('tom');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date end is not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderDateEnd');
    }

    public function testOrderUserFirstNameBlank()
    {
        $this->testOrder->setOrderUserFirstName(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order first name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderUserFirstName');
    }

    public function testOrderUserLastNameBlank()
    {
        $this->testOrder->setOrderUserLastName(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order last name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderUserLastName');
    }

    public function testOrderUserEmailBlank()
    {
        $this->testOrder->setOrderUserEmail(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order e-mail can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderUserEmail');
    }

    public function testOrderUserIpAddressBlank()
    {
        $this->testOrder->setOrderUserIpAddress(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'User IP address must be defined.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderUserIpAddress');
    }

    public function testOrderUserIpAddressInvalid()
    {
        $this->testOrder->setOrderUserIpAddress('112.3');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'User IP address is invalid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderUserIpAddress');
    }

    public function testOrderProducts()
    {
        $this->testOrder->setOrderProducts(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order product list can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderProducts');
    }

    public function testOrderAmountBlank()
    {
        $this->testOrder->setOrderAmount(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order amount can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmount');
    }

    public function testOrderAmountNotNumber()
    {
        $this->testOrder->setOrderAmount('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order amount must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmount');
    }

    public function testOrderAmountNegative()
    {
        $this->testOrder->setOrderAmount(-1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order amount can not be negative.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmount');
    }

    public function testOrderAmountCouponBlank()
    {
        $this->testOrder->setOrderAmountCoupon(null)
                        ->setCouponId(1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(2, $violationList->count());
        $this->assertEquals($violationList[1]->getMessage(),        'Order coupon amount can not be blank.');
        $this->assertEquals($violationList[1]->getPropertyPath(),   'orderAmountCoupon');
    }

    public function testOrderAmountCouponNotNumber()
    {
        $this->testOrder->setOrderAmountCoupon('test')
                        ->setCouponId(1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(2, $violationList->count());
        $this->assertEquals($violationList[1]->getMessage(),        'Order amount coupon must be a number.');
        $this->assertEquals($violationList[1]->getPropertyPath(),   'orderAmountCoupon');
    }

    public function testOrderAmountCouponNegative()
    {
        $this->testOrder->setOrderAmountCoupon(-1)
                        ->setCouponId(1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(2, $violationList->count());
        $this->assertEquals($violationList[1]->getMessage(),        'Order coupon amount can not be negative.');
        $this->assertEquals($violationList[1]->getPropertyPath(),   'orderAmountCoupon');
    }

    public function testOrderAmountVatBlank()
    {
        $this->testOrder->setOrderAmountVat(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order tax amount can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountVat');
    }

    public function testOrderAmountVatNotNumber()
    {
        $this->testOrder->setOrderAmountVat('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order tax amount must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountVat');
    }

    public function testOrderAmountVatNegative()
    {
        $this->testOrder->setOrderAmountVat(-1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order tax amount can not be negative.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountVat');
    }

    public function testOrderAmountShippingBlank()
    {
        $this->testOrder->setOrderAmountShipping(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order shipping amount can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountShipping');
    }

    public function testOrderAmountShippingNotNumber()
    {
        $this->testOrder->setOrderAmountShipping('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order shipping amount must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountShipping');
    }

    public function testOrderAmountShippingNegative()
    {
        $this->testOrder->setOrderAmountShipping(-1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order shipping amount can not be negative.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountShipping');
    }

    public function testOrderAmountCreditsBlank()
    {
        $this->testOrder->setOrderAmountCredits(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order credit amount can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountCredits');
    }

    public function testOrderAmountCreditsNotNumber()
    {
        $this->testOrder->setOrderAmountCredits('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order credit amount must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountCredits');
    }

    public function testOrderAmountCreditsNegative()
    {
        $this->testOrder->setOrderAmountCredits(-1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order credit amount can not be negative.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountCredits');
    }

    public function testOrderAmountTotalBlank()
    {
        $this->testOrder->setOrderAmountTotal(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order total amount can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountTotal');
    }

    public function testOrderAmountTotalNotNumber()
    {
        $this->testOrder->setOrderAmountTotal('test');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order total amount must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountTotal');
    }

    public function testOrderAmountTotalNegative()
    {
        $this->testOrder->setOrderAmountTotal(-1);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order total amount can not be negative.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderAmountTotal');
    }

    public function testOrderShippingAddressFirstNameBlank()
    {
        $this->testOrder->setOrderShippingAddressFirstName(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping first name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressFirstName');
    }

    public function testOrderShippingAddressFirstNameTooLong()
    {
        $this->testOrder->setOrderShippingAddressFirstName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping first name may only be 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressFirstName');
    }

    public function testOrderShippingAddressLastNameBlank()
    {
        $this->testOrder->setOrderShippingAddressLastName(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping last name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressLastName');
    }

    public function testOrderShippingAddressLastNameTooLong()
    {
        $this->testOrder->setOrderShippingAddressLastName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping last name may only be 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressLastName');
    }

    public function testOrderShippingAddressStreetBlank()
    {
        $this->testOrder->setOrderShippingAddressStreet(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping street can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressStreet');
    }

    public function testOrderShippingAddressStreetTooLong()
    {
        $this->testOrder->setOrderShippingAddressStreet(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping street may only be 255 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressStreet');
    }

    public function testOrderShippingAddressCityBlank()
    {
        $this->testOrder->setOrderShippingAddressCity(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping city can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressCity');
    }

    public function testOrderShippingAddressCityTooLong()
    {
        $this->testOrder->setOrderShippingAddressCity(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping city may only be 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressCity');
    }

    public function testOrderShippingAddressStateBlank()
    {
        $this->testOrder->setOrderShippingAddressState(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping state can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressState');
    }

    public function testOrderShippingAddressStateBadValue()
    {
        $this->testOrder->setOrderShippingAddressState('XY');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping state code is not valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressState');
    }

    public function testOrderShippingAddressZipcodeBlank()
    {
        $this->testOrder->setOrderShippingAddressZipcode(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Shipping zip code can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressZipcode');
    }

    public function testOrderShippingAddressZipcodeBadValue()
    {
        $this->testOrder->setOrderShippingAddressZipcode('12-234');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),       'Shipping zip code must be 5 digits or 9 digits with a hyphen.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressZipcode');
    }

    public function testOrderShippingAddressZipcodeNine()
    {
        $this->testOrder->setOrderShippingAddressZipcode('12345-1234');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(0, $violationList->count());
    }

    public function testOrderShippingCountryBadValue()
    {
        $this->testOrder->setOrderShippingAddressCountry('England');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'We only ship within the US.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderShippingAddressCountry');
    }

    public function testOrderBillingAddressFirstNameBlank()
    {
        $this->testOrder->setOrderBillingAddressFirstName(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing first name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressFirstName');
    }

    public function testOrderBillingAddressFirstNameTooLong()
    {
        $this->testOrder->setOrderBillingAddressFirstName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing first name may only be 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressFirstName');
    }

    public function testOrderBillingAddressLastNameBlank()
    {
        $this->testOrder->setOrderBillingAddressLastName(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing last name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressLastName');
    }

    public function testOrderBillingAddressLastNameTooLong()
    {
        $this->testOrder->setOrderBillingAddressLastName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing last name may only be 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressLastName');
    }

    public function testOrderBillingAddressStreetBlank()
    {
        $this->testOrder->setOrderBillingAddressStreet(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing street can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressStreet');
    }

    public function testOrderBillingAddressStreetTooLong()
    {
        $this->testOrder->setOrderBillingAddressStreet(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing street may only be 255 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressStreet');
    }

    public function testOrderBillingAddressCityBlank()
    {
        $this->testOrder->setOrderBillingAddressCity(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing city can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressCity');
    }

    public function testOrderBillingAddressCityTooLong()
    {
        $this->testOrder->setOrderBillingAddressCity(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing city may only be 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressCity');
    }

    public function testOrderBillingAddressStateBlank()
    {
        $this->testOrder->setOrderBillingAddressState(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing state can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressState');
    }

    public function testOrderBillingAddressStateBadValue()
    {
        $this->testOrder->setOrderBillingAddressState('XY');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing state code is not valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressState');
    }

    public function testOrderBillingAddressZipcodeBlank()
    {
        $this->testOrder->setOrderBillingAddressZipcode(null);
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Billing zip code can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressZipcode');
    }

    public function testOrderBillingAddressZipcodeBadValue()
    {
        $this->testOrder->setOrderBillingAddressZipcode('12-234');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),       'Billing zip code must be 5 digits or 9 digits with a hyphen');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressZipcode');
    }

    public function testOrderBillingAddressZipcodeNine()
    {
        $this->testOrder->setOrderBillingAddressZipcode('12345-1234');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(0, $violationList->count());
    }

    public function testOrderBillingCountryBadValue()
    {
        $this->testOrder->setOrderBillingAddressCountry('England');
        $violationList = $this->validator->validate($this->testOrder);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'We only ship within the US.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'orderBillingAddressCountry');
    }

    public function testDuplicateAddressFalse()
    {
        $this->assertFalse($this->testOrder->areAddressesDuplicate());
    }

    public function testDuplicateAddressTrue()
    {
        $this->testOrder->setOrderBillingAddressFirstName($this->testOrder->getOrderShippingAddressFirstName())
                        ->setOrderBillingAddressLastName($this->testOrder->getOrderShippingAddressLastName())
                        ->setOrderBillingAddressStreet($this->testOrder->getOrderShippingAddressStreet())
                        ->setOrderBillingAddressCity($this->testOrder->getOrderShippingAddressCity())
                        ->setOrderBillingAddressState($this->testOrder->getOrderShippingAddressState())
                        ->setOrderBillingAddressZipcode($this->testOrder->getOrderShippingAddressZipcode())
                        ->setOrderBillingAddressCountry($this->testOrder->getOrderShippingAddressCountry());
        $this->assertTrue($this->testOrder->areAddressesDuplicate());
    }

    public function testSetShippingMethodClassic()
    {
        $this->testOrder->setOrderAmountShipping(null);
        $this->testOrder->setOrderShippingMethod('classic', $this->shipping);

        $this->assertEquals(7, $this->testOrder->getOrderAmountShipping());
    }

    public function testSetShippingMethodExpress()
    {
        $this->testOrder->setOrderAmountShipping(null);
        $this->testOrder->setOrderShippingMethod('express', $this->shipping);

        $this->assertEquals(19, $this->testOrder->getOrderAmountShipping());
    }

    public function testSetShippingMethodNoManager()
    {
        $this->testOrder->setOrderAmountShipping(null);
        $this->testOrder->setOrderShippingMethod('express');

        $this->assertNull($this->testOrder->getOrderAmountShipping());
    }

    public function testBeforeSave()
    {
        $this->testOrder->setOrderDateEnd(null)
                        ->setOrderDateCreation(null)
                        ->setOrderAmountTotal(null)
                        ->setOrderStatus(null)
                        ->setOrderAmount(100)
                        ->setOrderAmountShipping(20)
                        ->setOrderAmountVat(10)
                        ->setOrderAmountCredits(5)
                        ->setOrderAmountCoupon(10);
        $this->testOrder->beforeSave();
        $this->assertNotNull($this->testOrder->getOrderDateEnd());
        $this->assertNotNull($this->testOrder->getOrderDateCreation());
        $this->assertEquals('unpaid',  $this->testOrder->getOrderStatus());
        $this->assertEquals(115,       $this->testOrder->getOrderAmountTotal());
    }

    public function testGetOrderTotal()
    {
        $this->testOrder->setOrderAmount(100)
                        ->setOrderAmountShipping(20)
                        ->setOrderAmountVat(10)
                        ->setOrderAmountCredits(5)
                        ->setOrderAmountCoupon(10);
        $this->assertEquals(115, $this->testOrder->getOrderTotal());
    }

    public function testGetOrderTotalPreCredits()
    {
        $this->testOrder->setOrderAmount(100)
                        ->setOrderAmountShipping(20)
                        ->setOrderAmountVat(10)
                        ->setOrderAmountCredits(5)
                        ->setOrderAmountCoupon(10);
        $this->assertEquals(120, $this->testOrder->getOrderTotalPreCredits());
    }

    public function testGetShippingCostTotal()
    {
        $this->testOrder->setOrderAmount(100)
                        ->setOrderAmountShipping(20)
                        ->setOrderAmountVat(10)
                        ->setOrderAmountCredits(5)
                        ->setOrderAmountCoupon(10);
        $this->assertEquals(90, $this->testOrder->getShippingCostTotal());
    }

    public function testAssociationBasket()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new OrderData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(1);
        $this->assertEquals($order->getBasket()->getBasketId(), 1);
    }

    public function testAssociationCoupon()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new OrderData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $this->assertEquals($order->getCoupon()->getCouponId(), 1);
    }
}
