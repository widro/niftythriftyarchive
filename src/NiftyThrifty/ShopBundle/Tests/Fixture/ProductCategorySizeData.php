<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\ProductCategorySize;

class ProductCategorySizeData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Sizes require their categories to be loaded.
     */
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategoryData');
    }

    public function load(ObjectManager $manager)
    {
        $productCategorySize1 = new ProductCategorySize();
        $productCategorySize1->setProductCategorySizeName('Jumper - S');
        $productCategorySize1->setProductCategorySizeValue('S');
        $productCategorySize1->setProductCategorySizeOrder(1);
        $productCategorySize1->setProductCategory($this->getReference('product-category-1'));
        $this->addReference('product-category-size-1', $productCategorySize1);
        $manager->persist($productCategorySize1);

        $productCategorySize2 = new ProductCategorySize();
        $productCategorySize2->setProductCategorySizeName('Jumper - M');
        $productCategorySize2->setProductCategorySizeValue('M');
        $productCategorySize2->setProductCategorySizeOrder(2);
        $productCategorySize2->setProductCategory($this->getReference('product-category-1'));
        $this->addReference('product-category-size-2', $productCategorySize2);
        $manager->persist($productCategorySize2);

        $productCategorySize3 = new ProductCategorySize();
        $productCategorySize3->setProductCategorySizeName('Dresses - 8');
        $productCategorySize3->setProductCategorySizeValue('8');
        $productCategorySize3->setProductCategorySizeOrder(2);
        $productCategorySize3->setProductCategory($this->getReference('product-category-2'));
        $this->addReference('product-category-size-3', $productCategorySize3);
        $manager->persist($productCategorySize3);

        $productCategorySize4 = new ProductCategorySize();
        $productCategorySize4->setProductCategorySizeName('Rompers - M');
        $productCategorySize4->setProductCategorySizeValue('M');
        $productCategorySize4->setProductCategorySizeOrder(1);
        $productCategorySize4->setProductCategory($this->getReference('product-category-3'));
        $this->addReference('product-category-size-4', $productCategorySize4);
        $manager->persist($productCategorySize4);

        $productCategorySize5 = new ProductCategorySize();
        $productCategorySize5->setProductCategorySizeName('Rompers - L');
        $productCategorySize5->setProductCategorySizeValue('L');
        $productCategorySize5->setProductCategorySizeOrder(2);
        $productCategorySize5->setProductCategory($this->getReference('product-category-3'));
        $this->addReference('product-category-size-5', $productCategorySize5);
        $manager->persist($productCategorySize5);

        $productCategorySize6 = new ProductCategorySize();
        $productCategorySize6->setProductCategorySizeName('Dresses - 6');
        $productCategorySize6->setProductCategorySizeValue('6');
        $productCategorySize6->setProductCategorySizeOrder(1);
        $productCategorySize6->setProductCategory($this->getReference('product-category-2'));
        $this->addReference('product-category-size-6', $productCategorySize6);
        $manager->persist($productCategorySize6);

        $productCategorySize7 = new ProductCategorySize();
        $productCategorySize7->setProductCategorySizeName('Shoes - 8');
        $productCategorySize7->setProductCategorySizeValue('8');
        $productCategorySize7->setProductCategorySizeOrder(1);
        $productCategorySize7->setProductCategory($this->getReference('product-category-4'));
        $this->addReference('product-category-size-7', $productCategorySize7);
        $manager->persist($productCategorySize7);

        $productCategorySize8 = new ProductCategorySize();
        $productCategorySize8->setProductCategorySizeName('Shoes - 9');
        $productCategorySize8->setProductCategorySizeValue('9');
        $productCategorySize8->setProductCategorySizeOrder(2);
        $productCategorySize8->setProductCategory($this->getReference('product-category-4'));
        $this->addReference('product-category-size-8', $productCategorySize8);
        $manager->persist($productCategorySize8);

        $productCategorySize9 = new ProductCategorySize();
        $productCategorySize9->setProductCategorySizeName('Dresses - 10');
        $productCategorySize9->setProductCategorySizeValue('10');
        $productCategorySize9->setProductCategorySizeOrder(3);
        $productCategorySize9->setProductCategory($this->getReference('product-category-2'));
        $this->addReference('product-category-size-9', $productCategorySize9);
        $manager->persist($productCategorySize9);

        $manager->flush();
    }
}
