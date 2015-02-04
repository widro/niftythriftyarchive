<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NiftyThrifty\ShopBundle\Entity\UserCredits
 *
 * @ORM\Table(name="user_credits")
 * @ORM\Entity
 */
class UserCredits
{
    /**
     * @var integer $userCreditsId
     *
     * @ORM\Column(name="user_credits_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userCreditsId;

    /**
     * @var \DateTime $userCreditsDate
     *
     * @ORM\Column(name="user_credits_date", type="date", nullable=false)
     */
    private $userCreditsDate;

    /**
     * @var \DateTime $userCreditsDateEnd
     *
     * @ORM\Column(name="user_credits_date_end", type="date", nullable=false)
     */
    private $userCreditsDateEnd;

    /**
     * @var integer $userCreditsValue
     *
     * @ORM\Column(name="user_credits_value", type="integer", nullable=false)
     */
    private $userCreditsValue;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;
    
    const FIRST_PURCHASE_CREDITS = 25;

    /**
     * Given a userid and a number, set everything to make a valid negative credit object.
     *
     * @return UserCredits
     */
    public function setNegativeCredits($user, $value)
    {
        $nowTime = new \DateTime();

        $this->setUserId($user->getUserId())
             ->setUserCreditsDate($nowTime)
             ->setUserCreditsDateEnd($nowTime)
             ->setUserCreditsValue($value*-1);

        return $this;
    }
    
    /**
     * Given a user, set their first buy credits
     *
     * @return UserCredits
     */
    public function setFirstBuyCredits($user)
    {
        $nowTime = new \DateTime();
        $expireTime = new \DateTime("+6 months");
        
        $this->setUserId($user->getUserId())
             ->setUserCreditsDate($nowTime)
             ->setUserCreditsDateEnd($expireTime)
             ->setUserCreditsValue(self::FIRST_PURCHASE_CREDITS);
        return $this;
    }

    /**
     * Get userCreditsId
     *
     * @return integer 
     */
    public function getUserCreditsId()
    {
        return $this->userCreditsId;
    }

    /**
     * Set userCreditsDate
     *
     * @param \DateTime $userCreditsDate
     * @return UserCredits
     */
    public function setUserCreditsDate($userCreditsDate)
    {
        $this->userCreditsDate = $userCreditsDate;
    
        return $this;
    }

    /**
     * Get userCreditsDate
     *
     * @return \DateTime 
     */
    public function getUserCreditsDate()
    {
        return $this->userCreditsDate;
    }

    /**
     * Set userCreditsDateEnd
     *
     * @param \DateTime $userCreditsDateEnd
     * @return UserCredits
     */
    public function setUserCreditsDateEnd($userCreditsDateEnd)
    {
        $this->userCreditsDateEnd = $userCreditsDateEnd;
    
        return $this;
    }

    /**
     * Get userCreditsDateEnd
     *
     * @return \DateTime 
     */
    public function getUserCreditsDateEnd()
    {
        return $this->userCreditsDateEnd;
    }

    /**
     * Set userCreditsValue
     *
     * @param integer $userCreditsValue
     * @return UserCredits
     */
    public function setUserCreditsValue($userCreditsValue)
    {
        $this->userCreditsValue = $userCreditsValue;
    
        return $this;
    }

    /**
     * Get userCreditsValue
     *
     * @return integer 
     */
    public function getUserCreditsValue()
    {
        return $this->userCreditsValue;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserCredits
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
}
