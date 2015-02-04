<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * NiftyThrifty\ShopBundle\Entity\ProductCategorySize
 *
 * @ORM\Table(name="product_category_size")
 * @ORM\Entity
 */
class ProductCategorySize
{
    /**
     * @var integer $productCategorySizeId
     *
     * @ORM\Column(name="product_category_size_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productCategorySizeId;

    /**
     * @var string $productCategorySizeName
     *
     * @ORM\Column(name="product_category_size_name", type="string", length=63, nullable=false)
     */
    private $productCategorySizeName;

    /**
     * @var string $productCategorySizeValue
     *
     * @ORM\Column(name="product_category_size_value", type="string", length=63, nullable=false)
     */
    private $productCategorySizeValue;

    /**
     * @var integer $productCategorySizeOrder
     *
     * @ORM\Column(name="product_category_size_order", type="bigint", nullable=false)
     */
    private $productCategorySizeOrder;

    /**
     * @var integer $productCategoryId
     *
     * @ORM\Column(name="product_category_id", type="bigint", nullable=false)
     */
    private $productCategoryId;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="product_category_size")
     */
    private $products;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name required, less than 63 characters
        $metadata->addPropertyConstraint('productCategorySizeName', 
                                         new Assert\NotBlank(array('message' => 'Size name can not be blank.')));
        $metadata->addPropertyConstraint('productCategorySizeName', 
                                         new Assert\Length(array('max'       => 63,
                                                                 'maxMessage'=> 'Size name must be less than 63 characters.')));
        // Value required, less than 63 characters
        $metadata->addPropertyConstraint('productCategorySizeValue', 
                                         new Assert\NotBlank(array('message' => 'Size value can not be blank.')));
        $metadata->addPropertyConstraint('productCategorySizeValue', 
                                         new Assert\Length(array('max'       => 63,
                                                                 'maxMessage'=> 'Size value must be less than 63 characters.')));

        // Order is required, and must be a number
        $metadata->addPropertyConstraint('productCategorySizeOrder',
                                         new Assert\NotBlank(array('message' => 'Order value can not be blank.')));
        $metadata->addPropertyConstraint('productCategorySizeOrder',
                                         new Assert\Type(array('type'    => 'integer',
                                                               'message' => 'Order value must be a number.')));

        // Size is required and must be a number
        $metadata->addPropertyConstraint('productCategoryId',
                                         new Assert\NotBlank(array('message' => 'Category must be selected.')));
    }

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getProductCategorySizeName();
    }

    public function getId()
    {
        return $this->getProductCategorySizeId();
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
     * Set productCategorySizeName
     *
     * @param string $productCategorySizeName
     * @return ProductCategorySize
     */
    public function setProductCategorySizeName($productCategorySizeName)
    {
        $this->productCategorySizeName = $productCategorySizeName;
    
        return $this;
    }

    /**
     * Get productCategorySizeName
     *
     * @return string 
     */
    public function getProductCategorySizeName()
    {
        return $this->productCategorySizeName;
    }

    /**
     * Set productCategorySizeValue
     *
     * @param string $productCategorySizeValue
     * @return ProductCategorySize
     */
    public function setProductCategorySizeValue($productCategorySizeValue)
    {
        $this->productCategorySizeValue = $productCategorySizeValue;
    
        return $this;
    }

    /**
     * Get productCategorySizeValue
     *
     * @return string 
     */
    public function getProductCategorySizeValue()
    {
        return $this->productCategorySizeValue;
    }

    /**
     * Set productCategorySizeOrder
     *
     * @param integer $productCategorySizeOrder
     * @return ProductCategorySize
     */
    public function setProductCategorySizeOrder($productCategorySizeOrder)
    {
        $this->productCategorySizeOrder = $productCategorySizeOrder;
    
        return $this;
    }

    /**
     * Get productCategorySizeOrder
     *
     * @return integer 
     */
    public function getProductCategorySizeOrder()
    {
        return $this->productCategorySizeOrder;
    }

    /**
     * Set productCategoryId
     *
     * @param integer $productCategoryId
     * @return ProductCategorySize
     */
    public function setProductCategoryId($productCategoryId)
    {
        $this->productCategoryId = $productCategoryId;
    
        return $this;
    }

    /**
     * Get productCategoryId
     *
     * @return integer 
     */
    public function getProductCategoryId()
    {
        return $this->productCategoryId;
    }

    /**
     * Add products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     * @return ProductCategorySize
     */
    public function addProduct(\NiftyThrifty\ShopBundle\Entity\Product $products)
    {
        $this->products[] = $products;
    
        return $this;
    }

    /**
     * Remove products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     */
    public function removeProduct(\NiftyThrifty\ShopBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
    /**
     * @var NiftyThrifty\ShopBundle\Entity\ProductCategory
     */
    private $productCategory;


    /**
     * Set productCategory
     *
     * @param NiftyThrifty\ShopBundle\Entity\ProductCategory $productCategory
     * @return ProductCategorySize
     */
    public function setProductCategory(\NiftyThrifty\ShopBundle\Entity\ProductCategory $productCategory = null)
    {
        $this->productCategory = $productCategory;
        $this->productCategoryId = $productCategory->getProductCategoryId();
    
        return $this;
    }

    /**
     * Get productCategory
     *
     * @return NiftyThrifty\ShopBundle\Entity\ProductCategory 
     */
    public function getProductCategory()
    {
        return $this->productCategory;
    }
}
