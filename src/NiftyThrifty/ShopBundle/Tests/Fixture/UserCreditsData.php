<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\UserCredits;

class UserCreditsData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        // Full credit 1
        $userCredits1 = new UserCredits();
        $startDate1 = new \DateTime("-3 days");
        $endDate1   = new \DateTime("+3 days");
        $userCredits1->setUserId(1)
                     ->setUserCreditsDate($startDate1)
                     ->setUserCreditsDateEnd($endDate1)
                     ->setUserCreditsValue(10);
        $manager->persist($userCredits1);

        // Full credit 2
        $userCredits2 = new UserCredits();
        $startDate2 = new \DateTime("-5 days");
        $endDate2   = new \DateTime("+5 days");
        $userCredits2->setUserId(1)
                     ->setUserCreditsDate($startDate2)
                     ->setUserCreditsDateEnd($endDate2)
                     ->setUserCreditsValue(7);
        $manager->persist($userCredits2);

        // Expired credit 1
        $userCredits3 = new UserCredits();
        $startDate3 = new \DateTime("-23 days");
        $endDate3   = new \DateTime("-13 days");
        $userCredits3->setUserId(1)
                     ->setUserCreditsDate($startDate3)
                     ->setUserCreditsDateEnd($endDate3)
                     ->setUserCreditsValue(5);
        $manager->persist($userCredits3);

        // Used credit 1
        $userCredits4 = new UserCredits();
        $startDate4 = new \DateTime("-5 days");
        $endDate4   = new \DateTime("-5 days");
        $userCredits4->setUserId(1)
                     ->setUserCreditsDate($startDate4)
                     ->setUserCreditsDateEnd($endDate4)
                     ->setUserCreditsValue(-2);
        $manager->persist($userCredits4);

        // Used credit 2
        $userCredits5 = new UserCredits();
        $startDate5 = new \DateTime("-3 days");
        $endDate5   = new \DateTime("-3 days");
        $userCredits5->setUserId(1)
                     ->setUserCreditsDate($startDate5)
                     ->setUserCreditsDateEnd($endDate5)
                     ->setUserCreditsValue(-1);
        $manager->persist($userCredits5);

        // User 2 credit
        $userCredits6 = new UserCredits();
        $startDate6 = new \DateTime("-3 days");
        $endDate6   = new \DateTime("+3 days");
        $userCredits6->setUserId(2)
                     ->setUserCreditsDate($startDate6)
                     ->setUserCreditsDateEnd($endDate6)
                     ->setUserCreditsValue(25);
        $manager->persist($userCredits6);

        $manager->flush();
    }    
}
