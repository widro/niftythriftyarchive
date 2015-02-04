<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\BasketItem;

class MoreBasketItemData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData');
    }
    
    public function load(ObjectManager $manager)
    {
        // Item 1, active item, ongoing basket
        $basketItem6 = new BasketItem();
        $date1b = new \DateTime();
        $date1e = new \DateTime();
        $date1b->modify("-7 min");
        $date1e->modify("+3 min");
        $basketItem6->setBasket($this->getReference('basket-3'));
        $basketItem6->setProduct($this->getReference('product-1'));
        $basketItem6->setBasketItemDateAdd($date1b);
        $basketItem6->setBasketItemDateEnd($date1e);
        $basketItem6->setBasketItemPrice('10.00');
        $basketItem6->setBasketItemDiscount(0);
        $basketItem6->setBasketItemStatus(BasketItem::VALID);
        $basketItem6->setProductId(1);
        $basketItem6->setBasketId(3);
        $manager->persist($basketItem6);

        // Item 2, active item, ongoing basket
        $basketItem7 = new BasketItem();
        $date2e = new \DateTime();
        $date2b = new \DateTime();
        $date2b->modify("-8 min");
        $date2e->modify("+2 min");
        $basketItem7->setBasket($this->getReference('basket-3'));
        $basketItem7->setProduct($this->getReference('product-2'));
        $basketItem7->setBasketItemDateAdd($date2b);
        $basketItem7->setBasketItemDateEnd($date2e);
        $basketItem7->setBasketItemPrice('15.00');
        $basketItem7->setBasketItemDiscount(0);
        $basketItem7->setBasketItemStatus(BasketItem::VALID);
        $basketItem7->setProductId(2);
        $basketItem7->setBasketId(3);
        $manager->persist($basketItem7);

        $manager->flush();
    }
}
