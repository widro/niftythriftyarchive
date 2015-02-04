<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\ProductTagtype;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagtypeData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class ProductTagtypeTest extends WebTestCase
{
    public $testProductTagtype;
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
        $this->testProductTagtype = new ProductTagtype();
        $this->testProductTagtype->setProductTagtypeName('Validator Tag Type');
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidProductTagtype()
    {
        $violationList = $this->validator->validate($this->testProductTagtype);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductTagtypeNameBlank()
    {
        $this->testProductTagtype->setProductTagtypeName(null);
        $violationList = $this->validator->validate($this->testProductTagtype);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag type name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagtypeName');
    }

    public function testProductTagtypeNameTooLong()
    {
        $this->testProductTagtype->setProductTagtypeName(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testProductTagtype);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag type name must be less than 50 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagtypeName');
    }

    public function testProductTagtypeNameUniquePass()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagtypeData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductTagtype->setProductTagtypeName('U-NEEQ');
        $violationList = $this->validator->validate($this->testProductTagtype);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductTagtypeNameUniqueFail()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagtypeData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductTagtype->setProductTagtypeName('Color');
        $violationList = $this->validator->validate($this->testProductTagtype);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The tag type already exists.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagtypeName');
    }

    public function testConstants()
    {
        $this->assertEquals(1, ProductTagtype::GENERAL);
        $this->assertEquals(2, ProductTagtype::COLOR);
        $this->assertEquals(3, ProductTagtype::DECADE);
        $this->assertEquals(4, ProductTagtype::SUBCATEGORY);
        $this->assertEquals(5, ProductTagtype::ARCHETYPE);
    }

    public function testProductTagsAssociation()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productTagtype = $this->em->getRepository('NiftyThriftyShopBundle:ProductTagtype')->find(5);
        $this->assertCount(3, $productTagtype->getProductTags());
        $productTags = $productTagtype->getProductTags();
        $this->assertEquals(9,  $productTags[0]->getProductTagId());
        $this->assertEquals(8,  $productTags[1]->getProductTagId());
        $this->assertEquals(10, $productTags[2]->getProductTagId());
    }
}
