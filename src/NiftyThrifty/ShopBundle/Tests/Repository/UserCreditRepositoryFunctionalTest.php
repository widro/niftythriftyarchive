<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserCreditsData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;

class UserCreditsRepositoryFunctionalTest extends NiftyBaseTestCase
{
    public function testGetUserCreditsMultiple()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $creditCount = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals($creditCount, 14);
    }

    public function testGetUserCreditsSingle()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $creditCount = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(2);
        $this->assertEquals($creditCount, 25);
    }

    public function testGetUserCreditsNoCredits()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $creditCount = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(3);
        $this->assertEquals($creditCount, 0);
    }
}
