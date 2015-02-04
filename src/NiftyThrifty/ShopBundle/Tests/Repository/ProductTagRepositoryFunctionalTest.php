<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagData;

class ProductTagRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * Find a tag by the tagtype, should be ordered by name.
     *
     * @group Repository
     * @group ProductTag
     * @covers ProductTagRepository::findByTagType
     */
    public function testFindByTagType()
    {
        $this->addFixture(new ProductTagData);
        $this->executeFixtures();

        $productTags = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductTag')
            ->findByTagType(2);

        $this->assertCount(3, $productTags);
        $this->assertEquals($productTags[0]->getProductTagId(), 6);
        $this->assertEquals($productTags[1]->getProductTagId(), 7);
        $this->assertEquals($productTags[2]->getProductTagId(), 3);
    }

    public function testFindByTagTypeEmpty()
    {
        $this->addFixture(new ProductTagData);
        $this->executeFixtures();

        $productTags = $this->em
            ->getRepository('NiftyThriftyShopBundle:ProductTag')
            ->findByTagType(99999);

        $this->assertCount(0, $productTags);
    }
    
}
