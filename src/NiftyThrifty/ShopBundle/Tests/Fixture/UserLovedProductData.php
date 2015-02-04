<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\UserLovedProduct;

class UserLovedProductData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Sizes require their categories to be loaded.
     */
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\ProductData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\UserData');
    }

    public function load(ObjectManager $manager)
    {
        $nowTime = new \DateTime();

        // Regular loved product
        $lovedProduct1 = new UserLovedProduct();
        $lovedProduct1->setUserId(1)
                      ->setProductId(4)
                      ->setLoveType('basket')
                      ->setIsDeleted(0)
                      ->setDateLoved($nowTime)
                      ->setUser($this->getReference('user-1'))
                      ->setProduct($this->getReference('product-4'));
        $manager->persist($lovedProduct1);
        
        $lovedProduct2 = new UserLovedProduct();
        $lovedProduct2->setUserId(1)
                      ->setProductId(2)
                      ->setLoveType('link')
                      ->setIsDeleted(0)
                      ->setDateLoved($nowTime)
                      ->setUser($this->getReference('user-1'))
                      ->setProduct($this->getReference('product-2'));
        $manager->persist($lovedProduct2);
        
        $lovedProduct3 = new UserLovedProduct();
        $lovedProduct3->setUserId(1)
                      ->setProductId(1)
                      ->setLoveType('basket')
                      ->setIsDeleted(1)
                      ->setDateLoved($nowTime)
                      ->setUser($this->getReference('user-1'))
                      ->setProduct($this->getReference('product-1'));
        $manager->persist($lovedProduct3);
        
        $lovedProduct4 = new UserLovedProduct();
        $lovedProduct4->setUserId(2)
                      ->setProductId(1)
                      ->setLoveType('basket')
                      ->setIsDeleted(0)
                      ->setDateLoved($nowTime)
                      ->setUser($this->getReference('user-2'))
                      ->setProduct($this->getReference('product-1'));
        $manager->persist($lovedProduct4);

        $lovedProduct5 = new UserLovedProduct();
        $lovedProduct5->setUserId(2)
                      ->setProductId(3)
                      ->setLoveType('basket')
                      ->setIsDeleted(0)
                      ->setDateLoved($nowTime)
                      ->setUser($this->getReference('user-2'))
                      ->setProduct($this->getReference('product-3'));
        $manager->persist($lovedProduct5);
        
        $manager->flush();
    }
}
