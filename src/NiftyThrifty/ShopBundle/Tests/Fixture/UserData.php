<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\User;

class UserData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUserFirstName('Standard');
        $user1->setUserLastName('User');
        $user1->setUserEmail('ut_user');
        $user1->setUserPassword('ee59fc9d98f6e781f7063396ca7489f9e2e05b34');  // hash for ut_userpass
        $date1c = new \DateTime();
        $date1c->modify("-10 days");
        $date1l = new \DateTime();
        $date1l->modify("-3 days");
        $user1->setUserDateCreation($date1c);
        $user1->setUserDateLastConnection($date1l);
        $user1->setUserActive('true');
        $user1->setUserAdmin('false');
        $this->addReference('user-1', $user1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUserFirstName('Standard');
        $user2->setUserLastName('Admin');
        $user2->setUserEmail('ut_admin');
        $user2->setUserPassword('eb0b8d4608b447772c1e5f4d4712251e3cddb158');  // hash for ut_adminpass
        $date2c = new \DateTime();
        $date2c->modify("-10 days");
        $date2l = new \DateTime();
        $date2l->modify("-3 days");
        $user2->setUserDateCreation($date2c);
        $user2->setUserDateLastConnection($date2l);
        $user2->setUserActive('true');
        $user2->setUserAdmin('true');
        $this->addReference('user-2', $user2);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUserFirstName('Inactive');
        $user3->setUserLastName('User');
        $user3->setUserEmail('ut_inactive@niftythrifty.com');
        $user3->setUserPassword('18a892636d13509bedd6229a6b476dafc8368077');  // hash for ut_inactivepass
        $date3c = new \DateTime();
        $date3c->modify("-25 days");
        $date3l = new \DateTime();
        $date3l->modify("-10 days");
        $user3->setUserDateCreation($date2c);
        $user3->setUserDateLastConnection($date2l);
        $user3->setUserActive('false');
        $user3->setUserAdmin('false');
        $this->addReference('user-3', $user3);
        $manager->persist($user3);


/**

Functions for later.

$user->setInstagramId();
$user->setInstagramAccessToken();
$user->setFbId();
$user->setAddressIdShipping();
$user->setAddressIdBilling();
**/
        $manager->flush();
    }    
}
