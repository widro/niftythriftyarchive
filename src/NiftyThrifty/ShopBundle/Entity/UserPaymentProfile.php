<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPaymentProfile
 *
 * @ORM\Table(name="user_payment_profile")
 * @ORM\Entity
 */
class UserPaymentProfile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_payment_profile_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userPaymentProfileId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="card_digits", type="string", length=100, nullable=false)
     */
    private $cardDigits;

    /**
     * @var string
     */
    private $expirationDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="authorize_net_profile_id", type="bigint", nullable=false)
     */
    private $authorizeNetProfileId;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\User
     */
    private $user;
    
    /**
     * This is used for the entity select type in the orderFormType.
     */
    public function __toString()
    {
        return '*-' . $this->cardDigits . ' -- Exp: ' . $this->expirationDate;
    }

    /**
     * Get userPaymentProfileId
     *
     * @return integer 
     */
    public function getUserPaymentProfileId()
    {
        return $this->userPaymentProfileId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserPaymentProfile
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
     * Set cardDigits
     *
     * @param string $cardDigits
     * @return UserPaymentProfile
     */
    public function setCardDigits($cardDigits)
    {
        $this->cardDigits = $cardDigits;
    
        return $this;
    }

    /**
     * Get cardDigits
     *
     * @return string 
     */
    public function getCardDigits()
    {
        return $this->cardDigits;
    }

    /**
     * Set authorizeNetProfileId
     *
     * @param integer $authorizeNetProfileId
     * @return UserPaymentProfile
     */
    public function setAuthorizeNetProfileId($authorizeNetProfileId)
    {
        $this->authorizeNetProfileId = $authorizeNetProfileId;
    
        return $this;
    }

    /**
     * Get authorizeNetProfileId
     *
     * @return integer 
     */
    public function getAuthorizeNetProfileId()
    {
        return $this->authorizeNetProfileId;
    }

    /**
     * Set user
     *
     * @param \NiftyThrifty\ShopBundle\Entity\User $user
     * @return UserPaymentProfile
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
     * Set expirationDate
     *
     * @param string $expirationDate
     * @return UserPaymentProfile
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    
        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return string 
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
}
