<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagtypeData;

class ProductTagtypeRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * The navigation finder should return the looks in alphabetical order
     *
     * @group Repository
     * @group ProductTagtype
     * @covers ProductTagtypeRepository::findNavigation
     */
    public function testFindNavigation()
    {
        $this->addFixture(new ProductTagtypeData);
        $this->executeFixtures();

        $productTagtypes = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductTagtype')
            ->findNavigation();

        $this->assertCount(5, $productTagtypes);
        $this->assertEquals($productTagtypes[0]->getProductTagtypeId(), 1);
        $this->assertEquals($productTagtypes[1]->getProductTagtypeId(), 3);
        $this->assertEquals($productTagtypes[2]->getProductTagtypeId(), 2);
        $this->assertEquals($productTagtypes[3]->getProductTagtypeId(), 5);
        $this->assertEquals($productTagtypes[4]->getProductTagtypeId(), 4);
    }
}
