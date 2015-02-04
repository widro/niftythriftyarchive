<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\UserInvitation;

class UserInvitationData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Sizes require their categories to be loaded.
     */
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\UserData');
    }

    public function load(ObjectManager $manager)
    {
        // Pending e-mail invitation
        $inviteTime = new \DateTime("-10 days");
        $invitation1 = new UserInvitation();
        $invitation1->setUserInvitationStatus('pending')
                    ->setUserInvitationDate($inviteTime)
                    ->setUserInvitationType('mail')
                    ->setUserInvitationContent('Invite content 1')
                    ->setUserInvitationEmail('test1@niftythrifty.com')
                    ->setUserId(1)
                    ->setInvitingUser($this->getReference('user-1'));
        $manager->persist($invitation1);

        // Pending Facebook invitation
        $inviteTime = new \DateTime("-8 days");
        $invitation2 = new UserInvitation();
        $invitation2->setUserInvitationStatus('pending')
                    ->setUserInvitationDate($inviteTime)
                    ->setUserInvitationType('facebook')
                    ->setUserInvitationFbId(123)
                    ->setUserId(1)
                    ->setInvitingUser($this->getReference('user-1'));
        $manager->persist($invitation2);

        // Pending Twitter invitation
        $inviteTime = new \DateTime("-12 days");
        $invitation3 = new UserInvitation();
        $invitation3->setUserInvitationStatus('pending')
                    ->setUserInvitationDate($inviteTime)
                    ->setUserInvitationType('twitter')
                    ->setUserInvitationTwitterId(234)
                    ->setUserId(1)
                    ->setInvitingUser($this->getReference('user-1'));
        $manager->persist($invitation3);

        // Accepted e-mail invitation
        $inviteTime = new \DateTime("-5 days");
        $invitation4 = new UserInvitation();
        $invitation4->setUserInvitationStatus('accepted')
                    ->setUserInvitationLastName('User2')
                    ->setUserInvitationFirstName('Test2')
                    ->setUserInvitationDate($inviteTime)
                    ->setUserInvitationType('mail')
                    ->setUserInvitationContent('Invite content 2')
                    ->setUserInvitationEmail('ut_inactive@niftythrifty.com')
                    ->setUserInvitationUserId(3)
                    ->setInvitingUser($this->getReference('user-1'))
                    ->setUserId(1);
        $manager->persist($invitation4);

        // Spent e-mail invitation
        $inviteTime = new \DateTime("-15 days");
        $invitation5 = new UserInvitation();
        $invitation5->setUserInvitationStatus('spend')
                    ->setUserInvitationLastName('User3')
                    ->setUserInvitationFirstName('Test3')
                    ->setUserInvitationDate($inviteTime)
                    ->setUserInvitationType('mail')
                    ->setUserInvitationContent('Invite content 3')
                    ->setUserInvitationEmail('test3@niftythrifty.com')
                    ->setUserInvitationUserId(3)
                    ->setInvitingUser($this->getReference('user-1'))
                    ->setUserId(1);
        $manager->persist($invitation5);

        // Extra e-mail invitation
        $invitation6 = new UserInvitation();
        $invitation6->setUserInvitationStatus('spend')
                    ->setUserInvitationLastName('User4')
                    ->setUserInvitationFirstName('Test4')
                    ->setUserInvitationDate($inviteTime)
                    ->setUserInvitationType('mail')
                    ->setUserInvitationContent('Invite content 4')
                    ->setUserInvitationEmail('test4@niftythrifty.com')
                    ->setUserInvitationUserId(1)
                    ->setInvitingUser($this->getReference('user-2'))
                    ->setUserId(2);
        $manager->persist($invitation6);

        $manager->flush();
    }
}
