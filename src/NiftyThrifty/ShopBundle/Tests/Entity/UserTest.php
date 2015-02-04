<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\User;
use NiftyThrifty\ShopBundle\Tests\Fixture\AddressData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;
use NiftyThrifty\ShopBundle\Tests\Fixture\InvoiceData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserInvitationData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserPaymentProfileData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserViewedProductData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * Tests for the validator methods.
 */
class UserTest extends WebTestCase
{
    public $testUser;
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
        $this->testUser = new User();
        $this->testUser
             ->setUserFirstName('Validator')
             ->setUserLastName('Test')
             ->setUserEmail('validationtest@niftythrifty.com')
             ->setUserPassword('niftythrifty');
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidUser()
    {
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserFirstNameBlank()
    {
        $this->testUser->setUserFirstName(null);
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'First name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userFirstName');
    }

    public function testUserFirstNameTooLong()
    {
        $this->testUser->setUserFirstName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'First name must be less than 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userFirstName');
    }

    public function testUserLastNameBlank()
    {
        $this->testUser->setUserLastName(null);
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Last name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userLastName');
    }

    public function testUserLastNameTooLong()
    {
        $this->testUser->setUserLastName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Last name must be less than 60 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userLastName');
    }

    public function testUserEmailBlank()
    {
        $this->testUser->setUserEmail(null);
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'E-mail address can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userEmail');
    }

    public function testUserEmailBadValue()
    {
        $this->testUser->setUserEmail('test');
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'E-mail address must be valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userEmail');
    }

    public function testUserNameUniquePass()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testUser->setUserEmail('unique.name@niftythrifty.com');
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(0, $violationList->count());

    }

    public function testUserNameUniqueFail()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testUser->setUserEmail('ut_inactive@niftythrifty.com');
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The entered e-mail address already exists.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userEmail');
    }

    public function testUserPasswordBlank()
    {
        $this->testUser->setUserPassword(null);
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Password may not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userPassword');
    }

    public function testUserPasswordTooShort()
    {
        $this->testUser->setUserPassword('test');
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Password must be more than 6 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userPassword');
    }

    public function testUserPasswordTooLong()
    {
        $this->testUser->setUserPassword('thispasswordiswaytoolong');
        $violationList = $this->validator->validate($this->testUser, array('accountInfo', 'passwordCheck'));
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Password must be fewer than 16 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userPassword');
    }
    
    public function testGetInviteToken()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertEquals('b4d7b6b9f0', $user->getInviteToken());
    }
    
    public function testGetRolesNonAdmin()
    {
        $this->testUser->setUserAdmin('false');
        $expected = array('ROLE_USER');
        
        $this->assertEquals($expected, $this->testUser->getRoles());
    }
    
    public function testGetRolesAdmin()
    {
        $this->testUser->setUserAdmin('true');
        $expected = array('ROLE_ADMIN');
        
        $this->assertEquals($expected, $this->testUser->getRoles());
    }
    
    public function testSetCreationTime()
    {
        $this->testUser
             ->setUserDateCreation(null)
             ->setUserDateLastConnection(null)
             ->setUserActive(null)
             ->setUserAdmin(null);
             
        $this->testUser->setCreationTime();
        $this->assertNotNull($this->testUser->getUserDateCreation());
        $this->assertNotNull($this->testUser->getUserDateCreation());
        $this->assertEquals($this->testUser->getUserActive(),   'true');
        $this->assertEquals($this->testUser->getUserAdmin(),    'false');
    }
    
    public function testAssociationBaskets()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new BasketData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertCount(2, $user->getBaskets());
    }
    
    public function testAssociationAddresses()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new AddressData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertCount(4, $user->getAddresses());
    }
    
    public function testAssociationInvoices()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new InvoiceData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertCount(1, $user->getInvoices());
    }
    
    public function testAssociationUserPaymentProfiles()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserPaymentProfileData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertCount(3, $user->getUserPaymentProfiles());
    }
    
    public function testAssociationUserInvitations()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserInvitationData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertCount(5, $user->getUserInvitations());
        $invitations = $user->getUserInvitations();
        $this->assertEquals($invitations[0]->getUserInvitationId(), 4);
        $this->assertEquals($invitations[1]->getUserInvitationId(), 2);
        $this->assertEquals($invitations[2]->getUserInvitationId(), 1);
        $this->assertEquals($invitations[3]->getUserInvitationId(), 3);
        $this->assertEquals($invitations[4]->getUserInvitationId(), 5);
    }
    
    public function testAssociationAddressShipping()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new AddressData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertEquals($user->getAddressBilling()->getAddressId(), 1);
    }
    
    public function testAssociationAddressBilling()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new AddressData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertEquals($user->getAddressShipping()->getAddressId(), 3);
    }
    
    public function testAssociationUserLovedProducts()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        
        $productIds         = array(4,2);
        $userLovedProducts  = $user->getUserLovedProducts();
        $this->assertCount(2, $userLovedProducts);
        $firstLove = $userLovedProducts->first();
        $userLovedProducts->next();
        $nextLove  = $userLovedProducts->current();

        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $firstLove->getProduct());
        $this->assertEquals($firstLove->getProductId(),                     $firstLove->getProduct()->getProductId());
        $this->assertTrue(in_array($firstLove->getProduct()->getProductId(),$productIds));
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $nextLove->getProduct());
        $this->assertEquals($nextLove->getProductId(),                      $nextLove->getProduct()->getProductId());
        $this->assertTrue(in_array($nextLove->getProduct()->getProductId(), $productIds));
        $this->assertNotEquals($firstLove->getProduct()->getProductId(),    $nextLove->getProduct()->getProductId());
    }
    
    public function testAssociationLovedProducts()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        
        $productIds     = array(1,2,4);
        $lovedProducts  = $user->getLovedProducts();
        $this->assertCount(3, $lovedProducts);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $lovedProducts[0]);
        $this->assertEquals($lovedProducts[0]->getProductId(),              $lovedProducts[0]->getProductId());
        $this->assertTrue(in_array($lovedProducts[0]->getProductId(),       $productIds));
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $lovedProducts[1]);
        $this->assertEquals($lovedProducts[1]->getProductId(),              $lovedProducts[1]->getProductId());
        $this->assertTrue(in_array($lovedProducts[1]->getProductId(),       $productIds));
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $lovedProducts[2]);
        $this->assertEquals($lovedProducts[2]->getProductId(),              $lovedProducts[2]->getProductId());
        $this->assertTrue(in_array($lovedProducts[2]->getProductId(),       $productIds));
    }

    public function testGetLovedProductIdsEmpty()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);

        $this->assertEquals($user->getLovedProductIds(), array());
    }

    public function testGetLovedProductsFull()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $expected = array(2,4);
        $productIds = $user->getLovedProductIds();
        sort($productIds);
        $this->assertEquals($productIds, $expected);
    }

    public function testIsLoved()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $expected = array(2,4);
        $productIds = $user->getLovedProductIds();
        sort($productIds);
        $this->assertEquals($productIds, $expected);
        $this->assertTrue($user->isLoved(2));
        $this->assertTrue($user->isLoved(4));
        $this->assertFalse($user->isLoved(1));
        $this->assertFalse($user->isLoved(10));
    }

    public function testIsLovedArrayNotLoaded()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserLovedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $this->assertTrue($user->isLoved(2));
        $this->assertTrue($user->isLoved(4));
        $this->assertFalse($user->isLoved(1));
        $this->assertFalse($user->isLoved(10));
    }

    public function testAssociationUserViewedProduct()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserViewedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        
        $productIds         = array(1,2);
        $userViewedProducts  = $user->getUserViewedProducts();
        $this->assertCount(2, $userViewedProducts);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',              $userViewedProducts[0]->getProduct());
        $this->assertEquals($userViewedProducts[0]->getProductId(),                      $userViewedProducts[0]->getProduct()->getProductId());
        $this->assertTrue(in_array($userViewedProducts[0]->getProduct()->getProductId(), $productIds));
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',              $userViewedProducts[1]->getProduct());
        $this->assertEquals($userViewedProducts[1]->getProductId(),                      $userViewedProducts[1]->getProduct()->getProductId());
        $this->assertTrue(in_array($userViewedProducts[1]->getProduct()->getProductId(), $productIds));
        $this->assertNotEquals($userViewedProducts[0]->getProduct()->getProductId(),     $userViewedProducts[1]->getProduct()->getProductId());
    }

    public function testAssociationViewedProducts()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserViewedProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        
        $productIds     = array(1,2);
        $lovedProducts  = $user->getViewedProducts();
        $this->assertCount(2, $lovedProducts);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $lovedProducts[0]);
        $this->assertEquals($lovedProducts[0]->getProductId(),              $lovedProducts[0]->getProductId());
        $this->assertTrue(in_array($lovedProducts[0]->getProductId(),       $productIds));
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Product',  $lovedProducts[1]);
        $this->assertEquals($lovedProducts[1]->getProductId(),              $lovedProducts[1]->getProductId());
        $this->assertTrue(in_array($lovedProducts[1]->getProductId(),       $productIds));
    }
}
