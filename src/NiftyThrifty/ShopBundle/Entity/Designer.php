<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NiftyThrifty\ShopBundle\Entity\Designer
 *
 * @ORM\Table(name="designer")
 * @ORM\Entity
 */
class Designer
{
    /**
     * @var integer $designerId
     *
     * @ORM\Column(name="designer_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $designerId;
    private $id;

    /**
     * @var string $designerName
     *
     * @ORM\Column(name="designer_name", type="string", length=90, nullable=false)
     */
    private $designerName;

    public function __toString()
    {
        return $this->getDesignerName();
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
     * Set designerName
     *
     * @param string $designerName
     * @return Designer
     */
    public function setDesignerName($designerName)
    {
        $this->designerName = $designerName;
    
        return $this;
    }

    /**
     * Get designerName
     *
     * @return string 
     */
    public function getDesignerName()
    {
        return $this->designerName;
    }
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $products;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->getDesignerId();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name can not be null, must be less than 50 characters, and must be unique.
        $metadata->addPropertyConstraint('designerName', new Assert\NotBlank(array('message' => 'Designer name can not be blank.')));
        $metadata->addPropertyConstraint('designerName', new Assert\Length(array('max'       => 50,
                                                                                 'maxMessage'=> 'Designer name must be less than 50 characters.')));
        $metadata->addConstraint(new UniqueEntity(array('fields' => 'designerName',
                                                        'message'=> 'The designer already exists')));
    }
    
    /**
     * Add products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     * @return Designer
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
}
