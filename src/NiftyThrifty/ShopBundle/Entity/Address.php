<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * NiftyThrifty\ShopBundle\Entity\Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity
 */
class Address
{
    const TYPE_SHIPPING = 'shipping';
    const TYPE_BILLING  = 'billing';

    /**
     * @var integer $addressId
     *
     * @ORM\Column(name="address_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $addressId;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     */
    private $userId;

    /**
     * @var string $addressFirstName
     *
     * @ORM\Column(name="address_first_name", type="string", length=64, nullable=false)
     */
    private $addressFirstName;

    /**
     * @var string $addressLastName
     *
     * @ORM\Column(name="address_last_name", type="string", length=64, nullable=false)
     */
    private $addressLastName;

    /**
     * @var string $addressStreet
     *
     * @ORM\Column(name="address_street", type="string", length=255, nullable=false)
     */
    private $addressStreet;

    /**
     * @var string $addressCity
     *
     * @ORM\Column(name="address_city", type="string", length=64, nullable=false)
     */
    private $addressCity;

    /**
     * @var integer $stateId
     *
     * @ORM\Column(name="state_id", type="bigint", nullable=false)
     */
    private $stateId;

    /**
     * @var string $addressZipcode
     *
     * @ORM\Column(name="address_zipcode", type="string", length=20, nullable=false)
     */
    private $addressZipcode;

    /**
     * @var string $addressCountry
     *
     * @ORM\Column(name="address_country", type="string", length=255, nullable=false)
     */
    private $addressCountry;

    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // User can not be blank
        $metadata->addPropertyConstraint('userId',           new Assert\NotBlank(array('message' => 'User can not be blank')));

        // First name can not be blank and must be less than 60 characters
        $metadata->addPropertyConstraint('addressFirstName', new Assert\NotBlank(array('message' => 'First name can not be blank')));
        $metadata->addPropertyConstraint('addressFirstName', new Assert\Length(array('max'    => 60,
                                                                                 'maxMessage' => 'First name must be less than 60 characters')));

        // Last name can not be blank and must be less than 60 characters
        $metadata->addPropertyConstraint('addressLastName',  new Assert\NotBlank(array('message' => 'Last name can not be blank')));
        $metadata->addPropertyConstraint('addressLastName',  new Assert\Length(array('max' => 60,
                                                                                 'maxMessage' => 'Last name must be less than 60 characters')));

        // Street address can not be blank and must be less than 255 characters
        $metadata->addPropertyConstraint('addressStreet',    new Assert\NotBlank(array('message' => 'Street address can not be blank')));
        $metadata->addPropertyConstraint('addressStreet',    new Assert\Length(array('max' => 254,
                                                                            'maxMessage' => 'Street address must be less than 255 characters')));

        // City can not be blank and must be less than 50 characters
        $metadata->addPropertyConstraint('addressCity',      new Assert\NotBlank(array('message' => 'City can not be blank')));
        $metadata->addPropertyConstraint('addressCity',      new Assert\Length(array('max' => 50,
                                                                                     'maxMessage' => 'City must be less than 50 characters')));

        // State can not be blank
        $metadata->addPropertyConstraint('state',            new Assert\NotBlank(array('message' => 'State must be selected')));

        // Zip code can not be blank, must be 5 integers
        $metadata->addPropertyConstraint('addressZipcode',   new Assert\NotBlank(array('message' => 'Zip code can not be blank')));
        $metadata->addPropertyConstraint('addressZipcode',   new Assert\Regex(array('pattern' => '/^\d{5}(-\d{4})?$/',
                                                                                    'message' => 'Zip code must be 5 digits or 9 digits with a hyphen')));

        // Country can not be blank.
        $metadata->addPropertyConstraint('addressCountry',   new Assert\NotBlank(array('message' => 'Country can not be blank')));
    }

    /**
     * Get addressId
     *
     * @return integer 
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Address
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
     * Set addressFirstName
     *
     * @param string $addressFirstName
     * @return Address
     */
    public function setAddressFirstName($addressFirstName)
    {
        $this->addressFirstName = $addressFirstName;
    
        return $this;
    }

    /**
     * Get addressFirstName
     *
     * @return string 
     */
    public function getAddressFirstName()
    {
        return $this->addressFirstName;
    }

    /**
     * Set addressLastName
     *
     * @param string $addressLastName
     * @return Address
     */
    public function setAddressLastName($addressLastName)
    {
        $this->addressLastName = $addressLastName;
    
        return $this;
    }

    /**
     * Get addressLastName
     *
     * @return string 
     */
    public function getAddressLastName()
    {
        return $this->addressLastName;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     * @return Address
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    
        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string 
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     * @return Address
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    
        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string 
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set stateId
     *
     * @param integer $stateId
     * @return Address
     */
    public function setStateId($stateId)
    {
        $this->stateId = $stateId;
    
        return $this;
    }

    /**
     * Get stateId
     *
     * @return integer 
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set addressZipcode
     *
     * @param string $addressZipcode
     * @return Address
     */
    public function setAddressZipcode($addressZipcode)
    {
        $this->addressZipcode = $addressZipcode;
    
        return $this;
    }

    /**
     * Get addressZipcode
     *
     * @return string 
     */
    public function getAddressZipcode()
    {
        return $this->addressZipcode;
    }

    /**
     * Set addressCountry
     *
     * @param string $addressCountry
     * @return Address
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;
    
        return $this;
    }

    /**
     * Get addressCountry
     *
     * @return string 
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }
    /**
     * @var \NiftyThrifty\ShopBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \NiftyThrifty\ShopBundle\Entity\User $user
     * @return Address
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
     * @var \NiftyThrifty\ShopBundle\Entity\State
     */
    private $state;


    /**
     * Set state
     *
     * @param \NiftyThrifty\ShopBundle\Entity\State $state
     * @return Address
     */
    public function setState(\NiftyThrifty\ShopBundle\Entity\State $state = null)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return \NiftyThrifty\ShopBundle\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }
}
