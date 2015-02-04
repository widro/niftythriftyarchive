<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ProductTagtype
 */
class ProductTagtype
{
    /**
     * @var integer
     */
    private $productTagtypeId;

    /**
     * @var string
     */
    private $productTagtypeName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $productTags;
    
    // Constant
    const GENERAL       = 1;
    const COLOR         = 2;
    const DECADE        = 3;
    const SUBCATEGORY   = 4;
    const ARCHETYPE     = 5;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name can not be null, must be less than 50 characters, and must be unique.
        $metadata->addPropertyConstraint('productTagtypeName', 
                                         new Assert\NotBlank(array('message' => 'Tag type name can not be blank.')));
        $metadata->addPropertyConstraint('productTagtypeName', 
                                         new Assert\Length(array('max'       => 50,
                                                                 'maxMessage'=> 'Tag type name must be less than 50 characters.')));
        $metadata->addConstraint(new UniqueEntity(array('fields' => 'productTagtypeName',
                                                        'message'=> 'The tag type already exists.')));
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productTags = new \Doctrine\Common\Collections\ArrayCollection();
    }
 
    public function __toString()
    {
        return $this->productTagtypeName;
    }

    /**
     * Sanitized getters for shared templates.
     */
    public function getId()
    {
        return $this->getProductTagtypeId();
    }
    public function getName()
    {
        return $this->getProductTagtypeName();
    }
   
    /**
     * Get productTagtypeId
     *
     * @return integer 
     */
    public function getProductTagtypeId()
    {
        return $this->productTagtypeId;
    }

    /**
     * Set productTagtypeName
     *
     * @param string $productTagtypeName
     * @return ProductTagtype
     */
    public function setProductTagtypeName($productTagtypeName)
    {
        $this->productTagtypeName = $productTagtypeName;
    
        return $this;
    }

    /**
     * Get productTagtypeName
     *
     * @return string 
     */
    public function getProductTagtypeName()
    {
        return $this->productTagtypeName;
    }

    /**
     * Add productTags
     *
     * @param \NiftyThrifty\ShopBundle\Entity\ProductTag $productTags
     * @return ProductTagtype
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
}
