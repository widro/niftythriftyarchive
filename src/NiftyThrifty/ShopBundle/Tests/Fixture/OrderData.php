<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Order;

class OrderData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\BasketData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\CouponData');
    }
    
    public function load(ObjectManager $manager)
    {
        $order1 = new Order();
        
        $order1->setBasket($this->getReference('basket-1'));
        $order1->setOrderStatus('paid');
        $dateCreate = new \DateTime("-10 days");
        $order1->setOrderDateCreation($dateCreate);
        $dateEnd = new \DateTime("-10 days");
        $order1->setOrderDateEnd($dateEnd);
        $order1->setOrderUserFirstName('Unpaid');
        $order1->setOrderUserLastName('Order');
        $order1->setOrderUserEmail('test@niftythrifty.com');
        $order1->setOrderAmount(95);
        $order1->setOrderAmountCoupon(0);
        $order1->setOrderAmountVat(0);
        $order1->setOrderAmountShipping(7.95);
        $order1->setOrderAmountCredits(4);
        $order1->setOrderAmountTotal(98.95);
        $order1->setOrderProducts('Five');
        $order1->setOrderShippingMethod('classic');
        $order1->setOrderShippingAddressFirstName('Jon');
        $order1->setOrderShippingAddressLastName('Smith');
        $order1->setOrderShippingAddressStreet('123 Gift Lane');
        $order1->setOrderShippingAddressCity('Gifttown');
        $order1->setOrderShippingAddressState('NY');
        $order1->setOrderShippingaddressZipcode(12111);
        $order1->setOrderShippingAddressCountry('USA');
        $order1->setOrderBillingAddressFirstName('Tom');
        $order1->setOrderBillingAddressLastName('Ryan');
        $order1->setOrderBillingAddressStreet('123 Bill Street');
        $order1->setOrderBillingAddressCity('Billtown');
        $order1->setOrderBillingAddressState('MA');
        $order1->setOrderBillingAddressZipcode(23345);
        $order1->setOrderBillingAddressCountry('USA');
        $order1->setOrderUserIpAddress('127.0.0.1');
        $this->addReference('order-1', $order1);
        $manager->persist($order1);

        $order2 = new Order();
        $order2->setBasket($this->getReference('basket-2'));
        $order2->setOrderStatus('unpaid');
        $dateCreate = new \DateTime("-10 minutes");
        $order2->setOrderDateCreation($dateCreate);
        $dateEnd = new \DateTime("+3 minutes");
        $order2->setOrderDateEnd($dateEnd);
        $order2->setOrderUserFirstName('Unpaid');
        $order2->setOrderUserLastName('Order');
        $order2->setOrderUserEmail('test@niftythrifty.com');
        $order2->setOrderAmount(95);
        $order2->setOrderAmountCoupon(0);
        $order2->setOrderAmountVat(0);
        $order2->setOrderAmountShipping(7.95);
        $order2->setOrderAmountCredits(4);
        $order2->setOrderAmountTotal(98.95);
        $order2->setOrderProducts('Five');
        $order2->setOrderShippingMethod('classic');
        $order2->setOrderShippingAddressFirstName('Jon');
        $order2->setOrderShippingAddressLastName('Smith');
        $order2->setOrderShippingAddressStreet('123 Gift Lane');
        $order2->setOrderShippingAddressCity('Gifttown');
        $order2->setOrderShippingAddressState('NY');
        $order2->setOrderShippingaddressZipcode(12111);
        $order2->setOrderShippingAddressCountry('USA');
        $order2->setOrderBillingAddressFirstName('Tom');
        $order2->setOrderBillingAddressLastName('Ryan');
        $order2->setOrderBillingAddressStreet('123 Bill Street');
        $order2->setOrderBillingAddressCity('Billtown');
        $order2->setOrderBillingAddressState('MA');
        $order2->setOrderBillingAddressZipcode(23345);
        $order2->setOrderBillingAddressCountry('USA');
        $order2->setOrderUserIpAddress('127.0.0.1');
        $order2->setCoupon($this->getReference('coupon-1'));
        $this->addReference('order-2', $order2);
        $manager->persist($order2);

        $manager->flush();
    }
}
