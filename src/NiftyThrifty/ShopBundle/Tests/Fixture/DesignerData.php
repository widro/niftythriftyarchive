<?php

namespace NiftyThrifty\ShopBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use NiftyThrifty\ShopBundle\Entity\Designer;

class DesignerData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $designer1 = new Designer();
        $designer1->setDesignerName('Prada');
        $this->addReference('designer-1', $designer1);

        $designer2 = new Designer();
        $designer2->setDesignerName('Coach');
        $this->addReference('designer-2', $designer2);

        $designer3 = new Designer();
        $designer3->setDesignerName('Starter');
        $this->addReference('designer-3', $designer3);

        $designer4 = new Designer();
        $designer4->setDesignerName('Under Armour');
        $this->addReference('designer-4', $designer4);

        $manager->persist($designer1);
        $manager->persist($designer2);
        $manager->persist($designer3);
        $manager->persist($designer4);
        $manager->flush();
    }
}

