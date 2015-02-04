<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XProductTag
 */
class XProductTag
{
    /**
     * @var integer
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var integer
     */
    private $productTagId;

    /**
     * @var integer
     */
    private $productId;


    /**
     * Set productTagId
     *
     * @param integer $productTagId
     * @return XProductTag
     */
    public function setProductTagId($productTagId)
    {
        $this->productTagId = $productTagId;
    
        return $this;
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
     * Set productId
     *
     * @param integer $productId
     * @return XProductTag
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    
        return $this;
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
}