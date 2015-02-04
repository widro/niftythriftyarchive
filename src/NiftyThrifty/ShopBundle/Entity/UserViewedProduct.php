<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * UserViewedProduct
 */
class UserViewedProduct
{
    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $productId;

    /**
     * @var \DateTime
     */
    private $dateViewed;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\Product
     */
    private $product;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\User
     */
    private $user;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Product and user must be populated and unique.
        $metadata->addPropertyConstraint('productId',   new Assert\NotBlank(array('message' => 'Product must be selected.')));
        $metadata->addPropertyConstraint('productId',   new Assert\Regex(array('pattern' => '/\d+/',
                                                                               'message' => 'Invalid product input.')));
        $metadata->addPropertyConstraint('userId',      new Assert\NotBlank(array('message' => 'User must be selected.')));
        $metadata->addPropertyConstraint('userId',      new Assert\Regex(array('pattern' => '/\d+/',
                                                                               'message' => 'Invalid user input.')));
        $metadata->addPropertyConstraint('dateViewed',  new Assert\NotBlank(array('message' => 'Date viewed is required.')));
        $metadata->addPropertyConstraint('dateViewed',  new Assert\DateTime(array('message' => 'Date viewed must be a valid date.')));
        
        // UserId/ProductId is the PK.
        $metadata->addConstraint(new UniqueEntity(array('fields' => array('productId', 'userId'),
                                                        'message'=> 'This Viewed Item already exists.')));
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserViewedProduct
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return UserViewedProduct
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

    /**
     * Set dateViewed
     *
     * @param \DateTime $dateViewed
     * @return UserViewedProduct
     */
    public function setDateViewed($dateViewed)
    {
        $this->dateViewed = $dateViewed;
    
        return $this;
    }

    /**
     * Get dateViewed
     *
     * @return \DateTime 
     */
    public function getDateViewed()
    {
        return $this->dateViewed;
    }

    /**
     * Set product
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Product $product
     * @return UserViewedProduct
     */
    public function setProduct(\NiftyThrifty\ShopBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return \NiftyThrifty\ShopBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set user
     *
     * @param \NiftyThrifty\ShopBundle\Entity\User $user
     * @return UserViewedProduct
     */
    public function setUser(\NiftyThrifty\ShopBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \NiftyThrifty\ShopBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
