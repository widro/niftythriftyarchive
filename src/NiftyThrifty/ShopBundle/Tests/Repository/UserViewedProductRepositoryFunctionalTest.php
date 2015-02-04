<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserViewedProductData;

class UserViewedProductRepositoryFunctionalTest extends NiftyBaseTestCase
{
    public function testFindByUserAndProductFound()
    {
        $this->addFixture(new UserViewedProductData);
        $this->executeFixtures();
        
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserViewedProduct')
                      ->findByUserAndProduct(1,2);

        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserViewedProduct', $loved);
        $this->assertEquals($loved->getUserId(), 1);
        $this->assertEquals($loved->getProductId(), 2);
    }
    
    /**
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function testFindByUserAndProductNotFound()
    {
        $this->addFixture(new UserViewedProductData);
        $this->executeFixtures();
        
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserViewedProduct')
                      ->findByUserAndProduct(10,10);
    }
}
