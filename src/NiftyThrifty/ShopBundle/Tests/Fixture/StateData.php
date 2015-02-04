<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\State;

class StateData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $state1 = new State();
        $state1->setStateName('New York');
        $state1->setStateCode('NY');
        $this->addReference('state-1', $state1);
        $manager->persist($state1);
        
        $state2 = new State();
        $state2->setStateName('Massachusettes');
        $state2->setStateCode('MA');
        $this->addReference('state-2', $state2);
        $manager->persist($state2);
        
        $manager->flush();
    }
}
