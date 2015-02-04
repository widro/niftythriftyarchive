<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use NiftyThrifty\ShopBundle\Entity\ProductTag;

class ProductTagData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Sizes require their categories to be loaded.
     */
    public function getDependencies()
    {
        return array('NiftyThrifty\ShopBundle\Tests\Fixture\ProductTagtypeData');
    }

    public function load(ObjectManager $manager)
    {
        $productTag1 = new ProductTag();
        $productTag1->setProductTagName('red');
        $productTag1->setProductTagSlug('red');
        $productTag1->setProductTagtype($this->getReference('product-tagtype-1'));

        $productTag2 = new ProductTag();
        $productTag2->setProductTagName('green');
        $productTag2->setProductTagSlug('green');
        $productTag2->setProductTagtype($this->getReference('product-tagtype-1'));

        $productTag3 = new ProductTag();
        $productTag3->setProductTagName('90s');
        $productTag3->setProductTagSlug('90s');
        $productTag3->setProductTagtype($this->getReference('product-tagtype-2'));

        $productTag4 = new ProductTag();
        $productTag4->setProductTagName('denim');
        $productTag4->setProductTagSlug('denim');
        $productTag4->setProductTagtype($this->getReference('product-tagtype-3'));

        $productTag5 = new ProductTag();
        $productTag5->setProductTagName('cotton');
        $productTag5->setProductTagSlug('cotton');
        $productTag5->setProductTagtype($this->getReference('product-tagtype-3'));

        $productTag6 = new ProductTag();
        $productTag6->setProductTagName('70s');
        $productTag6->setProductTagSlug('70s');
        $productTag6->setProductTagtype($this->getReference('product-tagtype-2'));

        $productTag7 = new ProductTag();
        $productTag7->setProductTagName('80s');
        $productTag7->setProductTagSlug('80s');
        $productTag7->setProductTagtype($this->getReference('product-tagtype-2'));
        
        $productTag8 = new ProductTag();
        $productTag8->setProductTagName('Look Classic');
        $productTag8->setProductTagSlug('look-classic');
        $productTag8->setProductTagtype($this->getReference('product-tagtype-5'));
        $this->addReference('tag-look-classic', $productTag8);

        $productTag9 = new ProductTag();
        $productTag9->setProductTagName('Look Boho');
        $productTag9->setProductTagSlug('look-boho');
        $productTag9->setProductTagtype($this->getReference('product-tagtype-5'));
        $this->addReference('tag-look-boho', $productTag9);

        $productTag10 = new ProductTag();
        $productTag10->setProductTagName('Look Rocker');
        $productTag10->setProductTagSlug('look-rocker');
        $productTag10->setProductTagtype($this->getReference('product-tagtype-5'));
        $this->addReference('tag-look-rocker', $productTag10);

        $manager->persist($productTag1);
        $manager->persist($productTag2);
        $manager->persist($productTag3);
        $manager->persist($productTag4);
        $manager->persist($productTag5);
        $manager->persist($productTag6);
        $manager->persist($productTag7);
        $manager->persist($productTag8);
        $manager->persist($productTag9);
        $manager->persist($productTag10);
        $manager->flush();
    }
}
