<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Invoice;
use NiftyThrifty\ShopBundle\Entity\Order;

class InvoiceTest extends WebTestCase
{
    public $shipping;

    public function setUp()
    {

        $kernel = static::createKernel();
        $kernel->boot();
        $this->shipping = $kernel->getContainer()->get('shipping_manager');
    }
    
    public function testConstants()
    {
        $this->assertEquals(Invoice::SHIPPING_STATUS_PROCESSING,'processing');
        $this->assertEquals(Invoice::SHIPPING_STATUS_EXPEDITED, 'expedited');
        $this->assertEquals(Invoice::SHIPPING_STATUS_TRACKING,  'track shipment');
        $this->assertEquals(Invoice::SHIPPING_STATUS_SHIPPWED,  'shipped');
    }

    public function testSetFromOrder()
    {
        $nowTime = new \DateTime();

        $order = new Order();
        $order->setBasketId(1)
              ->setOrderStatus('ongoing')
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
              ->setOrderUserIpAddress('123.432.123.1')
              ->setCouponId(1);

        $invoice = new Invoice();
        $invoice->setFromOrder($order);

        $this->assertEquals($invoice->getBasketId(),                        $order->getBasketId());
        $this->assertEquals($invoice->getInvoiceUserFirstName(),            $order->getOrderUserFirstName());
        $this->assertEquals($invoice->getInvoiceUserLastName(),             $order->getOrderUserLastName());
        $this->assertEquals($invoice->getInvoiceUserEmail(),                $order->getOrderUserEmail());
        $this->assertEquals($invoice->getInvoiceAmount(),                   $order->getOrderAmount());
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             $order->getOrderAmountCoupon());
        $this->assertEquals($invoice->getInvoiceAmountVat(),                $order->getOrderAmountVat());
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           $order->getOrderAmountShipping());
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            $order->getOrderAmountCredits());
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              $order->getOrderAmountTotal());
        $this->assertEquals($invoice->getInvoiceProducts(),                 $order->getOrderProducts());
        $this->assertEquals($invoice->getInvoiceShippingMethod(),           $order->getOrderShippingMethod());
        $this->assertEquals($invoice->getInvoiceShippingAddressFirstName(), $order->getOrderShippingAddressFirstName());
        $this->assertEquals($invoice->getInvoiceShippingAddressLastName(),  $order->getOrderShippingAddressLastName());
        $this->assertEquals($invoice->getInvoiceShippingAddressStreet(),    $order->getOrderShippingAddressStreet());
        $this->assertEquals($invoice->getInvoiceShippingAddressCity(),      $order->getOrderShippingAddressCity());
        $this->assertEquals($invoice->getInvoiceShippingAddressState(),     $order->getOrderShippingAddressState());
        $this->assertEquals($invoice->getInvoiceShippingAddressZipcode(),   $order->getOrderShippingAddressZipcode());
        $this->assertEquals($invoice->getInvoiceShippingAddressCountry(),   $order->getOrderShippingAddressCountry());
        $this->assertEquals($invoice->getInvoiceBillingAddressFirstName(),  $order->getOrderBillingAddressFirstName());
        $this->assertEquals($invoice->getInvoiceBillingAddressLastName(),   $order->getOrderBillingAddressLastName());
        $this->assertEquals($invoice->getInvoiceBillingAddressStreet(),     $order->getOrderBillingAddressStreet());
        $this->assertEquals($invoice->getInvoiceBillingAddressCity(),       $order->getOrderBillingAddressCity());
        $this->assertEquals($invoice->getInvoiceBillingAddressState(),      $order->getOrderBillingAddressState());
        $this->assertEquals($invoice->getInvoiceBillingAddressZipcode(),    $order->getOrderBillingAddressZipcode());
        $this->assertEquals($invoice->getInvoiceBillingAddressCountry(),    $order->getOrderBillingAddressCountry());
        $this->assertEquals($invoice->getInvoiceUserIpAddress(),            $order->getOrderUserIpAddress());
        $this->assertEquals($invoice->getCouponId(),                        $order->getCouponId());
        $this->assertEquals($invoice->getInvoiceShippingStatus(),           'processing');
        $this->assertNotNull($invoice->getInvoiceDate());
    }
    
    public function testSetFromOrderExpedited()
    {
        $nowTime = new \DateTime();

        $order = new Order();
        $order->setBasketId(1)
              ->setOrderStatus('ongoing')
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
              ->setOrderShippingMethod('express', $this->shipping)
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
              ->setOrderUserIpAddress('123.432.123.1')
              ->setCouponId(1);

        $invoice = new Invoice();
        $invoice->setFromOrder($order);

        $this->assertEquals($invoice->getBasketId(),                        $order->getBasketId());
        $this->assertEquals($invoice->getInvoiceUserFirstName(),            $order->getOrderUserFirstName());
        $this->assertEquals($invoice->getInvoiceUserLastName(),             $order->getOrderUserLastName());
        $this->assertEquals($invoice->getInvoiceUserEmail(),                $order->getOrderUserEmail());
        $this->assertEquals($invoice->getInvoiceAmount(),                   $order->getOrderAmount());
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             $order->getOrderAmountCoupon());
        $this->assertEquals($invoice->getInvoiceAmountVat(),                $order->getOrderAmountVat());
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           $order->getOrderAmountShipping());
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            $order->getOrderAmountCredits());
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              $order->getOrderAmountTotal());
        $this->assertEquals($invoice->getInvoiceProducts(),                 $order->getOrderProducts());
        $this->assertEquals($invoice->getInvoiceShippingMethod(),           $order->getOrderShippingMethod());
        $this->assertEquals($invoice->getInvoiceShippingAddressFirstName(), $order->getOrderShippingAddressFirstName());
        $this->assertEquals($invoice->getInvoiceShippingAddressLastName(),  $order->getOrderShippingAddressLastName());
        $this->assertEquals($invoice->getInvoiceShippingAddressStreet(),    $order->getOrderShippingAddressStreet());
        $this->assertEquals($invoice->getInvoiceShippingAddressCity(),      $order->getOrderShippingAddressCity());
        $this->assertEquals($invoice->getInvoiceShippingAddressState(),     $order->getOrderShippingAddressState());
        $this->assertEquals($invoice->getInvoiceShippingAddressZipcode(),   $order->getOrderShippingAddressZipcode());
        $this->assertEquals($invoice->getInvoiceShippingAddressCountry(),   $order->getOrderShippingAddressCountry());
        $this->assertEquals($invoice->getInvoiceBillingAddressFirstName(),  $order->getOrderBillingAddressFirstName());
        $this->assertEquals($invoice->getInvoiceBillingAddressLastName(),   $order->getOrderBillingAddressLastName());
        $this->assertEquals($invoice->getInvoiceBillingAddressStreet(),     $order->getOrderBillingAddressStreet());
        $this->assertEquals($invoice->getInvoiceBillingAddressCity(),       $order->getOrderBillingAddressCity());
        $this->assertEquals($invoice->getInvoiceBillingAddressState(),      $order->getOrderBillingAddressState());
        $this->assertEquals($invoice->getInvoiceBillingAddressZipcode(),    $order->getOrderBillingAddressZipcode());
        $this->assertEquals($invoice->getInvoiceBillingAddressCountry(),    $order->getOrderBillingAddressCountry());
        $this->assertEquals($invoice->getInvoiceUserIpAddress(),            $order->getOrderUserIpAddress());
        $this->assertEquals($invoice->getCouponId(),                        $order->getCouponId());
        $this->assertEquals($invoice->getInvoiceShippingStatus(),           'expedited');
        $this->assertNotNull($invoice->getInvoiceDate());
    }
}
