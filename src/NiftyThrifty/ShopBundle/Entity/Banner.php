<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use NiftyThrifty\ShopBundle\Entity\FileUploadEntity;

/**
 * Banner
 */
class Banner extends FileUploadEntity
{
    /**
     * @var integer
     */
    private $bannerId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $bannerImage;

    /**
     * @var string
     */
    private $bannerType;

    /**
     * @var \DateTime
     */
    private $rotationStartTime;

    /**
     * @var \DateTime
     */
    private $rotationEndTime;

    /**
     * @var \NiftyThrifty\ShopBundle\Entity\BannerType
     */
    private $bannerTypeEntity;

    /**
     * @var string
     */
    private $isDefault;


    // Metadata validations
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Description must be populated and less than 50 characters
        $metadata->addPropertyConstraint('description', new Assert\NotBlank(array('message' => 'Description can not be blank.')));
        $metadata->addPropertyConstraint('description', new Assert\Length(array('max'        => 50,
                                                                                'maxMessage' => 'Description must be less than 50 characters.')));

        // URL is not required, but check that it's a valid URL
        $metadata->addPropertyConstraint('url', new Assert\Url(array('message' => 'url is not valid.')));
        $metadata->addPropertyConstraint('url', new Assert\Length(array('max'        => 255,
                                                                        'maxMessage' => 'Url must be less than 255 characters.')));
        
        // Banner type is required
        $metadata->addPropertyConstraint('bannerType', new Assert\Length(array('max'        => 255,
                                                                               'maxMessage' => 'Banner type must be less than 255 characters.')));
        $metadata->addPropertyConstraint('bannerType', new Assert\Choice(array('choices' => array('home_upper_right'),
                                                                               'message' => 'Banner type not selected.')));        

        // Banner image must be an image
        $metadata->addPropertyConstraint('bannerImage', new Assert\Image(array('mimeTypesMessage' => 'Banner image is not an image file.'))); 

        // Dates must be chosen
        $metadata->addPropertyConstraint('rotationStartTime',   new Assert\NotBlank(array('message' => 'Start time must be selected.')));
        $metadata->addPropertyConstraint('rotationStartTime',   new Assert\DateTime(array('message' => 'Start time is not a valid date.')));
        $metadata->addPropertyConstraint('rotationEndTime',     new Assert\NotBlank(array('message' => 'End time must be selected.')));
        $metadata->addPropertyConstraint('rotationEndTime',     new Assert\DateTime(array('message' => 'End time is not a valid date.')));
        
        // Default is either yes or no
        $metadata->addPropertyConstraint('isDefault', new Assert\NotBlank(array('message' => 'Default status can not be blank.')));
        $metadata->addPropertyConstraint('isDefault', new Assert\Choice(array('choices' => array('yes', 'no'),
                                                                              'message' => 'Default status must be selected.')));
    }

    /**
     * Get bannerId
     *
     * @return integer 
     */
    public function getBannerId()
    {
        return $this->bannerId;
    }
    
    public function getId()
    {
        return $this->getBannerId();
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Banner
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Banner
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set bannerImage
     *
     * @param string $bannerImage
     * @return Banner
     */
    public function setBannerImage($bannerImage)
    {
        if ($this->bannerImage) {
            // Only append the web directory if the given image path is a relative path.
            $imagePath = substr($this->bannerImage, 0, 1) == '/' ? $this->bannerImage : $this->getWebDirectory() . $this->bannerImage;
            $this->oldBannerImage = new File($imagePath);
        }
        $this->bannerImage = $bannerImage;

        return $this;
    }

    /**
     * Get bannerImage
     *
     * @return string 
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * Set bannerType
     *
     * @param string $bannerType
     * @return Banner
     */
    public function setBannerType($bannerType)
    {
        $this->bannerType = $bannerType;
    
        return $this;
    }

    /**
     * Get bannerType
     *
     * @return string 
     */
    public function getBannerType()
    {
        return $this->bannerType;
    }

    /**
     * Set isDefault
     *
     * @param string $isDefault
     * @return Banner
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    
        return $this;
    }

    /**
     * Get isDefault
     *
     * @return string 
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set rotationStartTime
     *
     * @param \DateTime $rotationStartTime
     * @return Banner
     */
    public function setRotationStartTime($rotationStartTime)
    {
        $this->rotationStartTime = $rotationStartTime;
    
        return $this;
    }

    /**
     * Get rotationStartTime
     *
     * @return \DateTime 
     */
    public function getRotationStartTime()
    {
        return $this->rotationStartTime;
    }

    /**
     * Set rotationEndTime
     *
     * @param \DateTime $rotationEndTime
     * @return Banner
     */
    public function setRotationEndTime($rotationEndTime)
    {
        $this->rotationEndTime = $rotationEndTime;
    
        return $this;
    }

    /**
     * Get rotationEndTime
     *
     * @return \DateTime 
     */
    public function getRotationEndTime()
    {
        return $this->rotationEndTime;
    }

    /**
     * Set bannerTypeEntity
     *
     * @param \NiftyThrifty\ShopBundle\Entity\BannerType $bannerTypeEntity
     * @return Banner
     */
    public function setBannerTypeEntity(\NiftyThrifty\ShopBundle\Entity\BannerType $bannerTypeEntity = null)
    {
        $this->bannerTypeEntity = $bannerTypeEntity;
    
        return $this;
    }

    /**
     * Get bannerTypeEntity
     *
     * @return \NiftyThrifty\ShopBundle\Entity\BannerType 
     */
    public function getBannerTypeEntity()
    {
        return $this->bannerTypeEntity;
    }

    /**
     * @ORM\PrePersist
     */
    public function processImages()
    {
        if ($this->bannerImage instanceof File) {
            $filename = $this->getEncodedFilename($this->bannerImage);
            $this->fileBannerImage = $this->bannerImage;
            $this->tempBannerImage = $this->bannerImage;
            $this->bannerImage     = $this->getUploadDirectory()
                                       . $filename
                                       . '.'
                                       . $this->fileBannerImage->guessExtension();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function checkImages()
    {
        $this->processImages();
    }

    /**
     * @ORM\PostPersist
     */
    public function upload()
    {
        if ($this->fileBannerImage instanceof File) {
            $this->fileBannerImage->move($this->getUploadRootDirectory(), $this->bannerImage);
            if (file_exists($this->tempBannerImage)) unlink($this->tempBannerImage);
        }
    }

    /**
     * @ORM\PostUpdate
     */
    public function checkUpload()
    {
        $this->upload();
        if ($this->oldBannerImage && $this->oldBannerImage != $this->getWebDirectory() . $this->bannerImage) {
            unlink($this->oldBannerImage);
        }
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteFile()
    {
        if ($this->bannerImage) unlink($this->getWebDirectory() . $this->bannerImage);
    }
}
