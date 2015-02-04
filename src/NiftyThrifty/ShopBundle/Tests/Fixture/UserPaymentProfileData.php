<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\UserPaymentProfile;

class UserPaymentProfileData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\UserData');
    }

    public function load(ObjectManager $manager)
    {
        // User Card 1
        $userPaymentProfile1 = new UserPaymentProfile();
        $expireDate1         = new \DateTime("+1 year");
        $userPaymentProfile1->setUser($this->getReference('user-1'))
                            ->setCardDigits('1111')
                            ->setAuthorizeNetProfileId(1)
                            ->setExpirationDate($expireDate1->format('Y-m'));
        $manager->persist($userPaymentProfile1);

        // User Card 2
        $userPaymentProfile2 = new UserPaymentProfile();
        $expireDate2         = new \DateTime("+2 years");
        $userPaymentProfile2->setUser($this->getReference('user-1'))
                            ->setCardDigits('2222')
                            ->setAuthorizeNetProfileId(2)
                            ->setExpirationDate($expireDate2->format('Y-m'));
        $manager->persist($userPaymentProfile2);

        // User Card 3 Expired
        $userPaymentProfile3 = new UserPaymentProfile();
        $expireDate3         = new \DateTime("-1 year");
        $userPaymentProfile3->setUser($this->getReference('user-1'))
                            ->setCardDigits('3333')
                            ->setAuthorizeNetProfileId(3)
                            ->setExpirationDate($expireDate3->format('Y-m'));
        $manager->persist($userPaymentProfile3);

        // Other User Card
        $userPaymentProfile4 = new UserPaymentProfile();
        $expireDate4         = new \DateTime("+1 year");
        $userPaymentProfile4->setUser($this->getReference('user-2'))
                            ->setCardDigits('4444')
                            ->setAuthorizeNetProfileId(4)
                            ->setExpirationDate($expireDate4->format('Y-m'));
        $manager->persist($userPaymentProfile4);

        $manager->flush();
    }    
}
