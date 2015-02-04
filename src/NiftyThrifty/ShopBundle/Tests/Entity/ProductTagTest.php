<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\ProductTag;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class ProductTagTest extends WebTestCase
{
    public $testProductTag;
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
        $this->testProductTag = new ProductTag();
        $this->testProductTag
             ->setProductTagName('Validator Tag')
             ->setProductTagSlug('validator-tag')
             ->setProductTagtypeid(1);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidProductTag()
    {
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductTagNameBlank()
    {
        $this->testProductTag->setProductTagName(null);
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagName');
    }

    public function testProductTagNameTooLong()
    {
        $this->testProductTag->setProductTagName(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag name must be less than 50 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagName');
    }

    public function testProductTagNameUniquePass()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductTag
             ->setProductTagName('U-NEEQ')
             ->setProductTagtypeId(1);
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductTagNameUniqueAtLevelPass()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductTag
             ->setProductTagName('red')
             ->setProductTagtypeId(2);
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductTagNameUniqueFail()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductTag
             ->setProductTagName('red')
             ->setProductTagtypeId(1);
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag already exists for this tag type.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagName');
    }

    public function testProductTagSlugBlank()
    {
        $this->testProductTag->setProductTagSlug(null);
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag slug can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagSlug');
    }

    public function testProductTagSlugTooLong()
    {
        $this->testProductTag->setProductTagSlug(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tag slug must be less than 50 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagSlug');
    }

    public function testProductTagtypeIdBlank()
    {
        $this->testProductTag->setProductTagtypeId(null);
        $violationList = $this->validator->validate($this->testProductTag);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Please select tag type.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTagtypeId');
    }

    public function testGetId()
    {
        $this->assertEquals($this->testProductTag->getId(), $this->testProductTag->getProductTagId());
    }

    public function testGetName()
    {
        $this->assertEquals($this->testProductTag->getName(), $this->testProductTag->getProductTagName());
    }

    public function testToStringMethod()
    {
        $this->assertEquals($this->testProductTag, $this->testProductTag->getProductTagName());
    }

    public function testProductTagtypeAssociation()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductTagData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productTag = $this->em->getRepository('NiftyThriftyShopBundle:ProductTag')->find(1);
        $this->assertEquals($productTag->getProductTagtype()->getProductTagtypeId(), 1);
    }
}
