<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NiftyThrifty\ShopBundle\Entity\UserInvitation
 *
 * @ORM\Table(name="user_invitation")
 * @ORM\Entity
 */
class UserInvitation
{
    /**
     * @var integer $userInvitationId
     *
     * @ORM\Column(name="user_invitation_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userInvitationId;

    /**
     * @var string $userInvitationLastName
     *
     * @ORM\Column(name="user_invitation_last_name", type="string", length=255, nullable=false)
     */
    private $userInvitationLastName;

    /**
     * @var string $userInvitationFirstName
     *
     * @ORM\Column(name="user_invitation_first_name", type="string", length=255, nullable=false)
     */
    private $userInvitationFirstName;

    /**
     * @var string $userInvitationStatus
     *
     * @ORM\Column(name="user_invitation_status", type="string", nullable=false)
     */
    private $userInvitationStatus;

    /**
     * @var \DateTime $userInvitationDate
     *
     * @ORM\Column(name="user_invitation_date", type="date", nullable=false)
     */
    private $userInvitationDate;

    /**
     * @var string $userInvitationType
     *
     * @ORM\Column(name="user_invitation_type", type="string", nullable=false)
     */
    private $userInvitationType;

    /**
     * @var string $userInvitationContent
     *
     * @ORM\Column(name="user_invitation_content", type="text", nullable=false)
     */
    private $userInvitationContent;

    /**
     * @var string $userInvitationEmail
     *
     * @ORM\Column(name="user_invitation_email", type="string", length=255, nullable=false)
     */
    private $userInvitationEmail;

    /**
     * @var string $userInvitationFbId
     *
     * @ORM\Column(name="user_invitation_fb_id", type="string", length=255, nullable=false)
     */
    private $userInvitationFbId;

    /**
     * @var string $userInvitationTwitterId
     *
     * @ORM\Column(name="user_invitation_twitter_id", type="string", length=255, nullable=false)
     */
    private $userInvitationTwitterId;

    /**
     * @var integer $userInvitationUserId
     *
     * @ORM\Column(name="user_invitation_user_id", type="bigint", nullable=false)
     */
    private $userInvitationUserId;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    const STATUS_PENDING      = 'pending';
    const STATUS_ACCEPTED     = 'accepted';
    const STATUS_SPEND        = 'spend';
    const TYPE_LINK           = 'link';
    const TYPE_TWITTER        = 'twitter';
    const TYPE_FACEBOOK       = 'facebook';
    const TYPE_BOOK_ADDRESS   = 'book_address';
    const TYPE_MAIL           = 'mail';
    const DEFAULT_INVITE_TEXT = "I'm inviting you to join NiftyThrifty, where expert curators deliver rare vintage finds, everyday. Membership is free, so join now! Please click here and use the link to join.";

    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Status can only be one of three values.
        $metadata->addPropertyConstraint('userInvitationStatus', new Assert\NotBlank(array('message' => 'Status may not be blank.')));
        $metadata->addPropertyConstraint('userInvitationStatus', new Assert\Choice(array('choices' => array('pending','accepted','spend'),
                                                                                         'message' => 'Invalid status.')));

        // Date must be set
        $metadata->addPropertyConstraint('userInvitationDate', new Assert\NotBlank(array('message' => 'Date may not be blank.')));
        $metadata->addPropertyConstraint('userInvitationDate', new Assert\Date(array('message' => 'Not a valid date.')));

        // Type must be a valid input value.
        $metadata->addPropertyConstraint('userInvitationType', new Assert\NotBlank(array('message' => 'Type may not be blank')));
        $metadata->addPropertyConstraint('userInvitationType', new Assert\Choice(array('choices' => array('link','twitter','facebook','book_address','mail'),
                                                                                       'message' => 'Invalid type.')));

        // User id must be defined and a number
        $metadata->addPropertyConstraint('userId', new Assert\NotBlank(array('message' => 'Inviting user must be defined.')));

        $metadata->addConstraint(new UniqueEntity(array('fields'    => 'userInvitationEmail',
                                                        'message'   => 'This user has already been invited.')));
        $metadata->addConstraint(new UniqueEntity(array('fields'    => 'userInvitationFbId',
                                                        'message'   => 'This Facebook user has already been invited.')));
        $metadata->addConstraint(new UniqueEntity(array('fields'    => 'userInvitationTwitterId',
                                                        'message'   => 'This Twitter user has already been invited.')));
        /**
         * Callback validations:
         *  - One of email/fbid/twitid must not be null.  If e-mail address is defined, it must be a valid e-mail address
         *  - First/last name may be blank, but if they're not blank, they must be less than 255 chars.
         */
        $metadata->addConstraint(new Assert\Callback(array('methods' => array('validateInvitee', 'validateNames'))));
    }

    /**
     * One of email, facebookid, or twitterId must be not null.  If e-mail address is not null, validate it's an e-mail address.
     */
    public function validateInvitee(ExecutionContextInterface $context)
    {
        if (!$this->userInvitationEmail && !$this->userInvitationFbId && !$this->userInvitationTwitterId) {
            $context->addViolationAt('userInvitationEmail', 'One of e-mail, Facebook id, or Twitter id must be included.');
        }

        /**
         * If the e-mail is included, validate it's in a valid format and that it's not already
         * been added here.  Don't bother checking hostnames.
         */
        if ($this->userInvitationEmail) {
            $context->validateValue($this->userInvitationEmail,
                                    new Assert\Email(array('message' => 'Entered e-mail is not valid.')),
                                    'userInvitationEmail');
            if (!$this->userInvitationContent) {
                $context->addViolationAt('userInvitationContent', 'Message content may not be blank for an e-mail invitation.');
            }
        }
    }

    /**
     * If names are defined, they must be less than 255 characters.
     */
    public function validateNames(ExecutionContextInterface $context)
    {
        if ($this->userInvitationFirstName) {
            if (strlen($this->userInvitationFirstName) > 255) {
                $context->addViolationAt('userInvitationFirstName', 'First name must be less than 255 characters');
            }
        }

        if ($this->userInvitationLastName) {
            if (strlen($this->userInvitationLastName) > 255) {
                $context->addViolationAt('userInvitationLastName', 'Last name must be less than 255 characters');
            }
        }
    }

    /**
     * Given a refering link, pick out the domain.
     *
     * @param   string      A refering URI.
     * @return  string      The domain that is a legal input for this->userInvitationType
     */
    public static function getReferer($uri)
    {
        $uri = strtolower($uri);
        $returnVal = '';

        if (substr_count($uri, 'facebook')) {
            $returnVal = 'facebook';
        } else if (substr_count($uri, 'twitter')) {
            $returnVal = 'twitter';
        } else {
            $returnVal = 'mail';
        }

        return $returnVal;
    }

    /**
     * Get userInvitationId
     *
     * @return integer 
     */
    public function getUserInvitationId()
    {
        return $this->userInvitationId;
    }

    /**
     * Set userInvitationLastName
     *
     * @param string $userInvitationLastName
     * @return UserInvitation
     */
    public function setUserInvitationLastName($userInvitationLastName)
    {
        $this->userInvitationLastName = $userInvitationLastName;
    
        return $this;
    }

    /**
     * Get userInvitationLastName
     *
     * @return string 
     */
    public function getUserInvitationLastName()
    {
        return $this->userInvitationLastName;
    }

    /**
     * Set userInvitationFirstName
     *
     * @param string $userInvitationFirstName
     * @return UserInvitation
     */
    public function setUserInvitationFirstName($userInvitationFirstName)
    {
        $this->userInvitationFirstName = $userInvitationFirstName;
    
        return $this;
    }

    /**
     * Get userInvitationFirstName
     *
     * @return string 
     */
    public function getUserInvitationFirstName()
    {
        return $this->userInvitationFirstName;
    }

    /**
     * Set userInvitationStatus
     *
     * @param string $userInvitationStatus
     * @return UserInvitation
     */
    public function setUserInvitationStatus($userInvitationStatus)
    {
        $this->userInvitationStatus = $userInvitationStatus;
    
        return $this;
    }

    /**
     * Get userInvitationStatus
     *
     * @return string 
     */
    public function getUserInvitationStatus()
    {
        return $this->userInvitationStatus;
    }

    /**
     * Set userInvitationDate
     *
     * @param \DateTime $userInvitationDate
     * @return UserInvitation
     */
    public function setUserInvitationDate($userInvitationDate)
    {
        $this->userInvitationDate = $userInvitationDate;
    
        return $this;
    }

    /**
     * Get userInvitationDate
     *
     * @return \DateTime 
     */
    public function getUserInvitationDate()
    {
        return $this->userInvitationDate;
    }

    /**
     * Set userInvitationType
     *
     * @param string $userInvitationType
     * @return UserInvitation
     */
    public function setUserInvitationType($userInvitationType)
    {
        $this->userInvitationType = $userInvitationType;
    
        return $this;
    }

    /**
     * Get userInvitationType
     *
     * @return string 
     */
    public function getUserInvitationType()
    {
        return $this->userInvitationType;
    }

    /**
     * Set userInvitationContent
     *
     * @param string $userInvitationContent
     * @return UserInvitation
     */
    public function setUserInvitationContent($userInvitationContent)
    {
        $this->userInvitationContent = $userInvitationContent;
    
        return $this;
    }

    /**
     * Get userInvitationContent
     *
     * @return string 
     */
    public function getUserInvitationContent()
    {
        return $this->userInvitationContent;
    }

    /**
     * Set userInvitationEmail
     *
     * @param string $userInvitationEmail
     * @return UserInvitation
     */
    public function setUserInvitationEmail($userInvitationEmail)
    {
        $this->userInvitationEmail = $userInvitationEmail;
    
        return $this;
    }

    /**
     * Get userInvitationEmail
     *
     * @return string 
     */
    public function getUserInvitationEmail()
    {
        return $this->userInvitationEmail;
    }

    /**
     * Set userInvitationFbId
     *
     * @param string $userInvitationFbId
     * @return UserInvitation
     */
    public function setUserInvitationFbId($userInvitationFbId)
    {
        $this->userInvitationFbId = $userInvitationFbId;
    
        return $this;
    }

    /**
     * Get userInvitationFbId
     *
     * @return string 
     */
    public function getUserInvitationFbId()
    {
        return $this->userInvitationFbId;
    }

    /**
     * Set userInvitationTwitterId
     *
     * @param string $userInvitationTwitterId
     * @return UserInvitation
     */
    public function setUserInvitationTwitterId($userInvitationTwitterId)
    {
        $this->userInvitationTwitterId = $userInvitationTwitterId;
    
        return $this;
    }

    /**
     * Get userInvitationTwitterId
     *
     * @return string 
     */
    public function getUserInvitationTwitterId()
    {
        return $this->userInvitationTwitterId;
    }

    /**
     * Set userInvitationUserId
     *
     * @param integer $userInvitationUserId
     * @return UserInvitation
     */
    public function setUserInvitationUserId($userInvitationUserId)
    {
        $this->userInvitationUserId = $userInvitationUserId;
    
        return $this;
    }

    /**
     * Get userInvitationUserId
     *
     * @return integer 
     */
    public function getUserInvitationUserId()
    {
        return $this->userInvitationUserId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserInvitation
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
     * @var \NiftyThrifty\ShopBundle\Entity\User
     */
    private $invitingUser;


    /**
     * Set invitingUser
     *
     * @param \NiftyThrifty\ShopBundle\Entity\User $invitingUser
     * @return UserInvitation
     */
    public function setInvitingUser(\NiftyThrifty\ShopBundle\Entity\User $invitingUser = null)
    {
        $this->invitingUser = $invitingUser;
    
        return $this;
    }

    /**
     * Get invitingUser
     *
     * @return \NiftyThrifty\ShopBundle\Entity\User 
     */
    public function getInvitingUser()
    {
        return $this->invitingUser;
    }
}