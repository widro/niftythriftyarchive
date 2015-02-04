<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NiftyThrifty\ShopBundle\Entity\ProductCategory
 *
 * @ORM\Table(name="product_category")
 * @ORM\Entity
 */
class ProductCategory
{
    /**
     * @var integer $productCategoryId
     *
     * @ORM\Column(name="product_category_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productCategoryId;

    /**
     * @var string $productCategoryName
     *
     * @ORM\Column(name="product_category_name", type="string", length=63, nullable=false)
     */
    private $productCategoryName;

    /**
     * @var int
     */
    private $navigationOrder;

    /**
     * @var string
     */
    private $inNavigation;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Category is unique, required, and less than 63 characters.
        $metadata->addPropertyConstraint('productCategoryName', new Assert\NotBlank(array('message' => 'Category name can not be blank.')));
        $metadata->addPropertyConstraint('productCategoryName', new Assert\Length(array('max'       => 63,
                                                                                        'maxMessage'=> 'Category name must be less than 63 characters.')));
        $metadata->addConstraint(new UniqueEntity(array('fields' => 'productCategoryName',
                                                        'message'=> 'The category already exists')));

        // Navigation is yes or no
        $metadata->addPropertyConstraint('inNavigation', new Assert\NotBlank(array('message' => 'Please select if the category is in navigation.')));
        $metadata->addPropertyConstraint('inNavigation', new Assert\Choice(array('choices' => array('yes', 'no'),
                                                                                 'message' => 'Invalid value for in navigation.')));

        // Navigation order, if included, must be a number
        $metadata->addPropertyConstraint('navigationOrder', new Assert\Type(array('type'    => 'integer',
                                                                                  'message' => 'Order must be a number.')));
    }

    /**
     * Sanitized getters for navigation
     */
    public function getId()
    {
        return $this->getProductCategoryId();
    }
    public function getName()
    {
        return $this->getProductCategoryName();
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
     * Set productCategoryName
     *
     * @param string $productCategoryName
     * @return ProductCategory
     */
    public function setProductCategoryName($productCategoryName)
    {
        $this->productCategoryName = $productCategoryName;
    
        return $this;
    }

    /**
     * Get productCategoryName
     *
     * @return string 
     */
    public function getProductCategoryName()
    {
        return $this->productCategoryName;
    }
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $productCategorySizes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productCategorySizes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->productCategoryName;
    }
    
    /**
     * Add productCategorySizes
     *
     * @param NiftyThrifty\ShopBundle\Entity\ProductCategorySize $productCategorySizes
     * @return ProductCategory
     */
    public function addProductCategorySize(\NiftyThrifty\ShopBundle\Entity\ProductCategorySize $productCategorySizes)
    {
        $this->productCategorySizes[] = $productCategorySizes;
    
        return $this;
    }

    /**
     * Remove productCategorySizes
     *
     * @param NiftyThrifty\ShopBundle\Entity\ProductCategorySize $productCategorySizes
     */
    public function removeProductCategorySize(\NiftyThrifty\ShopBundle\Entity\ProductCategorySize $productCategorySizes)
    {
        $this->productCategorySizes->removeElement($productCategorySizes);
    }

    /**
     * Get productCategorySizes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProductCategorySizes()
    {
        return $this->productCategorySizes;
    }

    /**
     * Set inNavigation
     *
     * @param  $inNavigation
     * @return ProductCategory
     */
    public function setInNavigation($inNavigation)
    {
        $this->inNavigation = $inNavigation;
    
        return $this;
    }

    /**
     * Get inNavigation
     *
     * @return \tinyint 
     */
    public function getInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * Set navigationOrder
     *
     * @param $navigationOrder
     * @return ProductCategory
     */
    public function setNavigationOrder($navigationOrder)
    {
        $this->navigationOrder = $navigationOrder;
    
        return $this;
    }

    /**
     * Get navigationOrder
     *
     * @return \int 
     */
    public function getNavigationOrder()
    {
        return $this->navigationOrder;
    }
}
