<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Basket;

class BasketData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\UserData');
    }
    
    public function load(ObjectManager $manager)
    {
        // User 1, purchased basket
        $basket1 = new Basket();
        $date1c  = new \DateTime();
        $date1u  = new \DateTime();
        $date1c->modify("-10 days");
        $date1u->modify("-9 days");
        $basket1->setBasketDateCreation($date1c);
        $basket1->setBasketDateUpdate($date1u);
        $basket1->setBasketStatus('purchased');
        $basket1->setUser($this->getReference('user-1'));
        $this->addReference('basket-1', $basket1);
        $manager->persist($basket1);

        // User 1, ongoing basket
        $basket2 = new Basket();
        $date2c  = new \DateTime();
        $date2u  = new \DateTime();
        $date2c->modify("-2 days");
        $date2u->modify("-8 minutes");
        $basket2->setBasketDateCreation($date2c);
        $basket2->setBasketDateUpdate($date2u);
        $basket2->setBasketStatus('ongoing');
        $basket2->setUser($this->getReference('user-1'));
        $this->addReference('basket-2', $basket2);
        $manager->persist($basket2);

        // Inactive User 3, ongoing/abandoned basket
        $basket3 = new Basket();
        $date3c  = new \DateTime();
        $date3u  = new \DateTime();
        $date3c->modify("-20 days");
        $date3u->modify("-19 days");
        $basket3->setBasketDateCreation($date3c);
        $basket3->setBasketDateUpdate($date3u);
        $basket3->setBasketStatus('ongoing');
        $basket3->setUser($this->getReference('user-3'));
        $this->addReference('basket-3', $basket3);
        $manager->persist($basket3);

        $manager->flush();
    }
}
