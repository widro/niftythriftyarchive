<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\UserPaymentProfile;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserPaymentProfileData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class UserPaymentProfileTest extends WebTestCase
{
    public $em;
    public $container;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->container = $kernel->getContainer();
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testToStringMethod()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserPaymentProfileData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $userPaymentProfile = $this->em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')->find(1);
        $dateTime = new \DateTime("+1 year");
        $dateString = $dateTime->format('Y-m');
        $this->assertEquals("*-1111 -- Exp: $dateString", $userPaymentProfile);

    }

    public function testAssociationUser()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserPaymentProfileData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $userPaymentProfile = $this->em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')->find(1);
        $this->assertEquals($userPaymentProfile->getUser()->getUserId(), 1);
    }
}
