<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Invoice;

class InvoiceData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        // InvoiceData includes OrderData includes BasketData includes UserData
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\OrderData');
    }
    
    public function load(ObjectManager $manager)
    {
        $invoice1 = new Invoice();
        
        $invoice1->setInvoiceNum('123-6543');
        $invoice1->setOrder($this->getReference('order-1'));
        $invoice1->setBasket($this->getReference('basket-1'));
        $invoice1->setInvoiceStatus('x');
        $invoiceDate = new \DateTime('2013-06-01 11:30');
        $invoice1->setInvoiceDate($invoiceDate);
        $invoice1->setUser($this->getReference('user-1'));
        $invoice1->setInvoiceUserFirstName('Paid');
        $invoice1->setInvoiceUserLastName('Invoice');
        $invoice1->setInvoiceUserEmail('test@niftythrifty.com');
        $invoice1->setInvoiceAmount(95);
        $invoice1->setInvoiceAmountCoupon(0);
        $invoice1->setInvoiceAmountVat(0);
        $invoice1->setInvoiceAmountShipping(7.95);
        $invoice1->setInvoiceAmountCredits(4);
        $invoice1->setInvoiceAmountTotal(98.95);
        $invoice1->setInvoiceProducts('Five');
        $invoice1->setInvoiceShippingMethod('classic');
        $invoice1->setInvoiceShippingAddressFirstName('Jon');
        $invoice1->setInvoiceShippingAddressLastName('Smith');
        $invoice1->setInvoiceShippingAddressStreet('123 Gift Lane');
        $invoice1->setInvoiceShippingAddressCity('Gifttown');
        $invoice1->setInvoiceShippingAddressState('NY');
        $invoice1->setInvoiceShippingaddressZipcode(12111);
        $invoice1->setInvoiceShippingAddressCountry('USA');
        $invoice1->setInvoiceShippingStatus('shipped');
        $invoice1->setInvoiceBillingAddressFirstName('Tom');
        $invoice1->setInvoiceBillingAddressLastName('Ryan');
        $invoice1->setInvoiceBillingAddressStreet('123 Bill Street');
        $invoice1->setInvoiceBillingAddressCity('Billtown');
        $invoice1->setInvoiceBillingAddressState('MA');
        $invoice1->setInvoiceBillingAddressZipcode(23345);
        $invoice1->setInvoiceBillingAddressCountry('USA');
        $invoice1->setInvoiceUserIpAddress('127.0.0.1');
        // Nullable $order->setCouponId();
        $manager->persist($invoice1);

        $manager->flush();
    }
}
