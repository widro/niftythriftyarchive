<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\ProductCategorySize;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class ProductCategorySizeTest extends WebTestCase
{
    public $testProductCategorySize;
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
        $this->testProductCategorySize = new ProductCategorySize();
        $this->testProductCategorySize
             ->setProductCategorySizeName('Unit Test Category Size')
             ->setProductCategorySizeValue('UT')
             ->setProductCategorySizeOrder(1)
             ->setProductCategoryId(2);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidProductCategorySize()
    {
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductCategorySizeNameBlank()
    {
        $this->testProductCategorySize->setProductCategorySizeName(null);
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Size name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeName');
    }

    public function testProductCategorySizeNameTooLong()
    {
        $this->testProductCategorySize->setProductCategorySizeName(str_repeat('x', 64));
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Size name must be less than 63 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeName');
    }

    public function testProductCategorySizeValueBlank()
    {
        $this->testProductCategorySize->setProductCategorySizeValue(null);
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Size value can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeValue');
    }

    public function testProductCategorySizeValueTooLong()
    {
        $this->testProductCategorySize->setProductCategorySizeValue(str_repeat('x', 64));
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Size value must be less than 63 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeValue');
    }

    public function testProductCategorySizeOrderBlank()
    {
        $this->testProductCategorySize->setProductCategorySizeOrder(null);
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order value can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeOrder');
    }

    public function testProductCategorySizeOrderBadValue()
    {
        $this->testProductCategorySize->setProductCategorySizeOrder('tst');
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Order value must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeOrder');
    }

    public function testProductCategoryIdBlank()
    {
        $this->testProductCategorySize->setProductCategoryId(null);
        $violationList = $this->validator->validate($this->testProductCategorySize);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Category must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategoryId');
    }

    public function testGetId()
    {
        $this->assertEquals($this->testProductCategorySize->getId(), $this->testProductCategorySize->getProductCategorySizeId());
    }

    public function testToString()
    {
        $this->assertEquals($this->testProductCategorySize, $this->testProductCategorySize->getProductCategorySizeName());
    }

    public function testAssociationProducts()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productCategorySize = $this->em->getRepository('NiftyThriftyShopBundle:ProductCategorySize')->find(1);
        $this->assertEquals($productCategorySize->getProducts()->count(), 5);
    }

    public function testAssociationProductCategory()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $productCategorySize = $this->em->getRepository('NiftyThriftyShopBundle:ProductCategorySize')->find(1);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\ProductCategory', $productCategorySize->getProductCategory());
        $this->assertEquals($productCategorySize->getProductCategory()->getProductCategoryId(), 1);
    }
}
