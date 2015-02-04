<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Entity\Product;
use NiftyThrifty\ShopBundle\Tests\Fixture\ProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\XProductTagData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Tests for the validator methods.
 */
class ProductTest extends WebTestCase
{
    public $testProduct;
    public $validator;
    public $em;
    public $testFilePath;
    public $container;
    
    public function setUp()
    {
        $this->testFilePath = '/var/www/Symfony/src/NiftyThrifty/ShopBundle/Tests/TestFiles/';
        $kernel = static::createKernel();
        $kernel->boot();
        $this->validator = $kernel->getContainer()->get('validator');
        $this->em        = $kernel->getContainer()->get('doctrine')->getManager();
        $this->container = $kernel->getContainer();
        $this->testProduct = new Product();
        $this->testProduct
             ->setProductName('Validator Product 1')
             ->setProductDescription('Description 1')
             ->setProductCategorySizeId(1)
             ->setProductOverallCondition('Description of condition')
             ->setProductPrice(45)
             ->setProductDetailedConditionValue(2)
             ->setProductDetailedConditionDescription('Even more description')
             ->setProductFabric('denim')
             ->setProductMeasurements('43 inches')
             ->setProductAvailability('sale')
             ->setProductHeavy('no')
             //->setProductVisual1Large()
             //->setProductVisual2Large()
             //->setProductVisual3Large()
             ->setCollectionId(1)
             ->setDesignerId(2)
             ->setProductTaxes(7)
             ->setProductCode('TST1')
             ->setProductTaxesActive('no');
    }

    public function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }

    public function testValidProduct()
    {
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductNameBlank()
    {
        $this->testProduct->setProductName(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productName');
    }

    public function testProductNameTooLong()
    {
        $this->testProduct->setProductName(str_repeat('x', 64));
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product name must be less than 63 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productName');
    }

    public function testProductDescriptionBlank()
    {
        $this->testProduct->setProductDescription(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product description can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productDescription');
    }

    public function testProductCategorySizeIdBlank()
    {
        $this->testProduct->setProductCategorySizeId(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product size must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCategorySizeId');
    }

    public function testProductOverallConditionBlank()
    {
        $this->testProduct->setProductOverallCondition(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product overall condition can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productOverallCondition');
    }

    public function testProductOverallConditionTooLong()
    {
        $this->testProduct->setProductOverallCondition(str_repeat('x', 64));
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product overall condition must be less than 63 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productOverallCondition');
    }
    
    public function testProductDetailedConditionValueBlank()
    {
        $this->testProduct->setProductDetailedConditionValue(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product detailed condition value can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productDetailedConditionValue');
    }

    public function testProductDetailedConditionValueTooSmall()
    {
        $this->testProduct->setProductDetailedConditionValue(0);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Condition score too small; must be between 1 and 5.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productDetailedConditionValue');
    }
    
    public function testProductDetailedConditionValueTooLarge()
    {
        $this->testProduct->setProductDetailedConditionValue(6);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Condition score too large; must be between 1 and 5.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productDetailedConditionValue');
    }
    
    public function testProductDetailedConditionDescriptionBlank()
    {
        $this->testProduct->setProductDetailedConditionDescription(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product detailed condition description can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productDetailedConditionDescription');
    }

    public function testProductDetailedConditionDescriptionTooLong()
    {
        $this->testProduct->setProductDetailedConditionDescription(str_repeat('x', 64));
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product detailed condition description must be less than 63 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productDetailedConditionDescription');
    }

    public function testProductPriceBlank()
    {
        $this->testProduct->setProductPrice(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product price can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productPrice');
    }

    public function testProductPriceBadValue()
    {
        $this->testProduct->setProductPrice('test');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product price must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productPrice');
    }
    
    public function testProductOldPriceBadValue()
    {
        $this->testProduct->setProductOldPrice('test');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Old product price must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productOldPrice');
    }
    
    public function testProductOldPricePass()
    {
        $this->testProduct->setProductOldPrice(35);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductFabricBlank()
    {
        $this->testProduct->setProductFabric(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product fabric can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productFabric');
    }

    public function testProductFabricTooLong()
    {
        $this->testProduct->setProductFabric(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product fabric must be less than 255 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productFabric');
    }
    
    public function testProductMeasurementBlank()
    {
        $this->testProduct->setProductMeasurements(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product measurements can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productMeasurements');
    }

    public function testProductMeasurementTooLong()
    {
        $this->testProduct->setProductMeasurements(str_repeat('x', 256));
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product measurements must be less than 255 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productMeasurements');
    }
    
    public function testProductAvailableBlank()
    {
        $this->testProduct->setProductAvailability(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product availability can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productAvailability');
    }

    public function testProductAvailableBadValue()
    {
        $this->testProduct->setProductAvailability('test');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Choose a valid availability.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productAvailability');
    }

    public function testProductAvailabilitySold()
    {
        $this->testProduct->setProductAvailability('sold');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }

    public function testProductAvailabilityReserved()
    {
        $this->testProduct->setProductAvailability('reserved');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testProductHeavyBlank()
    {
        $this->testProduct->setProductHeavy(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product heavy can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productHeavy');
    }

    public function testProductHeavyBadValue()
    {
        $this->testProduct->setProductHeavy('test');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Choose a valid product heavy.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productHeavy');
    }

    public function testCollectionIdBlank()
    {
        $this->testProduct->setCollectionId(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Collection must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'collectionId');
    }

    public function testProductTaxesBlank()
    {
        $this->testProduct->setProductTaxes(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tax value can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTaxes');
    }

    public function testProductTaxesBadValue()
    {
        $this->testProduct->setProductTaxes('test');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tax must be a number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTaxes');
    }
    
    public function testProductTaxesActiveBlank()
    {
        $this->testProduct->setProductTaxesActive(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Tax active must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTaxesActive');
    }

    public function testProductTaxesActiveBadValue()
    {
        $this->testProduct->setProductTaxesActive('test');
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Choose if taxes are active.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productTaxesActive');
    }
    
    public function testProductCodeBlank()
    {
        $this->testProduct->setProductCode(null);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product code can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCode');
    }

    public function testProductCodeTooLong()
    {
        $this->testProduct->setProductCode(str_repeat('x', 11));
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Product code must be less than 10 characters.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productCode');
    }

    public function testProductVisual1LargeNotAnImage()
    {
        $textFile = new File($this->testFilePath . 'testFile.txt', 'testFile.txt');
        $this->testProduct->setProductVisual1Large($textFile);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Image 1 must be an image.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productVisual1Large');
    }

    public function testProductVisual1LargeImagePass()
    {
        $imageFile = new File($this->testFilePath . 'productvis1.jpg', 'productvis1.jpg');
        $this->testProduct->setProductVisual1Large($imageFile);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testProductVisual2LargeNotAnImage()
    {
        $textFile = new File($this->testFilePath . 'testFile.txt', 'testFile.txt');
        $this->testProduct->setProductVisual2Large($textFile);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Image 2 must be an image.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productVisual2Large');
    }

    public function testProductVisual2LargeImagePass()
    {
        $imageFile = new File($this->testFilePath . 'productvis2.jpg', 'productvis2.jpg');
        $this->testProduct->setProductVisual2Large($imageFile);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testProductVisual3LargeNotAnImage()
    {
        $textFile = new File($this->testFilePath . 'testFile.txt', 'testFile.txt');
        $this->testProduct->setProductVisual3Large($textFile);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Image 3 must be an image.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'productVisual3Large');
    }

    public function testProductVisual3LargeImagePass()
    {
        $imageFile = new File($this->testFilePath . 'productvis3.jpg', 'productvis3.jpg');
        $this->testProduct->setProductVisual3Large($imageFile);
        $violationList = $this->validator->validate($this->testProduct);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testConstants()
    {
        $this->assertEquals(Product::RESERVED,                  'reserved');
        $this->assertEquals(Product::SOLD,                      'sold');
        $this->assertEquals(Product::SALE,                      'sale');
        $this->assertEquals(Product::DEFAULT_ORDER_COLUMN,      'productName');
        $this->assertEquals(Product::DEFAULT_ORDER_DIRECTION,   'ASC');
    }
    
    public function testGetId()
    {
        $this->assertEquals($this->testProduct->getId(), $this->testProduct->getProductId());
    }
    
    public function testAssociationCollection()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(1);
        $this->assertEquals($product->getCollection()->getCollectionId(), 1);
    }

    public function testAssociationProductCategorySize()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(1);
        $this->assertEquals($product->getProductCategorySize()->getProductCategorySizeId(), 1);
    }

    public function testAssociationDesigner()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(3);
        $this->assertEquals($product->getDesigner()->getDesignerId(), 1);
    }

    public function testAssociationProductTags()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $loader->addFixture(new ProductData);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor->execute($loader->getFixtures());
        
        $product = $this->em->getRepository('NiftyThriftyShopBundle:Product')->find(2);
        $this->assertCount(2, $product->getProductTags());
    }
}
