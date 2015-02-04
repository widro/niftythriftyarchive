<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;

class BasketRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * User's baskets persist indefinitely, so when a user is building a basket,
     * we want the one basket that is NOT purchased.
     *
     * @group Repository
     * @group Basket
     * @covers BasketRepository::findByUserOngoing
     */
    public function testFindByUserOngoing()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $basket = $this->em
            ->getRepository('NiftyThriftyShopBundle:Basket')
            ->findByUserOngoing(1);

        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Basket', $basket);
        $this->assertEquals($basket->getBasketId(), 2);
    }
    
    /**
     * Test that this returns null (a false value) if there is no ongoing basket
     *
     * @group Repository
     * @group Basket
     * @covers BasketRepository::findByUserOngoing
     */
    public function testFindByUserOngoingNoResult()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $basket = $this->em
            ->getRepository('NiftyThriftyShopBundle:Basket')
            ->findByUserOngoing(1);
            
        $this->assertNull($basket);
    }
}
