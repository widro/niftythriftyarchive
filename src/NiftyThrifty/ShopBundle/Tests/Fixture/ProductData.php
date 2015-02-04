<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\Product;

class ProductData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategorySizeData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\DesignerData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\CollectionData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagData',
                     );
    }

    /**
     * Not null fields are loaded first.  Null fields are not added by default except look
     */
    public function load(ObjectManager $manager)
    {
        // Item 1, reserved in cart 2, collection 1, no designer
        $product1 = new Product();
        $product1->setProductName('Product One');
        $product1->setProductDescription('Description product one');
        $product1->setProductCategorySize($this->getReference('product-category-size-1'));
        $product1->setProductOverallCondition('good');
        $product1->setProductPrice(10);
        $product1->setProductDetailedConditionValue(4);
        $product1->setProductDetailedConditionDescription('Good condition');
        $product1->setProductFabric('Cotton');
        $product1->setProductMeasurements('measure');
        $product1->setProductAvailability(Product::RESERVED);
        $product1->setProductHeavy('no');
        $product1->setCollection($this->getReference('collection-1'));
        $product1->setProductTaxes(8.875);
        $product1->setProductTaxesActive('yes');
        $product1->setProductCode('UT1');
        $this->addReference('product-1', $product1);
        $manager->persist($product1);

        // Item 2, reserved in cart 2, collection 1, designer 1
        $product2 = new Product();
        $product2->setProductName('Product Two');
        $product2->setProductDescription('Description product two');
        $product2->setProductCategorySize($this->getReference('product-category-size-2'));
        $product2->addProductTag($this->getReference('tag-look-classic'));
        $product2->addProductTag($this->getReference('tag-look-boho'));
        $product2->setProductOverallCondition('good');
        $product2->setProductPrice(15);
        $product2->setProductDetailedConditionValue(4);
        $product2->setProductDetailedConditionDescription('Good condition');
        $product2->setProductFabric('Cotton');
        $product2->setProductMeasurements('measure');
        $product2->setProductAvailability(Product::RESERVED);
        $product2->setProductHeavy('no');
        $product2->setCollection($this->getReference('collection-2'));
        $product2->setDesigner($this->getReference('designer-1'));
        $product2->setProductTaxes(8.875);
        $product2->setProductTaxesActive('yes');
        $product2->setProductCode('UT2');
        $this->addReference('product-2', $product2);
        $manager->persist($product2);

        // Item 3, reserved in cart 2, collection 1
        $product3 = new Product();
        $product3->setProductName('Product Three');
        $product3->setProductDescription('Description product three');
        $product3->setProductCategorySize($this->getReference('product-category-size-1'));
        $product3->setProductOverallCondition('good');
        $product3->setProductPrice(12);
        $product3->setProductDetailedConditionValue(4);
        $product3->setProductDetailedConditionDescription('Good condition');
        $product3->setProductFabric('Cotton');
        $product3->setProductMeasurements('measure');
        $product3->setProductAvailability(Product::RESERVED);
        $product3->setProductHeavy('no');
        $product3->setCollection($this->getReference('collection-1'));
        $product3->setDesigner($this->getReference('designer-1'));
        $product3->setProductTaxes(8.875);
        $product3->setProductTaxesActive('yes');
        $product3->setProductCode('UT3');
        $this->addReference('product-3', $product3);
        $manager->persist($product3);

        // ITEM 4, Expired from cart 1.
        $product4 = new Product();
        $product4->setProductName('Product Four');
        $product4->setProductDescription('Description product four');
        $product4->setProductCategorySize($this->getReference('product-category-size-3'));
        $product4->addProductTag($this->getReference('tag-look-classic'));
        $product4->setProductOverallCondition('good');
        $product4->setProductPrice(17);
        $product4->setProductOldPrice(25);
        $product4->setProductDetailedConditionValue(4);
        $product4->setProductDetailedConditionDescription('Good condition');
        $product4->setProductFabric('Cotton');
        $product4->setProductMeasurements('measure');
        $product4->setProductAvailability(Product::RESERVED);
        $product4->setProductHeavy('no');
        $product4->setCollection($this->getReference('collection-3'));
        $product4->setDesigner($this->getReference('designer-1'));
        $product4->setProductTaxes(8.875);
        $product4->setProductTaxesActive('yes');
        $product4->setProductCode('UT4');
        $this->addReference('product-4', $product4);
        $manager->persist($product4);

        // Item 5, Sold item
        $product5 = new Product();
        $product5->setProductName('Product Five');
        $product5->setProductDescription('Description product five');
        $product5->setProductCategorySize($this->getReference('product-category-size-1'));
        $product5->addProductTag($this->getReference('tag-look-classic'));
        $product5->setProductOverallCondition('good');
        $product5->setProductPrice(20);
        $product5->setProductDetailedConditionValue(4);
        $product5->setProductDetailedConditionDescription('Good condition');
        $product5->setProductFabric('Cotton');
        $product5->setProductMeasurements('measure');
        $product5->setProductAvailability(Product::SOLD);
        $product5->setProductHeavy('no');
        $product5->setCollection($this->getReference('collection-1'));
        $product5->setDesigner($this->getReference('designer-1'));
        $product5->setProductTaxes(8.875);
        $product5->setProductTaxesActive('yes');
        $product5->setProductCode('UT5');
        $this->addReference('product-5', $product5);
        $manager->persist($product5);

        // Item 6, Deleted from the ongoing basket.
        $product6 = new Product();
        $product6->setProductName('Product Six');
        $product6->setProductDescription('Description product six');
        $product6->setProductCategorySize($this->getReference('product-category-size-3'));
        $product6->setProductOverallCondition('good');
        $product6->setProductPrice(12);
        $product6->setProductOldPrice(20);
        $product6->setProductDetailedConditionValue(4);
        $product6->setProductDetailedConditionDescription('Good condition');
        $product6->setProductFabric('Cotton');
        $product6->setProductMeasurements('measure');
        $product6->setProductAvailability(Product::SALE);
        $product6->setProductHeavy('no');
        $product6->setCollection($this->getReference('collection-1'));
        $product6->setProductTaxes(8.875);
        $product6->setProductTaxesActive('yes');
        $product6->setProductCode('UT6');
        $this->addReference('product-6', $product6);
        $manager->persist($product6);

        // Item 7, in Purchased basket 1
        $product7 = new Product();
        $product7->setProductName('Product Seven');
        $product7->setProductDescription('Description product seven');
        $product7->setProductCategorySize($this->getReference('product-category-size-1'));
        $product7->setProductOverallCondition('good');
        $product7->setProductPrice(35);
        $product7->setProductDetailedConditionValue(4);
        $product7->setProductDetailedConditionDescription('Good condition');
        $product7->setProductFabric('Cotton');
        $product7->setProductMeasurements('measure');
        $product7->setProductAvailability(Product::SOLD);
        $product7->setProductHeavy('no');
        $product7->setCollection($this->getReference('collection-1'));
        $product7->setDesigner($this->getReference('designer-1'));
        $product7->setProductTaxes(8.875);
        $product7->setProductTaxesActive('yes');
        $product7->setProductCode('UT7');
        $this->addReference('product-7', $product7);
        $manager->persist($product7);

        // Item 8.  Not in a basket.
        $product8 = new Product();
        $product8->setProductName('Product Eight');
        $product8->setProductDescription('Description product eight');
        $product8->setProductCategorySize($this->getReference('product-category-size-1'));
        $product8->addProductTag($this->getReference('tag-look-rocker'));
        $product8->setDesigner($this->getReference('designer-2'));
        $product8->setProductOverallCondition('good');
        $product8->setProductPrice(46);
        $product8->setProductDetailedConditionValue(4);
        $product8->setProductDetailedConditionDescription('Good condition');
        $product8->setProductFabric('Cotton');
        $product8->setProductMeasurements('measure');
        $product8->setProductAvailability(Product::SALE);
        $product8->setProductHeavy('no');
        $product8->setCollection($this->getReference('collection-1'));
        $product8->setProductTaxes(8.875);
        $product8->setProductTaxesActive('yes');
        $product8->setProductCode('UT8');
        $this->addReference('product-8', $product8);
        $manager->persist($product8);

        // Item 9.  Not in a basket.
        $product9 = new Product();
        $product9->setProductName('Product Nine');
        $product9->setProductDescription('Description product nine');
        $product9->setProductCategorySize($this->getReference('product-category-size-5'));
        $product9->addProductTag($this->getReference('tag-look-rocker'));
        $product9->setDesigner($this->getReference('designer-4'));
        $product9->setProductOverallCondition('good');
        $product9->setProductPrice(25);
        $product9->setProductDetailedConditionValue(4);
        $product9->setProductDetailedConditionDescription('Good condition');
        $product9->setProductFabric('Cotton');
        $product9->setProductMeasurements('measure');
        $product9->setProductAvailability(Product::SALE);
        $product9->setProductHeavy('no');
        $product9->setCollection($this->getReference('collection-3'));
        $product9->setProductTaxes(8.875);
        $product9->setProductTaxesActive('yes');
        $product9->setProductCode('UT9');
        $this->addReference('product-9', $product9);
        $manager->persist($product9);


        /** Nullable product fields for copy and paste where appropriate.
        $product->setProductOldPrice();
        $product->setProductDiscount();
        $product->setProductVisual1();
        $product->setProductVisual1Large();
        $product->setProductVisual2();
        $product->setProductVisual2Large();
        $product->setProductVisual3();
        $product->setProductVisual3Large();
        $product->setDesigner('designer-1');
        $product->setProductHashtag();
        $product->setProductInstagramMediaIdNifty();
        $product->setProductInstagramMediaIdCustomer();
        $product->setProductTagsize();
        **/

        $manager->flush();
    }
}
