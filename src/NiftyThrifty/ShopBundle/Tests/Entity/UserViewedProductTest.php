<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\UserViewedProduct;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserViewedProductData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class UserViewedProductTest extends WebTestCase
{
    public $testUserViewedProduct;
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
        $this->testUserViewedProduct = new UserViewedProduct();
        $nowTime = new \DateTime();
        $this->testUserViewedProduct
             ->setProductId(10)
             ->setUserId(10)
             ->setDateViewed($nowTime);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidUserViewedProduct()
    {
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testUserViewedProductIdBlank()
    {
        $this->testUserViewedProduct->setProductId(null);
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productId');
    }

    public function testUserViewedProductIdNotANumber()
    {
        $this->testUserViewedProduct->setProductId('dinkers');
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid product input.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productId');
    }
    public function testUserViewedUserIdBlank()
    {
        $this->testUserViewedProduct->setUserId(null);
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'User must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userId');
    }

    public function testUserViewedUserIdNotANumber()
    {
        $this->testUserViewedProduct->setUserId('dinkers');
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid user input.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userId');
    }
    
    public function testDateViewedInvalidDate()
    {
        $this->testUserViewedProduct->setDateViewed('dinkers');
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date viewed must be a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'dateViewed');
    }
    
    public function testDateLovedBlank()
    {
        $this->testUserViewedProduct->setDateViewed(null);
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date viewed is required.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'dateViewed');
    }

    public function testUserProductUnique()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserViewedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $nowTime    = new \DateTime();
        $this->testUserViewedProduct
             ->setUserId(1)
             ->setProductId(1)
             ->setDateViewed($nowTime);
        $violationList = $this->validator->validate($this->testUserViewedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(), 'This Viewed Item already exists.');
    }
    
    public function testUserProductUniqueValid()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserViewedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $nowTime    = new \DateTime();
        $user       = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $product    = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(3);
        $newViewed   = new UserViewedProduct();
        $newViewed->setProduct($product)
                 ->setUser($user)
                 ->setUserId($user->getUserId())
                 ->setProductId($product->getProductId())
                 ->setDateViewed($nowTime);
        $violationList = $this->validator->validate($newViewed);
        $this->assertEquals(0, $violationList->count());
        
        $this->em->persist($newViewed);
        $this->em->flush();
        
        $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(3,3);
        $this->assertEquals($viewed->getProductId(), 3);
        $this->assertEquals($viewed->getUserId(),    3);
    }
    
    public function testAssociationUser()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserViewedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(1,2);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\User', $viewed->getUser());
        $this->assertEquals(1, $viewed->getUser()->getUserId());
    }
    
    public function testAssociationProduct()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserViewedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $viewed = $this->em->getRepository('NiftyThriftyShopBundle:UserViewedProduct')->findByUserAndProduct(1,2);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product', $viewed->getProduct());
        $this->assertEquals(2, $viewed->getProduct()->getProductId());
    }
}
