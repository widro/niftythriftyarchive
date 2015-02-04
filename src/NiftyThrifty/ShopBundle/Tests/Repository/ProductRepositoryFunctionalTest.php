<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;

class ProductRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * Get all the items in a category, sort by default.
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryDefaultSort()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1);

        $this->assertCount(4, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(1, $products[1]->getProductId());
        $this->assertEquals(3, $products[2]->getProductId());
        $this->assertEquals(2, $products[3]->getProductId());
    }

    /**
     * Get all the items in a category, sort by something else
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryOrderByOtherAsc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('orderBy' => 'productId'));

        $this->assertCount(4, $products);
        $this->assertEquals(1, $products[0]->getProductId());
        $this->assertEquals(2, $products[1]->getProductId());
        $this->assertEquals(3, $products[2]->getProductId());
        $this->assertEquals(8, $products[3]->getProductId());
    }

    /**
     * Get all the items in a category, sort by something else descending
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryOrderByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('orderBy'       => 'productId', 
                                                    'orderDirection'=> 'DESC'));

        $this->assertCount(4, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(2, $products[2]->getProductId());
        $this->assertEquals(1, $products[3]->getProductId());
    }

    /**
     * Get all the items in a category, default sort page 1
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryPageOneDefaultSort()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('pageSize'      => 3, 
                                                    'pageNumber'    => 1));

        $this->assertCount(3, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(1, $products[1]->getProductId());
        $this->assertEquals(3, $products[2]->getProductId());
    }

    /**
     * Get all the items in a category, default sort page 2
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryPageTwoDefaultSort()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('pageSize'      => 3, 
                                                    'pageNumber'    => 2));

        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * Get all the items in a category, sort by something else page 1
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryPageOneSortByOther()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('orderBy'   => 'productId', 
                                                    'pageSize'  => 2, 
                                                    'pageNumber'=> 1));

        $this->assertCount(2, $products);
        $this->assertEquals(1, $products[0]->getProductId());
        $this->assertEquals(2, $products[1]->getProductId());
    }

    /**
     * Get all the items in a category, sort by something else page 2
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryPageTwoSortByOther()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('orderBy'   => 'productId', 
                                                    'pageSize'  => 2, 
                                                    'pageNumber'=> 2));

        $this->assertCount(2, $products);
        $this->assertEquals(3, $products[0]->getProductId());
        $this->assertEquals(8, $products[1]->getProductId());
    }

    /**
     * Get all the items in a category, sort by something else
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryPageOneSortByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('orderBy'       => 'productId', 
                                                    'orderDirection'=> 'desc', 
                                                    'pageSize'      => 2, 
                                                    'pageNumber'    => 1));
        $this->assertCount(2, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
    }

    /**
     * Get all the items in a category, sort by something else
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByCategory
     */
    public function testFindByCategoryPageTwoSortByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByCategory(1, array('orderBy'       => 'productId', 
                                                    'orderDirection'=> 'desc', 
                                                    'pageSize'      => 2, 
                                                    'pageNumber'    => 2));
        $this->assertCount(2, $products);
        $this->assertEquals(2, $products[0]->getProductId());
        $this->assertEquals(1, $products[1]->getProductId());
    }
    
    /**
     * Get category count with values
     */
    public function testFindCountByCategory()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByCategory(1);

        $this->assertEquals(4, $products);
    }

    /**
     * Get category count with no values
     */
    public function testFindCountByCategoryZero()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByCategory(9999);

        $this->assertEquals(0, $products);
    }

    /**
     * Find all products based on a designer
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository::findByDesigner
     */
    public function testFindByDesignerDefault()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1);
        $this->assertCount(3, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(2, $products[2]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerOrderByOtherAsc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('orderBy'       => 'productId',
                                                    'orderDirection'=> 'asc'));

        $this->assertCount(3, $products);
        $this->assertEquals(2, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(4, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerOrderByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('orderBy'       => 'productId',
                                                    'orderDirection'=> 'desc'));
        $this->assertCount(3, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(2, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerDefaultOrderPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('pageSize'  => 2,
                                                    'pageNumber'=> 1));
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerDefaultOrderPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('pageSize'  => 2,
                                                    'pageNumber'=> 2));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerOrderByOtherPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('pageSize'  => 2,
                                                    'pageNumber'=> 1,
                                                    'orderBy'   => 'productId'));
        $this->assertCount(2, $products);
        $this->assertEquals(2, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerOrderByOtherPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('pageSize'  => 2,
                                                    'pageNumber'=> 2,
                                                    'orderBy'   => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerOrderByOtherDescPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('pageSize'      => 2,
                                                    'pageNumber'    => 1,
                                                    'orderDirection'=> 'desc',
                                                    'orderBy'       => 'productId'));
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByDesignerOrderByOtherDescPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByDesigner(1, array('pageSize'      => 2,
                                                    'pageNumber'    => 2,
                                                    'orderDirection'=> 'desc',
                                                    'orderBy'       => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * Get category designer count with values
     */
    public function testFindCountByDesigner()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByDesigner(1);
        $this->assertEquals(3, $products);
    }

    /**
     * Get category designer count with no values
     */
    public function testFindCountByDesignerZero()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByDesigner(9999);
        $this->assertEquals(0, $products);
    }

    /**
     * Find all items by their size tests.
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository:findBySize
     */
    public function testFindBySizeDefault()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1);
        $this->assertCount(3, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(1, $products[1]->getProductId());
        $this->assertEquals(3, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeOrderByOtherAsc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('orderBy'       => 'productId',
                                                'orderDirection'=> 'asc'));
        $this->assertCount(3, $products);
        $this->assertEquals(1, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(8, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeOrderByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(3, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(1, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeDefaultOrderPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('pageSize'  => 2,
                                                'pageNumber'=> 1));
        $this->assertCount(2, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(1, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeDefaultOrderPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('pageSize'  => 2,
                                                'pageNumber'=> 2));
        $this->assertCount(1, $products);
        $this->assertEquals(3, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeOrderByOtherAscPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('pageSize'  => 2,
                                                'pageNumber'=> 1,
                                                'orderBy'   => 'productId'));
        $this->assertCount(2, $products);
        $this->assertEquals(1, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeOrderByOtherAscPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('pageSize'  => 2,
                                                'pageNumber'=> 2,
                                                'orderBy'   => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(8, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeOrderByOtherDescPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('pageSize'      => 2,
                                                'pageNumber'    => 1,
                                                'orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(2, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindBySizeOrderByOtherDescPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findBySize(1, array('pageSize'      => 2,
                                                'pageNumber'    => 2,
                                                'orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(1, $products);
        $this->assertEquals(1, $products[0]->getProductId());
    }

    /**
     * Get category designer count with values
     */
    public function testFindCountBySize()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountBySize(1);
        $this->assertEquals(3, $products);
    }

    /**
     * Get category designer count with no values
     */
    public function testFindCountBySizeNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountBySize(9999);
        $this->assertEquals(0, $products);
    }

    /**
     * Test the findByLook function, all defaults.
     *
     * @group Repository
     * @group Product
     * @covers Product::findByLook
     */
    public function testFindByLookDefault()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8);
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(2, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookOrderByOtherAsc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('orderBy'       => 'productId',
                                                'orderDirection'=> 'asc'));
        $this->assertCount(2, $products);
        $this->assertEquals(2, $products[0]->getProductId());
        $this->assertEquals(4, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookOrderByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(2, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookDefaultOrderPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('pageSize'  => 1,
                                                'pageNumber'=> 1));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookDefaultOrderPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('pageSize'  => 1,
                                                'pageNumber'=> 2));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookOrderByOtherAscPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('pageSize'  => 1,
                                                'pageNumber'=> 1,
                                                'orderBy'   => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookOrderByOtherAscPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('pageSize'  => 1,
                                                'pageNumber'=> 2,
                                                'orderBy'   => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookOrderByOtherDescPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('pageSize'      => 1,
                                                'pageNumber'    => 1,
                                                'orderBy'       => 'productId',
                                                'orderDirection'=>'desc'));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLookOrderByOtherDescPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLook(8, array('pageSize'      => 1,
                                                'pageNumber'    => 2,
                                                'orderBy'       => 'productId',
                                                'orderDirection'=>'desc'));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * Get category look/tag count with values
     */
    public function testFindCountByLook()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByLook(8);
        $this->assertEquals(2, $products);
    }

    /**
     * Get category look/tag count with no values
     */
    public function testFindCountByLookNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByLook(9999);
        $this->assertEquals(0, $products);
    }
    
    /**
     * Find by a collection.  Collections include sold items so we can show that they've
     * been sold.
     *
     * @group Repository
     * @group Product
     * @covers Product::findByCollection
     */
    public function testFindByCollectionDefault()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1);
        $this->assertCount(6, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(5, $products[1]->getProductId());
        $this->assertEquals(1, $products[2]->getProductId());
        $this->assertEquals(7, $products[3]->getProductId());
        $this->assertEquals(6, $products[4]->getProductId());
        $this->assertEquals(3, $products[5]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionSortByOtherAsc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('orderBy'          => 'productId',
                                                     'orderDirection'   => 'asc'));
        $this->assertCount(6, $products);
        $this->assertEquals(1, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(5, $products[2]->getProductId());
        $this->assertEquals(6, $products[3]->getProductId());
        $this->assertEquals(7, $products[4]->getProductId());
        $this->assertEquals(8, $products[5]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionSortByOtherDesc()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('orderBy'          => 'productId',
                                                     'orderDirection'   => 'desc'));
        $this->assertCount(6, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(7, $products[1]->getProductId());
        $this->assertEquals(6, $products[2]->getProductId());
        $this->assertEquals(5, $products[3]->getProductId());
        $this->assertEquals(3, $products[4]->getProductId());
        $this->assertEquals(1, $products[5]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionDefaultSortPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('pageSize'     => 3,
                                                     'pageNumber'   => 1));
        $this->assertCount(3, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(5, $products[1]->getProductId());
        $this->assertEquals(1, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionDefaultSortPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('pageSize'     => 3,
                                                     'pageNumber'   => 2));
        $this->assertCount(3, $products);
        $this->assertEquals(7, $products[0]->getProductId());
        $this->assertEquals(6, $products[1]->getProductId());
        $this->assertEquals(3, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionSortByOtherAscPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('pageSize'     => 3,
                                                     'pageNumber'   => 1,
                                                     'orderBy'      => 'productId'));
        $this->assertCount(3, $products);
        $this->assertEquals(1, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(5, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionSortByOtherAscPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('pageSize'     => 3,
                                                     'pageNumber'   => 2,
                                                     'orderBy'      => 'productId'));
        $this->assertCount(3, $products);
        $this->assertEquals(6, $products[0]->getProductId());
        $this->assertEquals(7, $products[1]->getProductId());
        $this->assertEquals(8, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionSortByOtherDescPageOne()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('pageSize'     => 3,
                                                     'pageNumber'   => 1,
                                                     'orderBy'      => 'productId',
                                                     'orderDirection'=> 'desc'));
        $this->assertCount(3, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(7, $products[1]->getProductId());
        $this->assertEquals(6, $products[2]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByCollectionSortByOtherDescPageTwo()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection(1, array('pageSize'     => 3,
                                                     'pageNumber'   => 2,
                                                     'orderBy'      => 'productId',
                                                     'orderDirection'=> 'desc'));
        $this->assertCount(3, $products);
        $this->assertEquals(5, $products[0]->getProductId());
        $this->assertEquals(3, $products[1]->getProductId());
        $this->assertEquals(1, $products[2]->getProductId());
    }

    /**
     * Get collection count with values
     */
    public function testFindCountByCollection()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByCollection(1);
        $this->assertEquals(6, $products);
    }

    /**
     * Get collection count with no values
     */
    public function testFindCountByCollectionNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByCollection(9999);
        $this->assertEquals(0, $products);
    }

    /**
     * Find all items by loved status.
     *
     * @group Repository
     * @group Product
     * @covers ProductRepository:findByLove
     */
    public function testFindByLoveDefault()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1);
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(2, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveOrderByOtherAsc()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('orderBy'       => 'productId',
                                                'orderDirection'=> 'asc'));
        $this->assertCount(2, $products);
        $this->assertEquals(2, $products[0]->getProductId());
        $this->assertEquals(4, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveOrderByOtherDesc()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(2, $products[1]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveDefaultOrderPageOne()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('pageSize'  => 1,
                                                'pageNumber'=> 1));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveDefaultOrderPageTwo()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('pageSize'  => 1,
                                                'pageNumber'=> 2));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveOrderByOtherAscPageOne()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('pageSize'  => 1,
                                                'pageNumber'=> 1,
                                                'orderBy'   => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveOrderByOtherAscPageTwo()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('pageSize'  => 1,
                                                'pageNumber'=> 2,
                                                'orderBy'   => 'productId'));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveOrderByOtherDescPageOne()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('pageSize'      => 1,
                                                'pageNumber'    => 1,
                                                'orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }

    /**
     * @group Repository
     * @group Product
     */
    public function testFindByLoveOrderByOtherDescPageTwo()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findByLove(1, array('pageSize'      => 1,
                                                'pageNumber'    => 2,
                                                'orderBy'       => 'productId',
                                                'orderDirection'=> 'desc'));
        $this->assertCount(1, $products);
        $this->assertEquals(2, $products[0]->getProductId());
    }

    /**
     * Get love count
     */
    public function testFindCountByLove()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByLove(1);
        $this->assertEquals(2, $products);
    }

    /**
     * Get love count with no results
     */
    public function testFindCountByLoveNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByLove(9999);
        $this->assertEquals(0, $products);
    }
}
