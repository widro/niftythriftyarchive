<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\XProductTag;

class XProductTagData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Sizes require their categories to be loaded.
     */
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\ProductData',
                     'NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagData');
    }

    public function load(ObjectManager $manager)
    {
        $xProductTag1 = new XProductTag();
        $xProductTag1->setProductId($this->getReference('product-1')->getProductId())
                     ->setProductTagId($this->getReference('tag-look-classic')->getProductTagId());
        $manager->persist($xProductTag1);

        $xProductTag2 = new XProductTag();
        $xProductTag2->setProductId($this->getReference('product-1')->getProductId())
                     ->setProductTagId($this->getReference('tag-look-boho')->getProductTagId());
        $manager->persist($xProductTag2);

        $manager->flush();
    }
}
