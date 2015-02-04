<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Address;
use NiftyThrifty\ShopBundle\Entity\State;
use NiftyThrifty\ShopBundle\Tests\Fixture\AddressData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class AddressTest extends WebTestCase
{
    public $testAddress;
    public $validator;
    public $em;
    public $container;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->em        = $kernel->getContainer()->get('doctrine')->getManager();
        $this->container = $kernel->getContainer();
        $this->testAddress = new Address();
        $this->testAddress->setUserId(1)
                          ->setAddressFirstName('Test')
                          ->setAddressLastName('User')
                          ->setAddressStreet('123 Somewhere Place')
                          ->setAddressCity('Brooklyn')
                          ->setStateId(1)
                          ->setState(new State())
                          ->setAddressZipcode(11209)
                          ->setAddressCountry('USA');
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }
    
    public function testValidAddress()
    {
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testBlankUserId()
    {
        $this->testAddress->setUserId(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'User can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userId');
    }

    public function testBlankFirstName()
    {
        $this->testAddress->setAddressFirstName(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'First name can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressFirstName');
    }

    public function testFirstNameTooLong()
    {
        $this->testAddress->setAddressFirstName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'First name must be less than 60 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressFirstName');
    }

    public function testBlankLastName()
    {
        $this->testAddress->setAddressLastName(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Last name can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressLastName');
    }

    public function testLastNameTooLong()
    {
        $this->testAddress->setAddressLastName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Last name must be less than 60 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressLastName');
    }

    public function testBlankStreet()
    {
        $this->testAddress->setAddressStreet(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Street address can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressStreet');
    }

    public function testStreetTooLong()
    {
        $this->testAddress->setAddressStreet(str_repeat('x', 255));
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Street address must be less than 255 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressStreet');
    }

    public function testBlankCity()
    {
        $this->testAddress->setAddressCity(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'City can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressCity');
    }

    public function testCityTooLong()
    {
        $this->testAddress->setAddressCity(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'City must be less than 50 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressCity');
    }

    public function testBlankState()
    {
        $this->testAddress->setState(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'State must be selected');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'state');
    }

    public function testBlankZipcode()
    {
        $this->testAddress->setAddressZipcode(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Zip code can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressZipcode');
    }

    public function testZipcodeRegexNoHyphen()
    {
        $this->testAddress->setAddressZipcode(123456789);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Zip code must be 5 digits or 9 digits with a hyphen');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressZipcode');
    }

    public function testZipcodeRegexMisplacedHyphen()
    {
        $this->testAddress->setAddressZipcode('123-456789');
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Zip code must be 5 digits or 9 digits with a hyphen');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressZipcode');
    }

    public function testZipcodeRegexNinePasses()
    {
        $this->testAddress->setAddressZipcode('12345-6789');
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(0, $violationList->count());
    }

    public function testZipcodeRegexTooFewDigits()
    {
        $this->testAddress->setAddressZipcode(1234);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Zip code must be 5 digits or 9 digits with a hyphen');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressZipcode');
    }

    public function testZipcodeRegexTooMany()
    {
        $this->testAddress->setAddressZipcode(123456789123);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Zip code must be 5 digits or 9 digits with a hyphen');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressZipcode');
    }

    public function testBlankCountry()
    {
        $this->testAddress->setAddressCountry(null);
        $violationList = $this->validator->validate($this->testAddress);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Country can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'addressCountry');
    }

    public function testConstants()
    {
        $this->assertEquals(Address::TYPE_SHIPPING, 'shipping');
        $this->assertEquals(Address::TYPE_BILLING,  'billing');
    }

    public function testAssociationUser()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new AddressData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $address = $this->em->getRepository('NiftyThriftyShopBundle:Address')->find(1);
        $this->assertEquals($address->getUser()->getUserId(), 1);
    }

    public function testAssociationState()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new AddressData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $address = $this->em->getRepository('NiftyThriftyShopBundle:Address')->find(1);
        $this->assertEquals($address->getState()->getStateId(), 1);
    }
}
