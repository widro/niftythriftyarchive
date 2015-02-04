<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\BasketItem;

class BasketItemData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\BasketData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\ProductData');
    }
    
    public function load(ObjectManager $manager)
    {
        // Item 1, active item, ongoing basket
        $basketItem1 = new BasketItem();
        $date1b = new \DateTime();
        $date1e = new \DateTime();
        $date1b->modify("-7 minutes");
        $date1e->modify("+3 minutes");
        $basketItem1->setBasket($this->getReference('basket-2'));
        $basketItem1->setProduct($this->getReference('product-1'));
        $basketItem1->setBasketItemDateAdd($date1b);
        $basketItem1->setBasketItemDateEnd($date1e);
        $basketItem1->setBasketItemPrice('10.00');
        $basketItem1->setBasketItemDiscount(0);
        $basketItem1->setBasketItemStatus(BasketItem::VALID);
        $manager->persist($basketItem1);

        // Item 2, active item, ongoing basket
        $basketItem2 = new BasketItem();
        $date2e = new \DateTime();
        $date2b = new \DateTime();
        $date2b->modify("-8 min");
        $date2e->modify("+2 min");
        $basketItem2->setBasket($this->getReference('basket-2'));
        $basketItem2->setProduct($this->getReference('product-2'));
        $basketItem2->setBasketItemDateAdd($date2b);
        $basketItem2->setBasketItemDateEnd($date2e);
        $basketItem2->setBasketItemPrice('15.00');
        $basketItem2->setBasketItemDiscount(0);
        $basketItem2->setBasketItemStatus(BasketItem::VALID);
        $manager->persist($basketItem2);

        // Item 3, active item, ongoing basket
        $basketItem3 = new BasketItem();
        $date3b = new \DateTime();
        $date3e = new \DateTime();
        $date3b->modify("-2 min");
        $date3e->modify("+8 min");
        $basketItem3->setBasket($this->getReference('basket-2'));
        $basketItem3->setProduct($this->getReference('product-3'));
        $basketItem3->setBasketItemDateAdd($date3b);
        $basketItem3->setBasketItemDateEnd($date3e);
        $basketItem3->setBasketItemPrice('12.00');
        $basketItem3->setBasketItemDiscount(0);
        $basketItem3->setBasketItemStatus(BasketItem::VALID);
        $manager->persist($basketItem3);

        // Item 4, expired item, ongoing basket
        $basketItem4 = new BasketItem();
        $date4b = new \DateTime();
        $date4e = new \DateTime();
        $date4b->modify("-20 min");
        $date4e->modify("-10 min");
        $basketItem4->setBasket($this->getReference('basket-2'));
        $basketItem4->setProduct($this->getReference('product-4'));
        $basketItem4->setBasketItemDateAdd($date4b);
        $basketItem4->setBasketItemDateEnd($date4e);
        $basketItem4->setBasketItemPrice('10.00');
        $basketItem4->setBasketItemDiscount(0);
        $basketItem4->setBasketItemStatus(BasketItem::VALID);
        $manager->persist($basketItem4);

        // Item five, purchased basket.
        $basketItem5 = new BasketItem();
        $date5b = new \DateTime();
        $date5e = new \DateTime();
        $date5b->modify("-117 min");
        $date5e->modify("-107 min");
        $basketItem5->setBasket($this->getReference('basket-1'));
        $basketItem5->setProduct($this->getReference('product-7'));
        $basketItem5->setBasketItemDateAdd($date5b);
        $basketItem5->setBasketItemDateEnd($date5e);
        $basketItem5->setBasketItemPrice('10.00');
        $basketItem5->setBasketItemDiscount(0);
        $basketItem5->setBasketItemStatus(BasketItem::PAYMENT);
        $manager->persist($basketItem5);

        $manager->flush();
    }
}
