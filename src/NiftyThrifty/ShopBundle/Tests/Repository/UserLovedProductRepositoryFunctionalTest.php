<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;

class UserLovedProductRepositoryFunctionalTest extends NiftyBaseTestCase
{
    public function testFindByUserAndProductFound()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(1,2);

        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getUserId(), 1);
        $this->assertEquals($loved->getProductId(), 2);
    }
    
    /**
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function testFindByUserAndProductNotFound()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        
        $loved = $this->em
                      ->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                      ->findByUserAndProduct(10,10);
    }
}