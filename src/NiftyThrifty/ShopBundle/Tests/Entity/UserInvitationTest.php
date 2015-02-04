<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\UserInvitation;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserInvitationData;

/**
 * Tests for the validator methods.
 */
class UserInvitationTest extends WebTestCase
{
    public $testInvitation;
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
        $this->testInvitation = new UserInvitation();
        $nowTime = new \DateTime();
        $this->testInvitation->setUserInvitationStatus('pending')
                             ->setUserInvitationDate($nowTime)
                             ->setUserInvitationType('mail')
                             ->setUserInvitationContent('e-mail body')
                             ->setUserInvitationEmail('tom@niftythrifty.com')
                             ->setUserId(1);
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testUserInvitationValid()
    {
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testStatusAcceptedPass()
    {
        $this->testInvitation->setUserInvitationStatus('accepted');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testStatusSpendPass()
    {
        $this->testInvitation->setUserInvitationStatus('spend');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testStatusBlank()
    {
        $this->testInvitation->setUserInvitationStatus(null);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Status may not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationStatus');
    }

    public function testStatusInvalidValue()
    {
        $this->testInvitation->setUserInvitationStatus('tom');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid status.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationStatus');
    }

    public function testCreationDateBlank()
    {
        $this->testInvitation->setUserInvitationDate(null);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Date may not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationDate');
    }

    public function testCreationDateInvalidDate()
    {
        $this->testInvitation->setUserInvitationDate('tom');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Not a valid date.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationDate');
    }

    public function testUserInvitationTypeLinkPass()
    {
        $this->testInvitation->setUserInvitationType('link');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserInvitationTypeTwitterPass()
    {
        $this->testInvitation->setUserInvitationType('twitter');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserInvitationTypeFacebookPass()
    {
        $this->testInvitation->setUserInvitationType('facebook');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserInvitationTypeBookAddressPass()
    {
        $this->testInvitation->setUserInvitationType('book_address');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUserInvitationTypeBlank()
    {
        $this->testInvitation->setUserInvitationType(null);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Type may not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationType');
    }

    public function testUserInvitationTypeInvalidValue()
    {
        $this->testInvitation->setUserInvitationType('tom');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Invalid type.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationType');
    }

    public function testUserIdBlank()
    {
        $this->testInvitation->setUserId(null);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Inviting user must be defined.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userId');
    }

    public function testFirstNameValid()
    {
        $this->testInvitation->setUserInvitationFirstName('Tom');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testFirstNameTooLong()
    {
        $this->testInvitation->setUserInvitationFirstName(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'First name must be less than 255 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationFirstName');
    }

    public function testLastNameValid()
    {
        $this->testInvitation->setUserInvitationLastName('Phillips');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testLastNameTooLong()
    {
        $this->testInvitation->setUserInvitationLastName(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Last name must be less than 255 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationLastName');
    }

    public function testOneIdDefined()
    {
        $this->testInvitation->setUserInvitationEmail(null)
                             ->setUserInvitationFbId(null)
                             ->setUserInvitationTwitterId(null);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'One of e-mail, Facebook id, or Twitter id must be included.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationEmail');
    }

    public function testInvalidEmail()
    {
        $this->testInvitation->setUserInvitationEmail('tom.com');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Entered e-mail is not valid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationEmail');
    }

    public function testEmailDefinedContentBlank()
    {
        $this->testInvitation->setUserInvitationEmail('tom@niftythrifty.com')
                             ->setUserInvitationContent(null);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Message content may not be blank for an e-mail invitation.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationContent');
    }

    public function testValidWithFixture()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserInvitationData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(0, $violationList->count());
    }

    public function testUniqueEmail()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserInvitationData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testInvitation->setUserInvitationEmail('test1@niftythrifty.com');
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'This user has already been invited.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationEmail');
    }

    public function testUniqueFbId()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserInvitationData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testInvitation->setUserInvitationEmail(null)
                             ->setUserInvitationFbId(123);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'This Facebook user has already been invited.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationFbId');
    }

    public function testUniqueTwitterId()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new UserInvitationData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());

        $this->testInvitation->setUserInvitationEmail(null)
                             ->setUserInvitationTwitterId(234);
        $violationList = $this->validator->validate($this->testInvitation);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'This Twitter user has already been invited.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'userInvitationTwitterId');
    }

    public function testConstants()
    {
        $expected = "I'm inviting you to join NiftyThrifty, where expert curators deliver rare vintage finds, everyday. Membership is free, so join now! Please click here and use the link to join.";

        $this->assertEquals(UserInvitation::DEFAULT_INVITE_TEXT,$expected);
        $this->assertEquals(UserInvitation::STATUS_PENDING,     'pending');
        $this->assertEquals(UserInvitation::STATUS_ACCEPTED,    'accepted');
        $this->assertEquals(UserInvitation::STATUS_SPEND,       'spend');
        $this->assertEquals(UserInvitation::TYPE_LINK,          'link');
        $this->assertEquals(UserInvitation::TYPE_TWITTER,       'twitter');
        $this->assertEquals(UserInvitation::TYPE_FACEBOOK,      'facebook');
        $this->assertEquals(UserInvitation::TYPE_BOOK_ADDRESS,  'book_address');
        $this->assertEquals(UserInvitation::TYPE_MAIL,          'mail');
    }

    public function testGetRefererFacebook()
    {
        $this->assertEquals($this->testInvitation->getReferer('http://www.facebook.com'), 'facebook');
    }

    public function testGetRefererTwitter()
    {
        $this->assertEquals($this->testInvitation->getReferer('http://www.twitter.com'), 'twitter');
    }

    public function testGetRefererOther()
    {
        $this->assertEquals('mail', $this->testInvitation->getReferer('http://www.niftythrifty.com'));
    }
}
