<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\AddressData;

class AddressRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * Find all a user's addresses that aren't their default addresses.
     *
     * @group Repository
     * @group Address
     * @covers BasketRepository::findOtherAddressesForUser
     */
    public function testFindOtherAddressesForUser()
    {
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        $this->_updateUserFixtures();
        
        $addresses = $this->em
            ->getRepository('NiftyThriftyShopBundle:Address')
            ->findOtherAddressesForUser(1);
            
        $this->assertCount(2, $addresses);
        $this->assertEquals($addresses[0]->getAddressId(), 2);
        $this->assertEquals($addresses[1]->getAddressId(), 4);
    }
    
    /**
     * User and Address both have dependencies, so we have to update the user fixture
     * after the fixtures load to avoid an infinite referential loop.
     */
    private function _updateUserFixtures()
    {
        $user = $this->em
            ->getRepository('NiftyThriftyShopBundle:User')
            ->find(1);
        $address1 = $this->em
            ->getRepository('NiftyThriftyShopBundle:Address')
            ->find(1);
        $address2 = $this->em
            ->getRepository('NiftyThriftyShopBundle:Address')
            ->find(3);
            
        $user->setAddressShipping($address1);
        $user->setAddressBilling($address2);
        $this->em->flush();
    }
}
