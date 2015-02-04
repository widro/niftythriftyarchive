<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Designer;
use NiftyThrifty\ShopBundle\Tests\Fixture\DesignerData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class DesignerTest extends WebTestCase
{
    public $testDesigner;
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
        $this->testDesigner = new Designer();
        $this->testDesigner->setDesignerName('Designer');
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }                      

    public function testValidDesigner()
    {
        $violationList = $this->validator->validate($this->testDesigner);
        $this->assertEquals(0, $violationList->count());
    }

    public function testDesignerNameBlank()
    {
        $this->testDesigner->setDesignerName(null);
        $violationList = $this->validator->validate($this->testDesigner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Designer name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'designerName');
    }

    public function testDesignerNameTooLong()
    {
        $this->testDesigner->setDesignerName(str_repeat('x', 51));
        $violationList = $this->validator->validate($this->testDesigner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Designer name must be less than 50 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'designerName');
    }

    public function testDesignerNameUniquePass()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new DesignerData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testDesigner->setDesignerName('U-NEEQ');
        $violationList = $this->validator->validate($this->testDesigner);
        $this->assertEquals(0, $violationList->count());

    }

    public function testDesignerNameUniqueFail()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new DesignerData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testDesigner->setDesignerName('Prada');
        $violationList = $this->validator->validate($this->testDesigner);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The designer already exists');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'designerName');
    }

    public function testGetId()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new DesignerData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $designer = $this->em->getRepository('NiftyThriftyShopBundle:Designer')->findOneByDesignerName('Prada');
        $this->assertEquals($designer->getId(), $designer->getDesignerId());
    }

    public function testProductAssociation()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $designer = $this->em->getRepository('NiftyThriftyShopBundle:Designer')->findOneByDesignerName('Prada');
        $this->assertEquals(5, $designer->getProducts()->count());
    }

    public function testToStringMethod()
    {
        $this->assertEquals($this->testDesigner, $this->testDesigner->getDesignerName());
    }
}
