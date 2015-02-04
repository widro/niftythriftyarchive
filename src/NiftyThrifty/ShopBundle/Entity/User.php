<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NiftyThrifty\ShopBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * @var string $userFirstName
     *
     * @ORM\Column(name="user_first_name", type="string", length=100, nullable=false)
     */
    private $userFirstName;

    /**
     * @var string $userLastName
     *
     * @ORM\Column(name="user_last_name", type="string", length=100, nullable=false)
     */
    private $userLastName;

    /**
     * @var string $userEmail
     *
     * @ORM\Column(name="user_email", type="string", length=100, nullable=false)
     */
    private $userEmail;

    /**
     * @var string $userPassword
     *
     * @ORM\Column(name="user_password", type="string", length=255, nullable=false)
     */
    private $userPassword;

    /**
     * @var \DateTime $userDateCreation
     *
     * @ORM\Column(name="user_date_creation", type="date", nullable=false)
     */
    private $userDateCreation;

    /**
     * @var \DateTime $userDateLastConnection
     *
     * @ORM\Column(name="user_date_last_connection", type="datetime", nullable=false)
     */
    private $userDateLastConnection;

    /**
     * @var string $userInstagramId
     *
     * @ORM\Column(name="user_instagram_id", type="string", length=50, nullable=true)
     */
    private $userInstagramId;

    /**
     * @var string $userInstagramAccessToken
     *
     * @ORM\Column(name="user_instagram_access_token", type="string", length=255, nullable=true)
     */
    private $userInstagramAccessToken;

    /**
     * @var string $userFbId
     *
     * @ORM\Column(name="user_fb_id", type="string", length=255, nullable=true)
     */
    private $userFbId;

    /**
     * @var string $userActive
     *
     * @ORM\Column(name="user_active", type="string", nullable=false)
     */
    private $userActive;

    /**
     * @var integer $addressIdShipping
     *
     * @ORM\Column(name="address_id_shipping", type="bigint", nullable=true)
     */
    private $addressIdShipping;

    /**
     * @var integer $addressIdBilling
     *
     * @ORM\Column(name="address_id_billing", type="bigint", nullable=true)
     */
    private $addressIdBilling;

    /**
     * @var string $userAdmin
     *
     * @ORM\Column(name="user_admin", type="string", nullable=false)
     */
    private $userAdmin;
    
    /**
     * @var bigint $authorizeNetCustomerId
     *
     * @ORM\Column(name="authorize_net_customer_id", type="bigint", nullable=true)
     */
    private $authorizeNetCustomerId;

    /**
     * @var NiftyThrifty\ShopBundle\Entity\Basket
     */
    private $basket;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $baskets;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * Used in password updates
     */
    private $currentPassword;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\Address
     */
    private $addressShipping;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\Address
     */
    private $addressBilling;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $addresses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $invoices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lovedProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userInvitations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userPaymentProfiles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userLovedProducts;
    
    private $lovedProductIds;

    // These fields are required to comply with the UserInterface interface, but they translate
    // to pre-existing fields so they aren't required
    //   private $username;  $this->userEmail
    //   private $password;  $this->userPassword
    //   private $email;     $this->userEmail

    public function setCurrentPassword($val)
    {
        $this->currentPassword = $val;
        return $this;
    }
    
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }
    
    public function __construct()
    {
        $this->isActive         = true;
        $this->salt             = null;
        $this->baskets          = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lovedProductIds  = null;
    }
    
    public function getLovedProductIds()
    {
        if (!is_array($this->lovedProductIds)) {
            $this->lovedProductIds = array();
            $loves = $this->getUserLovedProducts();
            foreach ($loves as $love) {
                if (!$love->getIsDeleted()) $this->lovedProductIds[] = $love->getProductId();
            }
        }
        
        return $this->lovedProductIds;
    }
    
    public function isLoved($productId)
    {
        $loves = $this->getLovedProductIds();
        
        return (in_array($productId, $loves));
    }
    
    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // First name can not be blank and must be less than 60 characters
        $metadata->addPropertyConstraint('userFirstName', 
                                            new Assert\NotBlank(array('message' => 'First name can not be blank.',
                                                                      'groups'  => array('accountInfo'))));
        $metadata->addPropertyConstraint('userFirstName', 
                                            new Assert\Length(array('max'        => 60,
                                                                    'maxMessage' => 'First name must be less than 60 characters.',
                                                                    'groups'     => array('accountInfo'))));

        // Last name can not be blank and must be less than 60 characters
        $metadata->addPropertyConstraint('userLastName',  
                                            new Assert\NotBlank(array('message' => 'Last name can not be blank.',
                                                                      'groups'  => array('accountInfo'))));
        $metadata->addPropertyConstraint('userLastName',  
                                            new Assert\Length(array('max' => 60,
                                                                    'maxMessage' => 'Last name must be less than 60 characters.',
                                                                    'groups' => array('accountInfo'))));

        // E-mail must be validated via symfony's e-mail validator
        $metadata->addPropertyConstraint('userEmail', new Assert\NotBlank(array('message' => 'E-mail address can not be blank.',
                                                                                'groups'  => array('accountInfo'))));
        $metadata->addPropertyConstraint('userEmail',
                                            new Assert\Email(array('message'    => 'E-mail address must be valid.',
                                                                   'checkMX'    => true,
                                                                   'groups'     => array('accountInfo'),
                                                                   'checkHost'  => true)));
        $metadata->addConstraint(new UniqueEntity(array('fields'  => 'userEmail',
                                                        'message' => 'The entered e-mail address already exists.',
                                                        'groups'  => array('accountInfo'))));

        // Password must not be blank
        $metadata->addPropertyConstraint('userPassword',
                                            new Assert\NotBlank(array('message' => 'Password may not be blank.',
                                                                      'groups'  => array('passwordCheck'))));
        $metadata->addPropertyConstraint('userPassword',  
                                            new Assert\Length(array('max'       => 16,
                                                                    'min'       => 6,
                                                                    'maxMessage'=> 'Password must be fewer than 16 characters.',
                                                                    'minMessage'=> 'Password must be more than 6 characters.',
                                                                    'groups'    => array('passwordCheck'))));
        /**
         * This theoretically should check the user's current password when you're changing it
         * but I couldn't get it to work.  I'm leaving it here to re-examine in the future.
         * $metadata->addPropertyConstraint('currentPassword', 
         *                                     new UserPassword(array('message' => 'Incorrect value for your current password.',
         *                                                            'groups'  => array('passwordCheck'))));
         */
    }
    
    /**
     * This is the token we use to reference that a user was invited by someone else.
     *
     * @return string
     */
    public function getInviteToken()
    {
        $inviteToken = substr(sha1(substr($this->getUserPassword(),5,12)).
                                   substr(sha1($this->getUserEmail()),18,6),
                              23,
                              10);

        return $inviteToken; 
    }
    
    //*********************************************************************
    //The following methods exist for the UserInterface implementation
    
    /**
     * Username on this site is the user's e-mail address.
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getUserEmail();
    }
    
    /**
     * Password here is stored in userPassword
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->getUserPassword();
    }
    
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        // Verify that userAdmin designates admin users for back office, return appropriate roles here.
        if ($this->userAdmin == 'true') {
            $roleArray = array('ROLE_ADMIN');
        } else {
            $roleArray = array('ROLE_USER');
        }

        return $roleArray;
    }
    
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
    
    // End User Interface methods
    //*********************************************************************

    /**
     * Before inserting a user, set their creation time to now and set them to active.
     * @ORM\PrePersist
     */
    public function setCreationTime()
    {
        $this->userDateCreation         = new \DateTime("now");
        $this->userDateLastConnection   = new \DateTime("now");
        $this->userActive               = 'true';
        $this->userAdmin                = $this->userAdmin == 'true' ? 'true' : 'false';
    }
    
    //*********************************************************************
    //The following methods exist for the Serializable implementation

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->userId,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->userId,
        ) = unserialize($serialized);
    }

    // End Serializable methods
    //*********************************************************************

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
     * Set userFirstName
     *
     * @param string $userFirstName
     * @return User
     */
    public function setUserFirstName($userFirstName)
    {
        $this->userFirstName = $userFirstName;
    
        return $this;
    }

    /**
     * Get userFirstName
     *
     * @return string 
     */
    public function getUserFirstName()
    {
        return $this->userFirstName;
    }

    /**
     * Set userLastName
     *
     * @param string $userLastName
     * @return User
     */
    public function setUserLastName($userLastName)
    {
        $this->userLastName = $userLastName;
    
        return $this;
    }

    /**
     * Get userLastName
     *
     * @return string 
     */
    public function getUserLastName()
    {
        return $this->userLastName;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     * @return User
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    
        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string 
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set userPassword
     *
     * @param string $userPassword
     * @return User
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;
    
        return $this;
    }

    /**
     * Get userPassword
     *
     * @return string 
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * Set userDateCreation
     *
     * @param \DateTime $userDateCreation
     * @return User
     */
    public function setUserDateCreation($userDateCreation)
    {
        $this->userDateCreation = $userDateCreation;
    
        return $this;
    }

    /**
     * Get userDateCreation
     *
     * @return \DateTime 
     */
    public function getUserDateCreation()
    {
        return $this->userDateCreation;
    }

    /**
     * Set userDateLastConnection
     *
     * @param \DateTime $userDateLastConnection
     * @return User
     */
    public function setUserDateLastConnection($userDateLastConnection)
    {
        $this->userDateLastConnection = $userDateLastConnection;
    
        return $this;
    }

    /**
     * Get userDateLastConnection
     *
     * @return \DateTime 
     */
    public function getUserDateLastConnection()
    {
        return $this->userDateLastConnection;
    }

    /**
     * Set userInstagramId
     *
     * @param string $userInstagramId
     * @return User
     */
    public function setUserInstagramId($userInstagramId)
    {
        $this->userInstagramId = $userInstagramId;
    
        return $this;
    }

    /**
     * Get userInstagramId
     *
     * @return string 
     */
    public function getUserInstagramId()
    {
        return $this->userInstagramId;
    }

    /**
     * Set userInstagramAccessToken
     *
     * @param string $userInstagramAccessToken
     * @return User
     */
    public function setUserInstagramAccessToken($userInstagramAccessToken)
    {
        $this->userInstagramAccessToken = $userInstagramAccessToken;
    
        return $this;
    }

    /**
     * Get userInstagramAccessToken
     *
     * @return string 
     */
    public function getUserInstagramAccessToken()
    {
        return $this->userInstagramAccessToken;
    }

    /**
     * Set userFbId
     *
     * @param string $userFbId
     * @return User
     */
    public function setUserFbId($userFbId)
    {
        $this->userFbId = $userFbId;
    
        return $this;
    }

    /**
     * Get userFbId
     *
     * @return string 
     */
    public function getUserFbId()
    {
        return $this->userFbId;
    }

    /**
     * Set userActive
     *
     * @param string $userActive
     * @return User
     */
    public function setUserActive($userActive)
    {
        $this->userActive = $userActive;
    
        return $this;
    }

    /**
     * Get userActive
     *
     * @return string 
     */
    public function getUserActive()
    {
        return $this->userActive;
    }

    /**
     * Set addressIdShipping
     *
     * @param integer $addressIdShipping
     * @return User
     */
    public function setAddressIdShipping($addressIdShipping)
    {
        $this->addressIdShipping = $addressIdShipping;
    
        return $this;
    }

    /**
     * Get addressIdShipping
     *
     * @return integer 
     */
    public function getAddressIdShipping()
    {
        return $this->addressIdShipping;
    }

    /**
     * Set addressIdBilling
     *
     * @param integer $addressIdBilling
     * @return User
     */
    public function setAddressIdBilling($addressIdBilling)
    {
        $this->addressIdBilling = $addressIdBilling;
    
        return $this;
    }

    /**
     * Get addressIdBilling
     *
     * @return integer 
     */
    public function getAddressIdBilling()
    {
        return $this->addressIdBilling;
    }

    /**
     * Set userAdmin
     *
     * @param string $userAdmin
     * @return User
     */
    public function setUserAdmin($userAdmin)
    {
        $this->userAdmin = $userAdmin;
    
        return $this;
    }

    /**
     * Get userAdmin
     *
     * @return string 
     */
    public function getUserAdmin()
    {
        return $this->userAdmin;
    }

    /**
     * Set authorizeNetCustomerId
     *
     * @param bigint $authorizeNetCustomerId
     * @return User
     */
    public function setAuthorizeNetCustomerId($authorizeNetCustomerId)
    {
        $this->authorizeNetCustomerId = $authorizeNetCustomerId;
    
        return $this;
    }

    /**
     * Get authorizeNetCustomerId
     *
     * @return bigint 
     */
    public function getAuthorizeNetCustomerId()
    {
        return $this->authorizeNetCustomerId;
    }

    /**
     * Set basket
     *
     * @param NiftyThrifty\ShopBundle\Entity\Basket $basket
     * @return User
     */
    public function setBasket(\NiftyThrifty\ShopBundle\Entity\Basket $basket = null)
    {
        $this->basket = $basket;
    
        return $this;
    }

    /**
     * Get basket
     *
     * @return NiftyThrifty\ShopBundle\Entity\Basket 
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Add baskets
     *
     * @param NiftyThrifty\ShopBundle\Entity\Basket $baskets
     * @return User
     */
    public function addBasket(\NiftyThrifty\ShopBundle\Entity\Basket $baskets)
    {
        $this->baskets[] = $baskets;
    
        return $this;
    }

    /**
     * Remove baskets
     *
     * @param NiftyThrifty\ShopBundle\Entity\Basket $baskets
     */
    public function removeBasket(\NiftyThrifty\ShopBundle\Entity\Basket $baskets)
    {
        $this->baskets->removeElement($baskets);
    }

    /**
     * Get baskets
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBaskets()
    {
        return $this->baskets;
    }

    /**
     * Set addressShipping
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Address $addressShipping
     * @return User
     */
    public function setAddressShipping(\NiftyThrifty\ShopBundle\Entity\Address $addressShipping = null)
    {
        $this->addressShipping = $addressShipping;
    
        return $this;
    }

    /**
     * Get addressShipping
     *
     * @return \NiftyThrifty\ShopBundle\Entity\Address 
     */
    public function getAddressShipping()
    {
        return $this->addressShipping;
    }

    /**
     * Set addressBilling
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Address $addressBilling
     * @return User
     */
    public function setAddressBilling(\NiftyThrifty\ShopBundle\Entity\Address $addressBilling = null)
    {
        $this->addressBilling = $addressBilling;
    
        return $this;
    }

    /**
     * Get addressBilling
     *
     * @return \NiftyThrifty\ShopBundle\Entity\Address 
     */
    public function getAddressBilling()
    {
        return $this->addressBilling;
    }

    /**
     * Add addresses
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Address $addresses
     * @return User
     */
    public function addAddresse(\NiftyThrifty\ShopBundle\Entity\Address $addresses)
    {
        $this->addresses[] = $addresses;
    
        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Address $addresses
     */
    public function removeAddresse(\NiftyThrifty\ShopBundle\Entity\Address $addresses)
    {
        $this->addresses->removeElement($addresses);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add invoices
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Invoice $invoices
     * @return User
     */
    public function addInvoice(\NiftyThrifty\ShopBundle\Entity\Invoice $invoices)
    {
        $this->invoices[] = $invoices;
    
        return $this;
    }

    /**
     * Remove invoices
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Invoice $invoices
     */
    public function removeInvoice(\NiftyThrifty\ShopBundle\Entity\Invoice $invoices)
    {
        $this->invoices->removeElement($invoices);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add userPaymentProfiles
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserPaymentProfile $userPaymentProfiles
     * @return User
     */
    public function addUserPaymentProfile(\NiftyThrifty\ShopBundle\Entity\UserPaymentProfile $userPaymentProfiles)
    {
        $this->userPaymentProfiles[] = $userPaymentProfiles;
    
        return $this;
    }

    /**
     * Remove userPaymentProfiles
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserPaymentProfile $userPaymentProfiles
     */
    public function removeUserPaymentProfile(\NiftyThrifty\ShopBundle\Entity\UserPaymentProfile $userPaymentProfiles)
    {
        $this->userPaymentProfiles->removeElement($userPaymentProfiles);
    }

    /**
     * Get userPaymentProfiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserPaymentProfiles()
    {
        return $this->userPaymentProfiles;
    }

    /**
     * Add userInvitations
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserInvitation $userInvitations
     * @return User
     */
    public function addUserInvitation(\NiftyThrifty\ShopBundle\Entity\UserInvitation $userInvitations)
    {
        $this->userInvitations[] = $userInvitations;
    
        return $this;
    }

    /**
     * Remove userInvitations
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserInvitation $userInvitations
     */
    public function removeUserInvitation(\NiftyThrifty\ShopBundle\Entity\UserInvitation $userInvitations)
    {
        $this->userInvitations->removeElement($userInvitations);
    }

    /**
     * Get userInvitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserInvitations()
    {
        return $this->userInvitations;
    }

    /**
     * Add lovedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Product $lovedProducts
     * @return User
     */
    public function addLovedProduct(\NiftyThrifty\ShopBundle\Entity\Product $lovedProducts)
    {
        $this->lovedProducts[] = $lovedProducts;
    
        return $this;
    }

    /**
     * Remove lovedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Product $lovedProducts
     */
    public function removeLovedProduct(\NiftyThrifty\ShopBundle\Entity\Product $lovedProducts)
    {
        $this->lovedProducts->removeElement($lovedProducts);
    }

    /**
     * Get lovedProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLovedProducts()
    {
        return $this->lovedProducts;
    }

    /**
     * Add userLovedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserLovedProduct $userLovedProducts
     * @return User
     */
    public function addUserLovedProduct(\NiftyThrifty\ShopBundle\Entity\UserLovedProduct $userLovedProducts)
    {
        $this->userLovedProducts[] = $userLovedProducts;
    
        return $this;
    }

    /**
     * Remove userLovedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserLovedProduct $userLovedProducts
     */
    public function removeUserLovedProduct(\NiftyThrifty\ShopBundle\Entity\UserLovedProduct $userLovedProducts)
    {
        $this->userLovedProducts->removeElement($userLovedProducts);
    }

    /**
     * Get userLovedProducts
     * The standard generated code here was overrode to respect the logical delete flag.
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserLovedProducts()
    {
        return $this->userLovedProducts->filter(
                    function ($userLovedProduct) {
                        return !$userLovedProduct->getIsDeleted();
                    });
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userViewedProducts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $viewedProducts;


    /**
     * Add userViewedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserViewedProduct $userViewedProducts
     * @return User
     */
    public function addUserViewedProduct(\NiftyThrifty\ShopBundle\Entity\UserViewedProduct $userViewedProducts)
    {
        $this->userViewedProducts[] = $userViewedProducts;
    
        return $this;
    }

    /**
     * Remove userViewedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\UserViewedProduct $userViewedProducts
     */
    public function removeUserViewedProduct(\NiftyThrifty\ShopBundle\Entity\UserViewedProduct $userViewedProducts)
    {
        $this->userViewedProducts->removeElement($userViewedProducts);
    }

    /**
     * Get userViewedProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserViewedProducts()
    {
        return $this->userViewedProducts;
    }

    /**
     * Add viewedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Product $viewedProducts
     * @return User
     */
    public function addViewedProduct(\NiftyThrifty\ShopBundle\Entity\Product $viewedProducts)
    {
        $this->viewedProducts[] = $viewedProducts;
    
        return $this;
    }

    /**
     * Remove viewedProducts
     *
     * @param \NiftyThrifty\ShopBundle\Entity\Product $viewedProducts
     */
    public function removeViewedProduct(\NiftyThrifty\ShopBundle\Entity\Product $viewedProducts)
    {
        $this->viewedProducts->removeElement($viewedProducts);
    }

    /**
     * Get viewedProducts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViewedProducts()
    {
        return $this->viewedProducts;
    }
}
