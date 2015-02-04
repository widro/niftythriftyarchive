<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * BannerType
 */
class BannerType
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $banners;
    
    /**
     * Name is the PK for this table and also the only column, so confirm it is populated.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name must be not blank, less than 50 characters, and only certain characters
        $metadata->addPropertyConstraint('name', new Assert\NotBlank(array('message' => 'Name can not be blank.')));
        $metadata->addPropertyConstraint('name', new Assert\Length(array('max'        => 50,
                                                                         'maxMessage' => 'Name must be less than 50 characters.')));
        $metadata->addPropertyConstraint('name', new Assert\Regex(array('pattern' => '/^\w+$/',
                                                                        'message' => 'Name may only contain letters or underscores.')));
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->banners = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->getName();
    }

    public function __toString()
    {
        return $this->getName();
    }
    
    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Add banners
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Banner $banners
     * @return BannerType
     */
    public function addBanner(\NiftyThrifty\ShopBundle\Entity\Banner $banners)
    {
        $this->banners[] = $banners;
    
        return $this;
    }

    /**
     * Remove banners
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Banner $banners
     */
    public function removeBanner(\NiftyThrifty\ShopBundle\Entity\Banner $banners)
    {
        $this->banners->removeElement($banners);
    }

    /**
     * Get banners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBanners()
    {
        return $this->banners;
    }
}
