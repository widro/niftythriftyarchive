<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\UserLovedProduct;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

class UserLovedProductTest extends WebTestCase
{
    public $testUserLovedProduct;
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
        $this->testUserLovedProduct = new UserLovedProduct();
        $nowTime = new \DateTime();
        $this->testUserLovedProduct
             ->setProductId(10)
             ->setUserId(10)
             ->setDateLoved($nowTime)
             ->setLoveType(UserLovedProduct::LOVE_TYPE_BASKET)
             ->setIsDeleted(0);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidUserLovedProduct()
    {
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testUserLovedProductLoveTypeBlank()
    {
        $this->testUserLovedProduct->setLoveType(null);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Love type can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'loveType');
    }
    
    public function testUserLovedProductLoveTypeInvalid()
    {
        $this->testUserLovedProduct->setLoveType('dinkers');
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Choose a valid love type.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'loveType');
    }
    
    public function testUserLovedProductLoveTypeLinkValid()
    {
        $this->testUserLovedProduct->setLoveType(UserLovedProduct::LOVE_TYPE_LINK);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserLovedProductIsDeletedInvalid()
    {
        $this->testUserLovedProduct->setIsDeleted('dinkers');
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid is deleted value.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'isDeleted');
    }
    
    public function testUserLovedProductIsDeletedLinkValid()
    {
        $this->testUserLovedProduct->setIsDeleted(1);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserLovedProductIdBlank()
    {
        $this->testUserLovedProduct->setProductId(null);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productId');
    }

    public function testUserLovedProductIdNotANumber()
    {
        $this->testUserLovedProduct->setProductId('dinkers');
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid product input.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productId');
    }
    public function testUserLovedUserIdBlank()
    {
        $this->testUserLovedProduct->setUserId(null);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'User must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userId');
    }

    public function testUserLovedUserIdNotANumber()
    {
        $this->testUserLovedProduct->setUserId('dinkers');
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid user input.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userId');
    }
    
    public function testDateLovedInvalidDate()
    {
        $this->testUserLovedProduct->setDateLoved('dinkers');
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date loved is an invalid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'dateLoved');
    }
    
    public function testDateLovedBlank()
    {
        $this->testUserLovedProduct->setDateLoved(null);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date loved is required.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'dateLoved');
    }
    
    public function testUserProductUnique()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $this->testUserLovedProduct
             ->setUserId(1)
             ->setProductId(1);
        $violationList = $this->validator->validate($this->testUserLovedProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'This Loved Item already exists.');
    }
    
    public function testUserProductUniqueValid()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $nowTime    = new \DateTime();
        $user       = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $product    = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(3);
        $newLoved   = new UserLovedProduct();
        $newLoved->setProduct($product)
                 ->setUser($user)
                 ->setUserId($user->getUserId())
                 ->setProductId($product->getProductId())
                 ->setLoveType(UserLovedProduct::LOVE_TYPE_BASKET)
                 ->setDateLoved($nowTime)
                 ->setIsDeleted(0);
        $violationList = $this->validator->validate($newLoved);
        $this->assertEquals(0, $violationList->count());
        
        $this->em->persist($newLoved);
        $this->em->flush();
        
        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(3,3);
        $this->assertEquals($loved->getIsDeleted(), 0);
        $this->assertEquals($loved->getProductId(), 3);
        $this->assertEquals($loved->getUserId(),    3);
        $this->assertEquals($loved->getLoveType(),  UserLovedProduct::LOVE_TYPE_BASKET);
    }
    
    public function testConstants()
    {
        $this->assertEquals(UserLovedProduct::LOVE_TYPE_BASKET, 'basket');
        $this->assertEquals(UserLovedProduct::LOVE_TYPE_LINK,   'link');
    }
    
    public function testAssociationUser()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,2);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\User', $loved->getUser());
        $this->assertEquals(1, $loved->getUser()->getUserId());
    }
    
    public function testAssociationProduct()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,2);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product', $loved->getProduct());
        $this->assertEquals(2, $loved->getProduct()->getProductId());
    }
}