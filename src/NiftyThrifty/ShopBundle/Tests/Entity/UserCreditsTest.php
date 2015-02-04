<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\UserCredits;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class UserCreditsTest extends WebTestCase
{
    public $em;
    public $container;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em        = $kernel->getContainer()->get('doctrine')->getManager();
        $this->container = $kernel->getContainer();
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }
    
    public function testConstants()
    {
        $this->assertEquals(UserCredits::FIRST_PURCHASE_CREDITS, 25);
    }

    public function testSetNegativeCredits()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $userCredits = new UserCredits();
        $userCredits->setNegativeCredits($user, 10);

        $this->assertEquals($userCredits->getUserId(), $user->getUserId());
        $this->assertNotNull($userCredits->getUserCreditsDate());
        $this->assertNotNull($userCredits->getUserCreditsDateEnd());
        $this->assertEquals($userCredits->getUserCreditsDate(), $userCredits->getUserCreditsDateEnd());
        $this->assertEquals($userCredits->getUserCreditsValue(), -10);
    }
    
    public function testSetFirstBuyCredits()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $userCredits = new UserCredits();
        $userCredits->setFirstBuyCredits($user);

        $this->assertEquals($userCredits->getUserId(), $user->getUserId());
        $this->assertNotNull($userCredits->getUserCreditsDate());
        $this->assertNotNull($userCredits->getUserCreditsDateEnd());
        $this->assertEquals($userCredits->getUserCreditsDate()->diff($userCredits->getUserCreditsDateEnd())->format("%R%m months"), '+6 months');
        $this->assertEquals($userCredits->getUserCreditsValue(), UserCredits::FIRST_PURCHASE_CREDITS);
    }
}
