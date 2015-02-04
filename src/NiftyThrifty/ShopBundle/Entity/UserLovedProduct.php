<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * UserLovedProduct
 */
class UserLovedProduct
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
     * @var string
     */
    private $loveType;

    /**
     * @var integer
     */
    private $isDeleted;

    /**
     * @var \DateTime
     */
    private $dateLoved;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\Product
     */
    private $product;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\User
     */
    private $user;
    
    /**
     * loveType is an enum('basket','link') in the db.
     */
    const LOVE_TYPE_BASKET  = 'basket';
    const LOVE_TYPE_LINK    = 'link';

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Love type is one of two values
        $metadata->addPropertyConstraint('loveType',
                                         new Assert\NotBlank(array('message' => 'Love type can not be blank.')));
        $metadata->addPropertyConstraint('loveType',
                                         new Assert\Choice(array('message' => 'Choose a valid love type.',
                                                                 'choices' => array(self::LOVE_TYPE_BASKET,
                                                                                    self::LOVE_TYPE_LINK))));
        // Is deleted is 1/0 (blank is valid, the database default is 0).
        $metadata->addPropertyConstraint('isDeleted',
                                         new Assert\Choice(array('message' => 'Invalid is deleted value.',
                                                                 'choices' => array('0','1'))));

        // Product and user must be populated and unique.
        $metadata->addPropertyConstraint('productId',   new Assert\NotBlank(array('message' => 'Product must be selected.')));
        $metadata->addPropertyConstraint('productId',   new Assert\Regex(array('pattern' => '/\d+/',
                                                                               'message' => 'Invalid product input.')));
        $metadata->addPropertyConstraint('userId',      new Assert\NotBlank(array('message' => 'User must be selected.')));
        $metadata->addPropertyConstraint('userId',      new Assert\Regex(array('pattern' => '/\d+/',
                                                                               'message' => 'Invalid user input.')));
        $metadata->addPropertyConstraint('dateLoved',   new Assert\NotBlank(array('message' => 'Date loved is required.')));
        $metadata->addPropertyConstraint('dateLoved',   new Assert\DateTime(array('message' => 'Date loved is an invalid date.')));

        // UserId/ProductId is the PK.
        $metadata->addConstraint(new UniqueEntity(array('fields' => array('productId', 'userId'),
                                                        'message'=> 'This Loved Item already exists.')));
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserLovedProduct
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
     * @return UserLovedProduct
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
     * Set loveType
     *
     * @param string $loveType
     * @return UserLovedProduct
     */
    public function setLoveType($loveType)
    {
        $this->loveType = $loveType;
    
        return $this;
    }

    /**
     * Get loveType
     *
     * @return string 
     */
    public function getLoveType()
    {
        return $this->loveType;
    }

    /**
     * Set isDeleted
     *
     * @param integer $isDeleted
     * @return UserLovedProduct
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    
        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return integer 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set product
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Product $product
     * @return UserLovedProduct
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
     * @return UserLovedProduct
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

    /**
     * Set dateLoved
     *
     * @param \DateTime $dateLoved
     * @return UserLovedProduct
     */
    public function setDateLoved($dateLoved)
    {
        $this->dateLoved = $dateLoved;
    
        return $this;
    }

    /**
     * Get dateLoved
     *
     * @return \DateTime 
     */
    public function getDateLoved()
    {
        return $this->dateLoved;
    }
}