<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\ProductCategory;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategoryData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategorySizeData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class ProductCategoryTest extends WebTestCase
{
    public $testProductCategory;
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
        $this->testProductCategory = new ProductCategory();
        $this->testProductCategory
             ->setProductCategoryName('Unit Test Category')
             ->setInNavigation('yes')
             ->setNavigationOrder(1);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidProductCategory()
    {
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductCategoryNameBlank()
    {
        $this->testProductCategory->setProductCategoryName(null);
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Category name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategoryName');
    }

    public function testProductCategoryNameTooLong()
    {
        $this->testProductCategory->setProductCategoryName(str_repeat('x', 64));
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Category name must be less than 63 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategoryName');
    }

    public function testProductCategoryNameUniquePass()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductCategoryData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductCategory->setProductCategoryName('U-NEEQ');
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(0, $violationList->count());

    }

    public function testProductCategoryNameUniqueFail()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductCategoryData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testProductCategory->setProductCategoryName('Jumpers');
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The category already exists');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategoryName');
    }

    public function testProductCategoryInNavigationBlank()
    {
        $this->testProductCategory->setInNavigation(null);
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Please select if the category is in navigation.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'inNavigation');
    }

    public function testProductCategoryInNavigationBadValue()
    {
        $this->testProductCategory->setInNavigation('test');
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid value for in navigation.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'inNavigation');
    }

    public function testProductCategoryNavigationOrderBlankPass()
    {
        $this->testProductCategory->setNavigationOrder(null);
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductCategoryNavigationBadValue()
    {
        $this->testProductCategory->setNavigationOrder('test');
        $violationList = $this->validator->validate($this->testProductCategory);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'navigationOrder');
    }

    public function testGetId()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductCategoryData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productCategory = $this->em->getRepository('NiftyThriftyShopBundle:ProductCategory')->find(1);
        $this->assertEquals($productCategory->getId(), $productCategory->getProductCategoryId());
    }

    public function testGetName()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductCategoryData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productCategory = $this->em->getRepository('NiftyThriftyShopBundle:ProductCategory')->find(1);
        $this->assertEquals($productCategory->getName(), $productCategory->getProductCategoryName());
    }

    public function testProductCategorySizesAssociation()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductCategorySizeData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productCategory = $this->em->getRepository('NiftyThriftyShopBundle:ProductCategory')->find(1);
        $this->assertEquals(2, $productCategory->getProductCategorySizes()->count());
    }

    public function testToStringMethod()
    {
        $this->assertEquals($this->testProductCategory, $this->testProductCategory->getProductCategoryName());
    }
}
