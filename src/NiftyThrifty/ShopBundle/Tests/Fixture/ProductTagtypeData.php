<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use NiftyThrifty\ShopBundle\Entity\ProductTagtype;

class ProductTagtypeData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $productTagtype1 = new ProductTagtype();
        $productTagtype1->setProductTagtypeName('Color');
        $this->addReference('product-tagtype-1', $productTagtype1);

        $productTagtype2 = new ProductTagtype();
        $productTagtype2->setProductTagtypeName('Fabric');
        $this->addReference('product-tagtype-2', $productTagtype2);

        $productTagtype3 = new ProductTagtype();
        $productTagtype3->setProductTagtypeName('Decade');
        $this->addReference('product-tagtype-3', $productTagtype3);
        
        $productTagtype4 = new ProductTagtype();
        $productTagtype4->setProductTagtypeName('Other');
        $this->addReference('product-tagtype-4', $productTagtype4);
        
        // Look needs to be ID 5 so the code constant works
        $productTagtype5 = new ProductTagtype();
        $productTagtype5->setProductTagtypeName('Look');
        $this->addReference('product-tagtype-5', $productTagtype5);

        $manager->persist($productTagtype1);
        $manager->persist($productTagtype2);
        $manager->persist($productTagtype3);
        $manager->persist($productTagtype4);
        $manager->persist($productTagtype5);
        $manager->flush();
    }
}

