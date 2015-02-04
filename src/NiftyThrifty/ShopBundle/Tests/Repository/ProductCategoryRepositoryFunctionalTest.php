<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategoryData;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;

class ProductCategoryRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * The navigation finder should return the looks in alphabetical order
     *
     * @group Repository
     * @group ProductCategory
     * @covers ProductCateogryRepository::findNavigation
     */
    public function testFindNavigation()
    {
        $this->addFixture(new ProductCategoryData);
        $this->executeFixtures();

        $productCategories = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductCategory')
            ->findNavigation();

        $this->assertCount(4, $productCategories);
        $this->assertEquals($productCategories[0]->getProductCategoryId(), 2);
        $this->assertEquals($productCategories[1]->getProductCategoryId(), 1);
        $this->assertEquals($productCategories[2]->getProductCategoryId(), 3);
        $this->assertEquals($productCategories[3]->getProductCategoryId(), 4);
    }

    public function testFindCategoriesInCollection()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $productCategories = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductCategory')
            ->findCategoriesInCollection(1);

        $this->assertCount(2, $productCategories);
        $this->assertEquals($productCategories[0]['productCategoryId'],     2);
        $this->assertEquals($productCategories[0]['productCategoryName'],   'Dresses');
        $this->assertEquals($productCategories[1]['productCategoryId'],     1);
        $this->assertEquals($productCategories[1]['productCategoryName'],   'Jumpers');
    }

    public function testFindCategoriesWomen()
    {
        $this->addFixture(new ProductCategoryData);
        $this->executeFixtures();

        $productCategories = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductCategory')
            ->findCategoriesWomen();

        $this->assertCount(5, $productCategories);
        $this->assertEquals($productCategories[0]['productCategoryId'],     2);
        $this->assertEquals($productCategories[0]['productCategoryName'],   'Dresses');
        $this->assertEquals($productCategories[1]['productCategoryId'],     1);
        $this->assertEquals($productCategories[1]['productCategoryName'],   'Jumpers');
        $this->assertEquals($productCategories[2]['productCategoryId'],     3);
        $this->assertEquals($productCategories[2]['productCategoryName'],   'Rompers');
        $this->assertEquals($productCategories[3]['productCategoryId'],     4);
        $this->assertEquals($productCategories[3]['productCategoryName'],   'Shoes');
        $this->assertEquals($productCategories[4]['productCategoryId'],     5);
        $this->assertEquals($productCategories[4]['productCategoryName'],   'Tops');
    }
}
