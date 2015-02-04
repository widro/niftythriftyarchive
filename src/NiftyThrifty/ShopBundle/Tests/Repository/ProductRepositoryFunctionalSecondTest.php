<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;

class ProductRepositoryFunctionalSecondTest extends NiftyBaseTestCase
{    
    /**
     * Test the function that returns all items over a certain price
     *
     * @covers ProductRepository:findByPriceOver
     */
    public function testFindByPriceOverDefaultSort()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19);
                         
        $this->assertCount(2, $products);
        $this->assertEquals(9, $products[0]->getProductId());
        $this->assertEquals(8, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverSortByOtherAsc()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('orderBy'          => 'productId',
                                                     'orderDirection'   => 'asc'));
                         
        $this->assertCount(2, $products);
        $this->assertEquals(8, $products[0]->getProductId());
        $this->assertEquals(9, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverSortByOtherDesc()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('orderBy'          => 'productId',
                                                     'orderDirection'   => 'desc'));
                         
        $this->assertCount(2, $products);
        $this->assertEquals(9, $products[0]->getProductId());
        $this->assertEquals(8, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverDefaultSortPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('pageSize'  => 1,
                                                     'pageNumber'=> 1));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(9, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverDefaultSortPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('pageSize'  => 1,
                                                     'pageNumber'=> 2));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(8, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverSortByOtherAscPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('pageSize'         => 1,
                                                     'pageNumber'       => 1,
                                                     'orderBy'          => 'productId',
                                                     'orderDirection'   => 'asc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(8, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverSortByOtherAscPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('pageSize'         => 1,
                                                     'pageNumber'       => 2,
                                                     'orderBy'          => 'productId',
                                                     'orderDirection'   => 'asc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(9, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverSortByOtherDescPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('pageSize'         => 1,
                                                     'pageNumber'       => 1,
                                                     'orderBy'          => 'productId',
                                                     'orderDirection'   => 'desc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(9, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOverSortByOtherDescPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOver(19, array('pageSize'         => 1,
                                                     'pageNumber'       => 2,
                                                     'orderBy'          => 'productId',
                                                     'orderDirection'   => 'desc'));
        $this->assertCount(1, $products);
        $this->assertEquals(8, $products[0]->getProductId());
    }    
    
    /**
     * Get price over count with values
     */
    public function testFindCountByPriceOver()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByPriceOver(19);
        $this->assertEquals(2, $products);
    }

    /**
     * Get price over count with no values
     */
    public function testFindCountByPriceOverNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByPriceOver(9999);
        $this->assertEquals(0, $products);
    }

    /**
     * Test the function that returns all items over a certain price
     *
     * @covers ProductRepository:findByPriceOver
     */
    public function testFindByPriceUnderDefaultSort()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19);
                         
        $this->assertCount(2, $products);
        $this->assertEquals(6, $products[0]->getProductId());
        $this->assertEquals(4, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderSortByOtherAsc()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('orderBy'         => 'productId',
                                                      'orderDirection'  => 'asc'));
                         
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(6, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderSortByOtherDesc()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('orderBy'         => 'productId',
                                                      'orderDirection'  => 'desc'));
                         
        $this->assertCount(2, $products);
        $this->assertEquals(6, $products[0]->getProductId());
        $this->assertEquals(4, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderDefaultSortPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('pageSize'    => 1,
                                                      'pageNumber'  => 1));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(6, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderDefaultSortPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('pageSize'    => 1,
                                                      'pageNumber'  => 2));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderSortByOtherAscPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('pageSize'         => 1,
                                                      'pageNumber'       => 1,
                                                      'orderBy'          => 'productId',
                                                      'orderDirection'   => 'asc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderSortByOtherAscPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('pageSize'         => 1,
                                                      'pageNumber'       => 2,
                                                      'orderBy'          => 'productId',
                                                      'orderDirection'   => 'asc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(6, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderSortByOtherDescPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('pageSize'         => 1,
                                                      'pageNumber'       => 1,
                                                      'orderBy'          => 'productId',
                                                      'orderDirection'   => 'desc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(6, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceUnderSortByOtherDescPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceUnder(19, array('pageSize'         => 1,
                                                      'pageNumber'       => 2,
                                                      'orderBy'          => 'productId',
                                                      'orderDirection'   => 'desc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }
    
    /**
     * Get price under count with values
     */
    public function testFindCountByPriceUnder()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(4);
        $product->setProductAvailability('sale');
        $this->em->flush();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByPriceUnder(19);
        $this->assertEquals(2, $products);
    }

    /**
     * Get price under count with no values
     */
    public function testFindCountByPriceUnderNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByPriceUnder(1);
        $this->assertEquals(0, $products);
    }

    /**
     * Test the function that returns all items over a certain price
     *
     * @covers ProductRepository:findByPriceOld
     */
    public function testFindByPriceOldDefaultSort()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld();
                         
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(6, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldSortByOtherAsc()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('orderBy'         => 'productId',
                                                'orderDirection'  => 'asc'));
                         
        $this->assertCount(2, $products);
        $this->assertEquals(4, $products[0]->getProductId());
        $this->assertEquals(6, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldSortByOtherDesc()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('orderBy'         => 'productId',
                                                'orderDirection'  => 'desc'));
                         
        $this->assertCount(2, $products);
        $this->assertEquals(6, $products[0]->getProductId());
        $this->assertEquals(4, $products[1]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldDefaultSortPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('pageSize'    => 1,
                                                'pageNumber'  => 1));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldDefaultSortPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('pageSize'    => 1,
                                                'pageNumber'  => 2));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(6, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldSortByOtherAscPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('pageSize'         => 1,
                                                'pageNumber'       => 1,
                                                'orderBy'          => 'productId',
                                                'orderDirection'   => 'asc'));
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldSortByOtherAscPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('pageSize'         => 1,
                                                'pageNumber'       => 2,
                                                'orderBy'          => 'productId',
                                                'orderDirection'   => 'asc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(6, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldSortByOtherDescPageOne()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('pageSize'         => 1,
                                                  'pageNumber'       => 1,
                                                  'orderBy'          => 'productId',
                                                  'orderDirection'   => 'desc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(6, $products[0]->getProductId());
    }
    
    /**
     * @group Repository
     * @group Product
     */
    public function testFindByPriceOldSortByOtherDescPageTwo()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByPriceOld(array('pageSize'         => 1,
                                                'pageNumber'       => 2,
                                                'orderBy'          => 'productId',
                                                'orderDirection'   => 'desc'));
                         
        $this->assertCount(1, $products);
        $this->assertEquals(4, $products[0]->getProductId());
    }
    
    /**
     * Get price under count with values
     */
    public function testFindCountByPriceOld()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(4);
        $product->setProductAvailability('sale');
        $this->em->flush();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByPriceOld();
        $this->assertEquals(2, $products);
    }
    
    public function testFindByTerms()
    {
        $this->addFixture(new BasketItemData);
        $this->executeFixtures();
        $this->em
             ->getRepository('NiftyThriftyShopBundle:Basket')
             ->find(2)
             ->expireItems($this->em);
        
        $products = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByTerms('Eight');
                         
        $this->assertCount(1, $products);
        $this->assertEquals(8, $products[0]->getProductId());
    }

    /**
     * Get term count with values
     */
    public function testFindCountByTerms()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByTerms('Eight');
        $this->assertEquals(1, $products);
    }

    /**
     * Get term count with no values
     */
    public function testFindCountByTermsNone()
    {
        $this->addFixture(new ProductData);
        $this->executeFixtures();

        $products = $this->em
                          ->getRepository('NiftyThriftyShopBundle:Product')
                          ->findCountByTerms('bluster');
        $this->assertEquals(0, $products);
    }
}
