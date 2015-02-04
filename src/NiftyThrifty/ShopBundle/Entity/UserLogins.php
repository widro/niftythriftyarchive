<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NiftyThrifty\ShopBundle\Entity\UserLogins
 *
 * @ORM\Table(name="user_logins")
 * @ORM\Entity
 */
class UserLogins
{
    /**
     * @var integer $userLoginId
     *
     * @ORM\Column(name="user_login_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userLoginId;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var \DateTime $userLoginDate
     *
     * @ORM\Column(name="user_login_date", type="datetime", nullable=false)
     */
    private $userLoginDate;



    /**
     * Get userLoginId
     *
     * @return integer 
     */
    public function getUserLoginId()
    {
        return $this->userLoginId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserLogins
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
     * Set userLoginDate
     *
     * @param \DateTime $userLoginDate
     * @return UserLogins
     */
    public function setUserLoginDate($userLoginDate)
    {
        $this->userLoginDate = $userLoginDate;
    
        return $this;
    }

    /**
     * Get userLoginDate
     *
     * @return \DateTime 
     */
    public function getUserLoginDate()
    {
        return $this->userLoginDate;
    }
}