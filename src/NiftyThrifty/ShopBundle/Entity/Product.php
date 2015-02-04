<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use NiftyThrifty\ShopBundle\Entity\FileUploadEntity;

/**
 * NiftyThrifty\ShopBundle\Entity\Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product extends FileUploadEntity
{
    /**
     * @var integer $productId
     *
     * @ORM\Column(name="product_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productId;

    /**
     * @var string $productName
     *
     * @ORM\Column(name="product_name", type="string", length=63, nullable=false)
     */
    private $productName;

    /**
     * @var string $productDescription
     *
     * @ORM\Column(name="product_description", type="text", nullable=false)
     */
    private $productDescription;

    /**
     * @var integer $productCategorySizeId
     *
     * @ORM\Column(name="product_category_size_id", type="bigint", nullable=false)
     */
    private $productCategorySizeId;

    /**
     * @var integer $productTypeId
     *
     * @ORM\Column(name="product_type_id", type="bigint", nullable=true)
     */
    private $productTypeId;

    /**
     * @var string $productOverallCondition
     *
     * @ORM\Column(name="product_overall_condition", type="string", length=63, nullable=false)
     */
    private $productOverallCondition;

    /**
     * @var integer $productPrice
     *
     * @ORM\Column(name="product_price", type="integer", nullable=false)
     */
    private $productPrice;

    /**
     * @var integer $productOldPrice
     *
     * @ORM\Column(name="product_old_price", type="integer", nullable=true)
     */
    private $productOldPrice;

    /**
     * @var integer $productDiscount
     *
     * @ORM\Column(name="product_discount", type="integer", nullable=true)
     */
    private $productDiscount;

    /**
     * @var boolean $productDetailedConditionValue
     *
     * @ORM\Column(name="product_detailed_condition_value", type="integer", nullable=false)
     */
    private $productDetailedConditionValue;

    /**
     * @var string $productDetailedConditionDescription
     *
     * @ORM\Column(name="product_detailed_condition_description", type="string", length=63, nullable=false)
     */
    private $productDetailedConditionDescription;

    /**
     * @var string $productFabric
     *
     * @ORM\Column(name="product_fabric", type="string", length=255, nullable=false)
     */
    private $productFabric;

    /**
     * @var string $productMeasurements
     *
     * @ORM\Column(name="product_measurements", type="string", length=255, nullable=false)
     */
    private $productMeasurements;

    /**
     * @var string $productAvailability
     *
     * @ORM\Column(name="product_availability", type="string", nullable=false)
     */
    private $productAvailability;

    /**
     * @var string $productHeavy
     *
     * @ORM\Column(name="product_heavy", type="string", nullable=false)
     */
    private $productHeavy;

    /**
     * @var string $productVisual1
     *
     * @ORM\Column(name="product_visual1", type="string", length=255, nullable=true)
     */
    private $productVisual1;

    /**
     * @var string $productVisual1Large
     *
     * @ORM\Column(name="product_visual1_large", type="string", length=255, nullable=true)
     */
    private $productVisual1Large;

    /**
     * @var string $productVisual2
     *
     * @ORM\Column(name="product_visual2", type="string", length=255, nullable=true)
     */
    private $productVisual2;

    /**
     * @var string $productVisual2Large
     *
     * @ORM\Column(name="product_visual2_large", type="string", length=255, nullable=true)
     */
    private $productVisual2Large;

    /**
     * @var string $productVisual3
     *
     * @ORM\Column(name="product_visual3", type="string", length=255, nullable=true)
     */
    private $productVisual3;

    /**
     * @var string $productVisual3Large
     *
     * @ORM\Column(name="product_visual3_large", type="string", length=255, nullable=true)
     */
    private $productVisual3Large;

    /**
     * @var integer $collectionId
     *
     * @ORM\Column(name="collection_id", type="bigint", nullable=false)
     */
    private $collectionId;

    /**
     * @var integer $designerId
     *
     * @ORM\Column(name="designer_id", type="bigint", nullable=true)
     */
    private $designerId;

    /**
     * @var string $productHashtag
     *
     * @ORM\Column(name="product_hashtag", type="string", length=255, nullable=true)
     */
    private $productHashtag;

    /**
     * @var string $productInstagramMediaIdNifty
     *
     * @ORM\Column(name="product_instagram_media_id_nifty", type="string", length=255, nullable=true)
     */
    private $productInstagramMediaIdNifty;

    /**
     * @var string $productInstagramMediaIdCustomer
     *
     * @ORM\Column(name="product_instagram_media_id_customer", type="string", length=255, nullable=true)
     */
    private $productInstagramMediaIdCustomer;

    /**
     * @var float $productTaxes
     *
     * @ORM\Column(name="product_taxes", type="float", nullable=false)
     */
    private $productTaxes;

    /**
     * @var string $productTaxesActive
     *
     * @ORM\Column(name="product_taxes_active", type="string", nullable=false)
     */
    private $productTaxesActive;

    /**
     * @var string $productCode
     *
     * @ORM\Column(name="product_code", type="string", length=10, nullable=false)
     */
    private $productCode;

    /**
     * @var string $productTagsize
     *
     * @ORM\Column(name="product_tagsize", type="string", length=255, nullable=true)
     */
    private $productTagsize;

    /**
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="products")
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="collection_id")
     */
    protected $collection;

    /**
     * @var NiftyThrifty\ShopBundle\Entity\ProductCategorySize
     */
    private $productCategorySize;

    /**
     * @var NiftyThrifty\ShopBundle\Entity\Designer
     */
    private $designer;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $productTags;

    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name must be not blank and less than 63 characters
        $metadata->addPropertyConstraint('productName',
                                         new Assert\NotBlank(array('message' => 'Product name can not be blank.')));
        $metadata->addPropertyConstraint('productName',
                                         new Assert\Length(array('max'        => 63,
                                                                 'maxMessage' => 'Product name must be less than 63 characters.')));

        // Name must be not blank.  Database field is long-text so there is really no reasonable max.
        $metadata->addPropertyConstraint('productDescription', new Assert\NotBlank(array('message' => 'Product description can not be blank.')));

        // Product category size id must not be blank and should be a number
        $metadata->addPropertyConstraint('productCategorySizeId',
                                         new Assert\NotBlank(array('message' => 'Product size must be selected.')));

        // Condition stuff must be not blank and less than 63 characters
        $metadata->addPropertyConstraint('productOverallCondition',
                                         new Assert\NotBlank(array('message' => 'Product overall condition can not be blank.')));
        $metadata->addPropertyConstraint('productOverallCondition',
                                         new Assert\Length(array('max'        => 63,
                                                                 'maxMessage' => 'Product overall condition must be less than 63 characters.')));

        // Condition value is a number between 1 and 5.
        $metadata->addPropertyConstraint('productDetailedConditionValue',
                                         new Assert\NotBlank(array('message' => 'Product detailed condition value can not be blank.')));
        $metadata->addPropertyConstraint('productDetailedConditionValue',
                                         new Assert\Range(array('min' => 1,
                                                                'max' => 5,
                                                                'minMessage' => 'Condition score too small; must be between 1 and 5.',
                                                                'maxMessage' => 'Condition score too large; must be between 1 and 5.')));

        // Condition description is not blank and less than 63 characters.
        $metadata->addPropertyConstraint('productDetailedConditionDescription',
                                         new Assert\NotBlank(array('message' => 'Product detailed condition description can not be blank.')));
        $metadata->addPropertyConstraint('productDetailedConditionDescription',
                                         new Assert\Length(array('max'        => 63,
                                                                 'maxMessage' => 'Product detailed condition description must be less than 63 characters.')));

        // Price is required.
        $metadata->addPropertyConstraint('productPrice', new Assert\NotBlank(array('message' => 'Product price can not be blank.')));
        $metadata->addPropertyConstraint('productPrice', new Assert\Type(array('type'    => 'numeric',
                                                                               'message' => 'Product price must be a number.')));

        // If old price is set, it must be a number.
        $metadata->addPropertyConstraint('productOldPrice', new Assert\Type(array('type'    => 'numeric',
                                                                                  'message' => 'Old product price must be a number.')));

        // Fabric is required and less than 255 characters
        $metadata->addPropertyConstraint('productFabric',
                                         new Assert\NotBlank(array('message' => 'Product fabric can not be blank.')));
        $metadata->addPropertyConstraint('productFabric',
                                         new Assert\Length(array('max'        => 255,
                                                                 'maxMessage' => 'Product fabric must be less than 255 characters.')));

        // Measurements are required and less than 255 characters
        $metadata->addPropertyConstraint('productMeasurements',
                                         new Assert\NotBlank(array('message' => 'Product measurements can not be blank.')));
        $metadata->addPropertyConstraint('productMeasurements',
                                         new Assert\Length(array('max'        => 255,
                                                                 'maxMessage' => 'Product measurements must be less than 255 characters.')));

        // Availability is required and is one of three values
        $metadata->addPropertyConstraint('productAvailability',
                                         new Assert\NotBlank(array('message' => 'Product availability can not be blank.')));
        $metadata->addPropertyConstraint('productAvailability',
                                         new Assert\Choice(array('message' => 'Choose a valid availability.',
                                                                 'choices' => array(self::RESERVED,
                                                                                    self::SOLD,
                                                                                    self::ETSY,
                                                                                    self::EBAY,
                                                                                    self::SALE))));

        // Heavy is required and is yes/no
        $metadata->addPropertyConstraint('productHeavy',
                                         new Assert\NotBlank(array('message' => 'Product heavy can not be blank.')));
        $metadata->addPropertyConstraint('productHeavy',
                                         new Assert\Choice(array('message' => 'Choose a valid product heavy.',
                                                                 'choices' => array('yes','no'))));

        // Collection ID is required
        $metadata->addPropertyConstraint('collectionId', new Assert\NotBlank(array('message' => 'Collection must be selected.')));

        // Tax info is required
        $metadata->addPropertyConstraint('productTaxes', new Assert\NotBlank(array('message' => 'Tax value can not be blank.')));
        $metadata->addPropertyConstraint('productTaxes', new Assert\Type(array('type'    => 'numeric',
                                                                               'message' => 'Tax must be a number.')));
        $metadata->addPropertyConstraint('productTaxesActive', new Assert\NotBlank(array('message' => 'Tax active must be selected.')));
        $metadata->addPropertyConstraint('productTaxesActive',
                                         new Assert\Choice(array('message' => 'Choose if taxes are active.',
                                                                 'choices' => array('yes','no'))));

        // Product code is required
        $metadata->addPropertyConstraint('productCode',
                                         new Assert\NotBlank(array('message' => 'Product code can not be blank.')));
        $metadata->addPropertyConstraint('productCode',
                                         new Assert\Length(array('max'        => 10,
                                                                 'maxMessage' => 'Product code must be less than 10 characters.')));

        // If image fields are populated, ensure they're images
        $metadata->addPropertyConstraint('productVisual1Large',
                                         new Assert\Image(array('mimeTypesMessage' => 'Image 1 must be an image.')));
        $metadata->addPropertyConstraint('productVisual2Large',
                                         new Assert\Image(array('mimeTypesMessage' => 'Image 2 must be an image.')));
        $metadata->addPropertyConstraint('productVisual3Large',
                                         new Assert\Image(array('mimeTypesMessage' => 'Image 3 must be an image.')));
    }

    /**
     * Valid states for productAvailability
     */
    const RESERVED                  = 'reserved';       // User has reserved.
    const SOLD                      = 'sold';           // User has bought.
    const SALE                      = 'sale';           // Available
    const ETSY                      = 'etsy';           // Etsy
    const EBAY                      = 'ebay';           // Ebay
    const DEFAULT_ORDER_COLUMN      = 'productName';
    const DEFAULT_ORDER_DIRECTION   = 'ASC';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productTags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->getProductId();
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set productName
     *
     * @param string $productName
     * @return Product
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productDescription
     *
     * @param string $productDescription
     * @return Product
     */
    public function setProductDescription($productDescription)
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    /**
     * Get productDescription
     *
     * @return string
     */
    public function getProductDescription()
    {
        return $this->productDescription;
    }

    /**
     * Set productCategorySizeId
     *
     * @param integer $productCategorySizeId
     * @return Product
     */
    public function setProductCategorySizeId($productCategorySizeId)
    {
        $this->productCategorySizeId = $productCategorySizeId;

        return $this;
    }

    /**
     * Get productCategorySizeId
     *
     * @return integer
     */
    public function getProductCategorySizeId()
    {
        return $this->productCategorySizeId;
    }

    /**
     * Set productTypeId
     *
     * @param integer $productTypeId
     * @return Product
     */
    public function setProductTypeId($productTypeId)
    {
        $this->productTypeId = $productTypeId;

        return $this;
    }

    /**
     * Get productTypeId
     *
     * @return integer
     */
    public function getProductTypeId()
    {
        return $this->productTypeId;
    }

    /**
     * Set productOverallCondition
     *
     * @param string $productOverallCondition
     * @return Product
     */
    public function setProductOverallCondition($productOverallCondition)
    {
        $this->productOverallCondition = $productOverallCondition;

        return $this;
    }

    /**
     * Get productOverallCondition
     *
     * @return string
     */
    public function getProductOverallCondition()
    {
        return $this->productOverallCondition;
    }

    /**
     * Set productPrice
     *
     * @param integer $productPrice
     * @return Product
     */
    public function setProductPrice($productPrice)
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    /**
     * Get productPrice
     *
     * @return integer
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
     * Set productOldPrice
     *
     * @param integer $productOldPrice
     * @return Product
     */
    public function setProductOldPrice($productOldPrice)
    {
        $this->productOldPrice = $productOldPrice;

        return $this;
    }

    /**
     * Get productOldPrice
     *
     * @return integer
     */
    public function getProductOldPrice()
    {
        return $this->productOldPrice;
    }

    /**
     * Set productDiscount
     *
     * @param integer $productDiscount
     * @return Product
     */
    public function setProductDiscount($productDiscount)
    {
        $this->productDiscount = $productDiscount;

        return $this;
    }

    /**
     * Get productDiscount
     *
     * @return integer
     */
    public function getProductDiscount()
    {
        return $this->productDiscount;
    }

    /**
     * Set productDetailedConditionValue
     *
     * @param boolean $productDetailedConditionValue
     * @return Product
     */
    public function setProductDetailedConditionValue($productDetailedConditionValue)
    {
        $this->productDetailedConditionValue = $productDetailedConditionValue;

        return $this;
    }

    /**
     * Get productDetailedConditionValue
     *
     * @return boolean
     */
    public function getProductDetailedConditionValue()
    {
        return $this->productDetailedConditionValue;
    }

    /**
     * Set productDetailedConditionDescription
     *
     * @param string $productDetailedConditionDescription
     * @return Product
     */
    public function setProductDetailedConditionDescription($productDetailedConditionDescription)
    {
        $this->productDetailedConditionDescription = $productDetailedConditionDescription;

        return $this;
    }

    /**
     * Get productDetailedConditionDescription
     *
     * @return string
     */
    public function getProductDetailedConditionDescription()
    {
        return $this->productDetailedConditionDescription;
    }

    /**
     * Set productFabric
     *
     * @param string $productFabric
     * @return Product
     */
    public function setProductFabric($productFabric)
    {
        $this->productFabric = $productFabric;

        return $this;
    }

    /**
     * Get productFabric
     *
     * @return string
     */
    public function getProductFabric()
    {
        return $this->productFabric;
    }

    /**
     * Set productMeasurements
     *
     * @param string $productMeasurements
     * @return Product
     */
    public function setProductMeasurements($productMeasurements)
    {
        $this->productMeasurements = $productMeasurements;

        return $this;
    }

    /**
     * Get productMeasurements
     *
     * @return string
     */
    public function getProductMeasurements()
    {
        return $this->productMeasurements;
    }

    /**
     * Set productAvailability
     *
     * @param string $productAvailability
     * @return Product
     */
    public function setProductAvailability($productAvailability)
    {
        $this->productAvailability = $productAvailability;

        return $this;
    }

    /**
     * Get productAvailability
     *
     * @return string
     */
    public function getProductAvailability()
    {
        return $this->productAvailability;
    }

    /**
     * Set productHeavy
     *
     * @param string $productHeavy
     * @return Product
     */
    public function setProductHeavy($productHeavy)
    {
        $this->productHeavy = $productHeavy;

        return $this;
    }

    /**
     * Get productHeavy
     *
     * @return string
     */
    public function getProductHeavy()
    {
        return $this->productHeavy;
    }

    /**
     * Set productVisual1
     *
     * @param string $productVisual1
     * @return Product
     */
    public function setProductVisual1($productVisual1)
    {
        $this->productVisual1 = $productVisual1;

        return $this;
    }

    /**
     * Get productVisual1
     *
     * @return string
     */
    public function getProductVisual1()
    {
        return $this->productVisual1;
    }

    /**
     * Set productVisual1Large
     *
     * @param string $productVisual1Large
     * @return Product
     */
    public function setProductVisual1Large($productVisual1Large)
    {
        if ($this->productVisual1Large) {
            $this->oldVisual1Large = new File($this->getWebDirectory() . $this->productVisual1Large);
        }
        $this->productVisual1Large = $productVisual1Large;

        return $this;
    }

    /**
     * Get productVisual1Large
     *
     * @return string
     */
    public function getProductVisual1Large()
    {
        return $this->productVisual1Large;
    }

    /**
     * Set productVisual2
     *
     * @param string $productVisual2
     * @return Product
     */
    public function setProductVisual2($productVisual2)
    {
        $this->productVisual2 = $productVisual2;

        return $this;
    }

    /**
     * Get productVisual2
     *
     * @return string
     */
    public function getProductVisual2()
    {
        return $this->productVisual2;
    }

    /**
     * Set productVisual2Large
     *
     * @param string $productVisual2Large
     * @return Product
     */
    public function setProductVisual2Large($productVisual2Large)
    {
        if ($this->productVisual2Large) {
            $this->oldVisual2Large = new File($this->getWebDirectory() . $this->productVisual2Large);
        }
        $this->productVisual2Large = $productVisual2Large;

        return $this;
    }

    /**
     * Get productVisual2Large
     *
     * @return string
     */
    public function getProductVisual2Large()
    {
        return $this->productVisual2Large;
    }

    /**
     * Set productVisual3
     *
     * @param string $productVisual3
     * @return Product
     */
    public function setProductVisual3($productVisual3)
    {
        $this->productVisual3 = $productVisual3;

        return $this;
    }

    /**
     * Get productVisual3
     *
     * @return string
     */
    public function getProductVisual3()
    {
        return $this->productVisual3;
    }

    /**
     * Set productVisual3Large
     *
     * @param string $productVisual3Large
     * @return Product
     */
    public function setProductVisual3Large($productVisual3Large)
    {
        if ($this->productVisual3Large) {
            $this->oldVisual3Large = new File($this->getWebDirectory() . $this->productVisual3Large);
        }
        $this->productVisual3Large = $productVisual3Large;

        return $this;
    }

    /**
     * Get productVisual3Large
     *
     * @return string
     */
    public function getProductVisual3Large()
    {
        return $this->productVisual3Large;
    }

    /**
     * Set collectionId
     *
     * @param integer $collectionId
     * @return Product
     */
    public function setCollectionId($collectionId)
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    /**
     * Get collectionId
     *
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Set designerId
     *
     * @param integer $designerId
     * @return Product
     */
    public function setDesignerId($designerId)
    {
        $this->designerId = $designerId;

        return $this;
    }

    /**
     * Get designerId
     *
     * @return integer
     */
    public function getDesignerId()
    {
        return $this->designerId;
    }

    /**
     * Set productHashtag
     *
     * @param string $productHashtag
     * @return Product
     */
    public function setProductHashtag($productHashtag)
    {
        $this->productHashtag = $productHashtag;

        return $this;
    }

    /**
     * Get productHashtag
     *
     * @return string
     */
    public function getProductHashtag()
    {
        return $this->productHashtag;
    }

    /**
     * Set productInstagramMediaIdNifty
     *
     * @param string $productInstagramMediaIdNifty
     * @return Product
     */
    public function setProductInstagramMediaIdNifty($productInstagramMediaIdNifty)
    {
        $this->productInstagramMediaIdNifty = $productInstagramMediaIdNifty;

        return $this;
    }

    /**
     * Get productInstagramMediaIdNifty
     *
     * @return string
     */
    public function getProductInstagramMediaIdNifty()
    {
        return $this->productInstagramMediaIdNifty;
    }

    /**
     * Set productInstagramMediaIdCustomer
     *
     * @param string $productInstagramMediaIdCustomer
     * @return Product
     */
    public function setProductInstagramMediaIdCustomer($productInstagramMediaIdCustomer)
    {
        $this->productInstagramMediaIdCustomer = $productInstagramMediaIdCustomer;

        return $this;
    }

    /**
     * Get productInstagramMediaIdCustomer
     *
     * @return string
     */
    public function getProductInstagramMediaIdCustomer()
    {
        return $this->productInstagramMediaIdCustomer;
    }

    /**
     * Set productTaxes
     *
     * @param float $productTaxes
     * @return Product
     */
    public function setProductTaxes($productTaxes)
    {
        $this->productTaxes = $productTaxes;

        return $this;
    }

    /**
     * Get productTaxes
     *
     * @return float
     */
    public function getProductTaxes()
    {
        return $this->productTaxes;
    }

    /**
     * Set productTaxesActive
     *
     * @param string $productTaxesActive
     * @return Product
     */
    public function setProductTaxesActive($productTaxesActive)
    {
        $this->productTaxesActive = $productTaxesActive;

        return $this;
    }

    /**
     * Get productTaxesActive
     *
     * @return string
     */
    public function getProductTaxesActive()
    {
        return $this->productTaxesActive;
    }

    /**
     * Set productCode
     *
     * @param string $productCode
     * @return Product
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get productCode
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set productTagsize
     *
     * @param string $productTagsize
     * @return Product
     */
    public function setProductTagsize($productTagsize)
    {
        $this->productTagsize = $productTagsize;

        return $this;
    }

    /**
     * Get productTagsize
     *
     * @return string
     */
    public function getProductTagsize()
    {
        return $this->productTagsize;
    }

    /**
     * Set collection
     *
     * @param NiftyThrifty\ShopBundle\Entity\Collection $collection
     * @return Product
     */
    public function setCollection(\NiftyThrifty\ShopBundle\Entity\Collection $collection = null)
    {
        $this->collection = $collection;
        $this->collectionId = $collection->getCollectionId();

        return $this;
    }

    /**
     * Get collection
     *
     * @return NiftyThrifty\ShopBundle\Entity\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set productCategorySize
     *
     * @param NiftyThrifty\ShopBundle\Entity\ProductCategorySize $productCategorySize
     * @return Product
     */
    public function setProductCategorySize(\NiftyThrifty\ShopBundle\Entity\ProductCategorySize $productCategorySize = null)
    {
        $this->productCategorySize = $productCategorySize;
        $this->productCategorySizeId = $productCategorySize->getProductCategorySizeId();

        return $this;
    }

    /**
     * Get productCategorySize
     *
     * @return NiftyThrifty\ShopBundle\Entity\ProductCategorySize
     */
    public function getProductCategorySize()
    {
        return $this->productCategorySize;
    }

    /**
     * Set designer
     *
     * @param NiftyThrifty\ShopBundle\Entity\Designer $designer
     * @return Product
     */
    public function setDesigner(\NiftyThrifty\ShopBundle\Entity\Designer $designer = null)
    {
        $this->designer = $designer;

        return $this;
    }

    /**
     * Get designer
     *
     * @return NiftyThrifty\ShopBundle\Entity\Designer
     */
    public function getDesigner()
    {
        return $this->designer;
    }

    /**
     * Add productTags
     *
     * @param \NiftyThrifty\ShopBundle\Entity\ProductTag $productTags
     * @return Product
     */
    public function addProductTag(\NiftyThrifty\ShopBundle\Entity\ProductTag $productTags)
    {
        $this->productTags[] = $productTags;

        return $this;
    }

    /**
     * Remove productTags
     *
     * @param \NiftyThrifty\ShopBundle\Entity\ProductTag $productTags
     */
    public function removeProductTag(\NiftyThrifty\ShopBundle\Entity\ProductTag $productTags)
    {
        $this->productTags->removeElement($productTags);
    }

    /**
     * Get productTags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductTags()
    {
        return $this->productTags;
    }


    /**
     * @ORM\PrePersist
     */
    public function processImages()
    {
        if ($this->productVisual1Large instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->productVisual1Large);
            $this->fileVisual1Large       = $this->productVisual1Large;
            $this->tempVisual1Large       = $this->productVisual1Large;
            $this->productVisual1Large    = $this->getUploadDirectory()
                                                . $filename
                                                . '.'
                                                . $this->fileVisual1Large->guessExtension();
        }

        if ($this->productVisual2Large instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->productVisual2Large);
            $this->fileVisual2Large       = $this->productVisual2Large;
            $this->tempVisual2Large       = $this->productVisual2Large;
            $this->productVisual2Large    = $this->getUploadDirectory()
                                                . $filename
                                                . '.'
                                                . $this->fileVisual2Large->guessExtension();
        }

        if ($this->productVisual3Large instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->productVisual3Large);
            $this->fileVisual3Large       = $this->productVisual3Large;
            $this->tempVisual3Large       = $this->productVisual3Large;
            $this->productVisual3Large    = $this->getUploadDirectory()
                                                . $filename
                                                . '.'
                                                . $this->fileVisual3Large->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist
     */
    public function upload()
    {
        if ($this->fileVisual1Large instanceof UploadedFile) {
            $this->fileVisual1Large->move($this->getUploadRootDirectory(), $this->productVisual1Large);
            if (file_exists($this->tempVisual1Large)) unlink($this->tempVisual1Large);
        }
        if ($this->fileVisual2Large instanceof UploadedFile) {
            $this->fileVisual2Large->move($this->getUploadRootDirectory(), $this->productVisual2Large);
            if (file_exists($this->tempVisual2Large)) unlink($this->tempVisual2Large);
        }
        if ($this->fileVisual3Large instanceof UploadedFile) {
            $this->fileVisual3Large->move($this->getUploadRootDirectory(), $this->productVisual3Large);
            if (file_exists($this->tempVisual3Large)) unlink($this->tempVisual3Large);
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function checkImages()
    {
        $this->processImages();
    }

    /**
     * @ORM\PostUpdate
     */
    public function checkUpload()
    {
        $this->upload();
        if ($this->oldVisual1Large && $this->oldVisual1Large != $this->getWebDirectory() . $this->productVisual1Large) {
            unlink($this->oldVisual1Large);
        }
        if ($this->oldVisual2Large && $this->oldVisual2Large != $this->getWebDirectory() . $this->productVisual2Large) {
            unlink($this->oldVisual2Large);
        }
        if ($this->oldVisual3Large && $this->oldVisual3Large != $this->getWebDirectory() . $this->productVisual3Large) {
            unlink($this->oldVisual3Large);
        }
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteFile()
    {
        if ($this->productVisual1Large) unlink($this->getWebDirectory() . $this->productVisual1Large);
        if ($this->productVisual2Large) unlink($this->getWebDirectory() . $this->productVisual2Large);
        if ($this->productVisual3Large) unlink($this->getWebDirectory() . $this->productVisual3Large);
    }
}
