<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Collection;

/**
 * Tests for the validator methods.
 */
class CollectionTest extends WebTestCase
{
    public $testCollection;
    public $validator;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->testCollection = new Collection();
        $this->testCollection->setCollectionCode('TST')
                             ->setIsShop(0)
                             ->setCollectionName('Test Collection')
                             ->setCollectionDescription('Test Collection Description')
                             ->setCollectionType('Women')
                             ->setCollectionDateStart(new \DateTime("now"))
                             ->setCollectionDateEnd(new \DateTime("now"))
                             ->setCollectionActive('no');
    }
    
    public function testCollectionValid()
    {
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCollectionIsShopBlank()
    {
        $this->testCollection->setIsShop(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select if this is a shop');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'isShop');
    }
    
    public function testCollectionIsShopWrongValue()
    {
        $this->testCollection->setIsShop('Tommy');
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'You must choose if this is a shop');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'isShop');
    }
    
    /**
     * @covers Collection::validateCollectionCode
     */
    public function testCollectionCodeBlankPasses()
    {
        $this->testCollection->setCollectionCode(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(0, $violationList->count());
    }

    /**
     * @covers Collection::validateCollectionCode
     */
    public function testCollectionCodeTooShort()
    {
        $this->testCollection->setCollectionCode('TS');
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection code must be blank or 3 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionCode');
    }

    /**
     * @covers Collection::validateCollectionCode
     */
    public function testCollectionCodeTooLong()
    {
        $this->testCollection->setCollectionCode('TST1');
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection code must be blank or 3 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionCode');
    }
    
    public function testCollectionNameBlank()
    {
        $this->testCollection->setCollectionName(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection name can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionName');
    }

    public function testCollectionNameTooLong()
    {
        $this->testCollection->setCollectionName(str_repeat('x', 61));
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection name must be less than 60 characters');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionName');
    }

    public function testCollectionDescriptionBlank()
    {
        $this->testCollection->setCollectionDescription(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection description can not be blank');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionDescription');
    }
    
    public function testCollectionTypeWrongValue()
    {
        $this->testCollection->setCollectionType('Tommy');
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection type must be Women, Men, Home, or not set');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionType');
    }

    public function testCollectionTypeNullValid()
    {
        $this->testCollection->setCollectionType(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCollectionDateStartBlank()
    {
        $this->testCollection->setCollectionDateStart(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Start date must be defined');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionDateStart');
    }

    public function testCollectionDateEndBlank()
    {
        $this->testCollection->setCollectionDateEnd(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'End date must be defined');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionDateEnd');
    }

    public function testCollectionActiveBlank()
    {
        $this->testCollection->setCollectionActive(null);
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Select whether the shop is active');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionActive');
    }
    
    public function testCollectionActiveWrongValue()
    {
        $this->testCollection->setCollectionActive('Tommy');
        $violationList = $this->validator->validate($this->testCollection);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'You must choose if this collection is active');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionActive');
    }
}
