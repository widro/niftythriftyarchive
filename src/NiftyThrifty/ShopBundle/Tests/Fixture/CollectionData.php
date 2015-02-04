<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use NiftyThrifty\ShopBundle\Entity\Collection;

class CollectionData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $collection1 = new Collection();
        $collection1->setCollectionName('Active Not Ending Soon One');
        $collection1->setCollectionCode('ANE1');
        $collection1->setCollectionDescription('Winter collection');
        $collection1->setCollectionType('Women');
        $collection1->setCollectionActive('yes');
        $collection1->setIsShop('no');
        $date1s = new \DateTime();
        $date1s->modify("-5 days");
        $date1e = new \DateTime();
        $date1e->modify("+2 days");
        $collection1->setCollectionDateStart($date1s);
        $collection1->setCollectionDateEnd($date1e);
        $this->addReference('collection-1', $collection1);

        $collection2 = new Collection();
        $collection2->setCollectionName('Active Not Ending Soon Two');
        $collection2->setCollectionCode('ANE2');
        $collection2->setCollectionDescription('Fall collection');
        $collection2->setCollectionType('Women');
        $collection2->setCollectionActive('yes');
        $collection2->setIsShop('no');
        $date2s = new \DateTime();
        $date2s->modify("-4 days");
        $date2e = new \DateTime();
        $date2e->modify("+4 days");
        $collection2->setCollectionDateStart($date2s);
        $collection2->setCollectionDateEnd($date2e);
        $this->addReference('collection-2', $collection2);

        $collection3 = new Collection();
        $collection3->setCollectionName('Active Not Ending Soon Three');
        $collection3->setCollectionCode('ANE3');
        $collection3->setCollectionDescription('Summer collection');
        $collection3->setCollectionType('Women');
        $collection3->setCollectionActive('yes');
        $collection3->setIsShop('no');
        $date3s = new \DateTime();
        $date3s->modify("-3 days");
        $date3e = new \DateTime();
        $date3e->modify("+3 days");
        $collection3->setCollectionDateStart($date3s);
        $collection3->setCollectionDateEnd($date3e);
        $this->addReference('collection-3', $collection3);

        $collection4 = new Collection();
        $collection4->setCollectionName('Active Ending Soon One');
        $collection4->setCollectionCode('AES1');
        $collection4->setCollectionDescription('Expiring Winter collection');
        $collection4->setCollectionType('Women');
        $collection4->setCollectionActive('yes');
        $collection4->setIsShop('no');
        $date4s = new \DateTime();
        $date4s->modify("-6 days");
        $date4e = new \DateTime();
        $date4e->modify("+12 hours");
        $collection4->setCollectionDateStart($date4s);
        $collection4->setCollectionDateEnd($date4e);
        $this->addReference('collection-4', $collection4);

        $collection5 = new Collection();
        $collection5->setCollectionName('Active Ending Soon Two');
        $collection5->setCollectionCode('AES2');
        $collection5->setCollectionDescription('Expiring summer collection');
        $collection5->setCollectionType('Women');
        $collection5->setCollectionActive('yes');
        $collection5->setIsShop('no');
        $date5s = new \DateTime();
        $date5s->modify("-7 days");
        $date5e = new \DateTime();
        $date5e->modify("+8 hours");
        $collection5->setCollectionDateStart($date5s);
        $collection5->setCollectionDateEnd($date5e);
        $this->addReference('collection-5', $collection5);

        $collection6 = new Collection();
        $collection6->setCollectionName('Active Ending Soon Three');
        $collection6->setCollectionCode('AES3');
        $collection6->setCollectionDescription('Expiring Spring collection');
        $collection6->setCollectionType('Women');
        $collection6->setCollectionActive('yes');
        $collection6->setIsShop('no');
        $date6s = new \DateTime();
        $date6s->modify("-8 days");
        $date6e = new \DateTime();
        $date6e->modify("+16 hours");
        $collection6->setCollectionDateStart($date6s);
        $collection6->setCollectionDateEnd($date6e);
        $this->addReference('collection-6', $collection6);

        $collection7 = new Collection();
        $collection7->setCollectionName('Upcoming One');
        $collection7->setCollectionCode('UPC1');
        $collection7->setCollectionDescription('Next winter collection');
        $collection7->setCollectionType('Women');
        $collection7->setCollectionActive('yes');
        $collection7->setIsShop('no');
        $date7s = new \DateTime();
        $date7s->modify("+5 days");
        $date7e = new \DateTime();
        $date7e->modify("+12 days");
        $collection7->setCollectionDateStart($date7s);
        $collection7->setCollectionDateEnd($date7e);
        $this->addReference('collection-7', $collection7);

        $collection8 = new Collection();
        $collection8->setCollectionName('Upcoming Two');
        $collection8->setCollectionCode('UPC2');
        $collection8->setCollectionDescription('Next summer collection');
        $collection8->setCollectionType('Women');
        $collection8->setCollectionActive('yes');
        $collection8->setIsShop('no');
        $date8s = new \DateTime();
        $date8s->modify("+3 days");
        $date8e = new \DateTime();
        $date8e->modify("+10 days");
        $collection8->setCollectionDateStart($date8s);
        $collection8->setCollectionDateEnd($date8e);
        $this->addReference('collection-8', $collection8);

        $collection9 = new Collection();
        $collection9->setCollectionName('Upcoming Three');
        $collection9->setCollectionCode('UPC3');
        $collection9->setCollectionDescription('Next fall collection');
        $collection9->setCollectionType('Women');
        $collection9->setCollectionActive('yes');
        $collection9->setIsShop('no');
        $date9s = new \DateTime();
        $date9s->modify("+7 days");
        $date9e = new \DateTime();
        $date9e->modify("+14 days");
        $collection9->setCollectionDateStart($date9s);
        $collection9->setCollectionDateEnd($date9e);
        $this->addReference('collection-9', $collection9);

        $collection10 = new Collection();
        $collection10->setCollectionName('Expired Collection');
        $collection10->setCollectionCode('EXP1');
        $collection10->setCollectionDescription('Last Winter collection');
        $collection10->setCollectionType('Women');
        $collection10->setCollectionActive('yes');
        $collection10->setIsShop('no');
        $date10s = new \DateTime();
        $date10s->modify("-10 days");
        $date10e = new \DateTime();
        $date10e->modify("-2 days");
        $collection10->setCollectionDateStart($date10s);
        $collection10->setCollectionDateEnd($date10e);
        $this->addReference('collection-10', $collection10);

        $collection11 = new Collection();
        $collection11->setCollectionName('Not yet active collection');
        $collection11->setCollectionCode('INA1');
        $collection11->setCollectionDescription('In progress collection');
        $collection11->setCollectionType('Women');
        $collection11->setCollectionActive('no');
        $collection11->setIsShop('no');
        $date11s = new \DateTime();
        $date11s->modify("+10 days");
        $date11e = new \DateTime();
        $date11e->modify("+17 days");
        $collection11->setCollectionDateStart($date11s);
        $collection11->setCollectionDateEnd($date11e);
        $this->addReference('collection-11', $collection11);

        $collection12 = new Collection();
        $collection12->setCollectionName('Vintage Staples');
        $collection12->setCollectionCode('SHP1');
        $collection12->setCollectionDescription('Shop One');
        $collection12->setCollectionType('Women');
        $collection12->setCollectionActive('yes');
        $collection12->setIsShop('yes');
        $date12s = new \DateTime();
        $date12s->modify("-10 days");
        $date12e = new \DateTime();
        $date12e->modify("+30 days");
        $collection12->setCollectionDateStart($date12s);
        $collection12->setCollectionDateEnd($date12e);
        $this->addReference('shop-1', $collection12);
        
        $collection13 = new Collection();
        $collection13->setCollectionName('Featured Shop');
        $collection13->setCollectionCode('SHP2');
        $collection13->setCollectionDescription('Shop Two');
        $collection13->setCollectionType('Women');
        $collection13->setCollectionActive('yes');
        $collection13->setIsShop('yes');
        $date13s = new \DateTime();
        $date13s->modify("-15 days");
        $date13e = new \DateTime();
        $date13e->modify("+45 days");
        $collection13->setCollectionDateStart($date13s);
        $collection13->setCollectionDateEnd($date13e);
        $this->addReference('shop-2', $collection13);

        $manager->persist($collection1);
        $manager->persist($collection2);
        $manager->persist($collection3);
        $manager->persist($collection4);
        $manager->persist($collection5);
        $manager->persist($collection6);
        $manager->persist($collection7);
        $manager->persist($collection8);
        $manager->persist($collection9);
        $manager->persist($collection10);
        $manager->persist($collection11);
        $manager->persist($collection12);
        $manager->persist($collection13);

        $manager->flush();
    }
}
