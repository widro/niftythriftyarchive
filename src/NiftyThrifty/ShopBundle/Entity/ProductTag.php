<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ProductTag
 */
class ProductTag
{
    /**
     * @var integer
     */
    private $productTagId;

    /**
     * @var string
     */
    private $productTagName;

    /**
     * @var string
     */
    private $productTagSlug;

    /**
     * @var integer
     */
    private $productTagtypeId;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\ProductTagtype
     */
    private $productTagtype;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name can not be null, must be less than 50 characters, and must be unique.
        $metadata->addPropertyConstraint('productTagName',
                                         new Assert\NotBlank(array('message' => 'Tag name can not be blank.')));
        $metadata->addPropertyConstraint('productTagName',
                                         new Assert\Length(array('max'       => 50,
                                                                 'maxMessage'=> 'Tag name must be less than 50 characters.')));
        $metadata->addConstraint(new UniqueEntity(array('fields' => array('productTagName', 'productTagtypeId'),
                                                        'message'=> 'Tag already exists for this tag type.')));

        // Slug must be not null, less than 50 characters
        $metadata->addPropertyConstraint('productTagSlug',
                                         new Assert\NotBlank(array('message' => 'Tag slug can not be blank.')));
        $metadata->addPropertyConstraint('productTagSlug',
                                         new Assert\Length(array('max'       => 50,
                                                                 'maxMessage'=> 'Tag slug must be less than 50 characters.')));
        // Tagtype must be defined, must be a number.
        $metadata->addPropertyConstraint('productTagtypeId',
                                         new Assert\NotBlank(array('message' => 'Please select tag type.')));
    }

    /**
     * Sanitized getters for shared templates.
     */
    public function getId()
    {
        return $this->getProductTagId();
    }
    public function getName()
    {
        return $this->getProductTagName();
    }

    public function __toString()
    {
        return $this->getProductTagName();
    }

    /**
     * Get productTagId
     *
     * @return integer 
     */
    public function getProductTagId()
    {
        return $this->productTagId;
    }

    /**
     * Set productTagName
     *
     * @param string $productTagName
     * @return ProductTag
     */
    public function setProductTagName($productTagName)
    {
        $this->productTagName = $productTagName;
    
        return $this;
    }

    /**
     * Get productTagName
     *
     * @return string 
     */
    public function getProductTagName()
    {
        return $this->productTagName;
    }

    /**
     * Set productTagSlug
     *
     * @param string $productTagSlug
     * @return ProductTag
     */
    public function setProductTagSlug($productTagSlug)
    {
        $this->productTagSlug = $productTagSlug;
    
        return $this;
    }

    /**
     * Get productTagSlug
     *
     * @return string 
     */
    public function getProductTagSlug()
    {
        return $this->productTagSlug;
    }

    /**
     * Set productTagtypeId
     *
     * @param integer $productTagtypeId
     * @return ProductTag
     */
    public function setProductTagtypeId($productTagtypeId)
    {
        $this->productTagtypeId = $productTagtypeId;
    
        return $this;
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
     * Set productTagtype
     *
     * @param \NiftyThrifty\ShopBundle\Entity\ProductTagtype $productTagtype
     * @return ProductTag
     */
    public function setProductTagtype(\NiftyThrifty\ShopBundle\Entity\ProductTagtype $productTagtype = null)
    {
        $this->productTagtype = $productTagtype;
        $this->productTagtypeId = $productTagtype->getProductTagtypeId();
    
        return $this;
    }

    /**
     * Get productTagtype
     *
     * @return \NiftyThrifty\ShopBundle\Entity\ProductTagtype 
     */
    public function getProductTagtype()
    {
        return $this->productTagtype;
    }
}
