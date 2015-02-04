<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Address;

class AddressData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\UserData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\StateData');
    }
    
    public function load(ObjectManager $manager)
    {
        $address1 = new Address();
        $address1->setAddressFirstName('Standard');
        $address1->setAddressLastName('Billing');
        $address1->setAddressStreet('200 Somewhere Street');
        $address1->setAddressCity('Brooklyn');
        $address1->setAddressZipcode('11209');
        $address1->setAddressCountry('USA');
        $address1->setUser($this->getReference('user-1'));
        $address1->setState($this->getReference('state-1'));
        $manager->persist($address1);
        $this->getReference('user-1')->setAddressBilling($address1);
        
        $address2 = new Address();
        $address2->setAddressFirstName('Standard');
        $address2->setAddressLastName('Other');
        $address2->setAddressStreet('100 Old Address');
        $address2->setAddressCity('New York');
        $address2->setAddressZipcode('12180');
        $address2->setAddressCountry('USA');
        $address2->setUser($this->getReference('user-1'));
        $address2->setState($this->getReference('state-1'));
        $manager->persist($address2);
        
        $address3 = new Address();
        $address3->setAddressFirstName('Standard');
        $address3->setAddressLastName('Shipping');
        $address3->setAddressStreet('200 Somewhere Street');
        $address3->setAddressCity('Brooklyn');
        $address3->setAddressZipcode('11209');
        $address3->setAddressCountry('USA');
        $address3->setUser($this->getReference('user-1'));
        $address3->setState($this->getReference('state-1'));
        $manager->persist($address3);
        $this->getReference('user-1')->setAddressShipping($address3);
        
        $address4 = new Address();
        $address4->setAddressFirstName('Standard');
        $address4->setAddressLastName('Other Older');
        $address4->setAddressStreet('25 Some Other Place');
        $address4->setAddressCity('Mechanicville');
        $address4->setAddressZipcode('12118');
        $address4->setAddressCountry('USA');
        $address4->setUser($this->getReference('user-1'));
        $address4->setState($this->getReference('state-1'));
        $manager->persist($address4);
        
        $address5 = new Address();
        $address5->setAddressFirstName('Admin');
        $address5->setAddressLastName('Standard');
        $address5->setAddressStreet('35 Some Street');
        $address5->setAddressCity('Boston');
        $address5->setAddressZipcode('06543');
        $address5->setAddressCountry('USA');
        $address5->setUser($this->getReference('user-2'));
        $address5->setState($this->getReference('state-2'));
        $manager->persist($address5);

        $manager->flush();        
    }
}
