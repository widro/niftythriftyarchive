<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\UserViewedProduct;

class UserViewedProductData extends AbstractFixture implements DependentFixtureInterface
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
        $earlyTime = new \DateTime();
        $earlyTime->modify("-5 minutes");

        // Regular viewed product
        $viewedProduct1 = new UserViewedProduct();
        $viewedProduct1->setUserId(1)
                      ->setProductId(1)
                      ->setDateViewed($nowTime)
                      ->setUser($this->getReference('user-1'))
                      ->setProduct($this->getReference('product-1'));
        $manager->persist($viewedProduct1);
        
        $viewedProduct2 = new UserViewedProduct();
        $viewedProduct2->setUserId(1)
                      ->setProductId(2)
                      ->setDateViewed($earlyTime)
                      ->setUser($this->getReference('user-1'))
                      ->setProduct($this->getReference('product-2'));
        $manager->persist($viewedProduct2);
        
        $viewedProduct3 = new UserViewedProduct();
        $viewedProduct3->setUserId(2)
                      ->setProductId(1)
                      ->setDateViewed($nowTime)
                      ->setUser($this->getReference('user-2'))
                      ->setProduct($this->getReference('product-1'));
        $manager->persist($viewedProduct3);

        $viewedProduct4 = new UserViewedProduct();
        $viewedProduct4->setUserId(2)
                      ->setProductId(3)
                      ->setDateViewed($nowTime)
                      ->setUser($this->getReference('user-2'))
                      ->setProduct($this->getReference('product-3'));
        $manager->persist($viewedProduct4);
        
        $manager->flush();
    }
}
