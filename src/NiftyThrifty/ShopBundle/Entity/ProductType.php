<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NiftyThrifty\ShopBundle\Entity\ProductType
 *
 * @ORM\Table(name="product_type")
 * @ORM\Entity
 */
class ProductType
{
    /**
     * @var integer $productTypeId
     *
     * @ORM\Column(name="product_type_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productTypeId;

    /**
     * @var string $productTypeName
     *
     * @ORM\Column(name="product_type_name", type="string", length=255, nullable=false)
     */
    private $productTypeName;



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
     * Set productTypeName
     *
     * @param string $productTypeName
     * @return ProductType
     */
    public function setProductTypeName($productTypeName)
    {
        $this->productTypeName = $productTypeName;
    
        return $this;
    }

    /**
     * Get productTypeName
     *
     * @return string 
     */
    public function getProductTypeName()
    {
        return $this->productTypeName;
    }
}