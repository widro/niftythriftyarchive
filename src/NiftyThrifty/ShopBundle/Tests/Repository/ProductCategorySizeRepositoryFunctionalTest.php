<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductCategorySizeData;

class ProductCategorySizeRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * The navigation finder should return the looks in alphabetical order
     *
     * @group Repository
     * @group ProductCategorySize
     * @covers ProductCategorySizeRepository::findByCategoryId
     */
    public function testFindByProductCategoryId()
    {
        $this->addFixture(new ProductCategorySizeData);
        $this->executeFixtures();

        $productCategorySizes = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
            ->findByCategoryId(2);

        $this->assertCount(3, $productCategorySizes);
        $this->assertEquals($productCategorySizes[0]->getProductCategorySizeId(), 6);
        $this->assertEquals($productCategorySizes[1]->getProductCategorySizeId(), 3);
        $this->assertEquals($productCategorySizes[2]->getProductCategorySizeId(), 9);
    }

    public function testFindByProductCategoryIdEmpty()
    {
        $this->addFixture(new ProductCategorySizeData);
        $this->executeFixtures();
        $productCategorySizes = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
            ->findByCategoryId(99999);
        $this->assertCount(0, $productCategorySizes);
    }
}
