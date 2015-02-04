<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use NiftyThrifty\ShopBundle\Entity\ProductCategory;

class ProductCategoryData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $productCategory1 = new ProductCategory();
        $productCategory1->setProductCategoryName('Jumpers');
        $productCategory1->setInNavigation('yes');
        $productCategory1->setNavigationOrder(0);
        $this->addReference('product-category-1', $productCategory1);

        $productCategory2 = new ProductCategory();
        $productCategory2->setProductCategoryName('Dresses');
        $productCategory2->setInNavigation('yes');
        $productCategory2->setNavigationOrder(0);
        $this->addReference('product-category-2', $productCategory2);

        $productCategory3 = new ProductCategory();
        $productCategory3->setProductCategoryName('Rompers');
        $productCategory3->setInNavigation('yes');
        $productCategory3->setNavigationOrder(0);
        $this->addReference('product-category-3', $productCategory3);

        $productCategory4 = new ProductCategory();
        $productCategory4->setProductCategoryName('Shoes');
        $productCategory4->setInNavigation('yes');
        $productCategory4->setNavigationOrder(0);
        $this->addReference('product-category-4', $productCategory4);

        $productCategory5 = new ProductCategory();
        $productCategory5->setProductCategoryName('Tops');
        $productCategory5->setInNavigation('no');
        $productCategory5->setNavigationOrder(0);

        $manager->persist($productCategory1);
        $manager->persist($productCategory2);
        $manager->persist($productCategory3);
        $manager->persist($productCategory4);
        $manager->persist($productCategory5);
        $manager->flush();
    }
}
