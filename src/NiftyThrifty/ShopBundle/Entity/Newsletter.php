<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use NiftyThrifty\ShopBundle\Entity\FileUploadEntity;

/**
 * NiftyThrifty\ShopBundle\Entity\Newsletter
 *
 * @ORM\Table(name="newsletter")
 * @ORM\Entity
 */
class Newsletter extends FileUploadEntity
{
    /**
     * @var integer $newsletterId
     *
     * @ORM\Column(name="newsletter_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $newsletterId;

    /**
     * @var string $newsletterName
     *
     * @ORM\Column(name="newsletter_name", type="string", length=64, nullable=false)
     */
    private $newsletterName;

    /**
     * @var string $newsletterTitle
     *
     * @ORM\Column(name="newsletter_title", type="string", length=255, nullable=false)
     */
    private $newsletterTitle;

    /**
     * @var string $newsletterLink
     *
     * @ORM\Column(name="newsletter_link", type="string", length=255, nullable=false)
     */
    private $newsletterLink;

    /**
     * @var string $newsletterCollectionImg
     *
     * @ORM\Column(name="newsletter_collection_img", type="string", length=255, nullable=false)
     */
    private $newsletterCollectionImg;

    /**
     * @var string $newsletterProduct1Img
     *
     * @ORM\Column(name="newsletter_product1_img", type="string", length=255, nullable=true)
     */
    private $newsletterProduct1Img;

    /**
     * @var string $newsletterProduct1Link
     *
     * @ORM\Column(name="newsletter_product1_link", type="string", length=255, nullable=true)
     */
    private $newsletterProduct1Link;

    /**
     * @var string $newsletterProduct2Img
     *
     * @ORM\Column(name="newsletter_product2_img", type="string", length=255, nullable=true)
     */
    private $newsletterProduct2Img;

    /**
     * @var string $newsletterProduct2Link
     *
     * @ORM\Column(name="newsletter_product2_link", type="string", length=255, nullable=true)
     */
    private $newsletterProduct2Link;

    /**
     * @var integer $newsletterBlastId
     *
     * @ORM\Column(name="newsletter_blast_id", type="bigint", nullable=true)
     */
    private $newsletterBlastId;

    /**
     * @var \DateTime $newsletterBlastScheduleTime
     *
     * @ORM\Column(name="newsletter_blast_schedule_time", type="datetime", nullable=true)
     */
    private $newsletterBlastScheduleTime;

    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Name required
        $metadata->addPropertyConstraint('newsletterName', new Assert\NotBlank(array('message' => 'Newsletter name can not be blank.')));
        $metadata->addPropertyConstraint('newsletterName', new Assert\Length(array('max'        => 64,
                                                                                   'maxMessage' => 'Newsletter name must be less than 64 characters.')));
        
        // Description required
        $metadata->addPropertyConstraint('newsletterTitle', new Assert\NotBlank(array('message' => 'Newsletter title can not be blank.')));
        $metadata->addPropertyConstraint('newsletterTitle', new Assert\Length(array('max'        => 64,
                                                                                    'maxMessage' => 'Newsletter title must be less than 64 characters.')));

        // Newsletter image is not blank and is an image file
        $metadata->addPropertyConstraint('newsletterCollectionImg', 
                                         new Assert\Image(array('mimeTypesMessage' => 'Newsletter image is not an image file.')));

        // Newsletter Link is not blank and a valid url.
        $metadata->addPropertyConstraint('newsletterLink', new Assert\NotBlank(array('message' => 'Newsletter url can not be blank.')));
        $metadata->addPropertyConstraint('newsletterLink', new Assert\Url(array('message' => 'Newsletter link must be a valid URL.')));

        // If defined, must be image files.
        $metadata->addPropertyConstraint('newsletterProduct1Img', 
                                         new Assert\Image(array('mimeTypesMessage' => 'Product 1 image is not an image file.')));
        $metadata->addPropertyConstraint('newsletterProduct2Img', 
                                         new Assert\Image(array('mimeTypesMessage' => 'Product 2 image is not an image file.')));

        // Link fields, if defined, must be proper URLs
        $metadata->addPropertyConstraint('newsletterProduct1Link',  new Assert\Url(array('message' => 'Product link 1 must be a valid URL.')));
        $metadata->addPropertyConstraint('newsletterProduct2Link',  new Assert\Url(array('message' => 'Product link 2 must be a valid URL.')));
        
        // If blast time is defined, it must be a datetime
        $metadata->addPropertyConstraint('newsletterBlastScheduleTime', 
                                         new Assert\DateTime(array('message' => 'Schedule time must be a valid date/time.')));
    }

    /**
     * Get newsletterId
     *
     * @return integer
     */
    public function getNewsletterId()
    {
        return $this->newsletterId;
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->newsletterId;
    }

    /**
     * Set newsletterName
     *
     * @param string $newsletterName
     * @return Newsletter
     */
    public function setNewsletterName($newsletterName)
    {
        $this->newsletterName = $newsletterName;

        return $this;
    }

    /**
     * Get newsletterName
     *
     * @return string
     */
    public function getNewsletterName()
    {
        return $this->newsletterName;
    }

    /**
     * Set newsletterTitle
     *
     * @param string $newsletterTitle
     * @return Newsletter
     */
    public function setNewsletterTitle($newsletterTitle)
    {
        $this->newsletterTitle = $newsletterTitle;

        return $this;
    }

    /**
     * Get newsletterTitle
     *
     * @return string
     */
    public function getNewsletterTitle()
    {
        return $this->newsletterTitle;
    }


    /**
     * Set newsletterLink
     *
     * @param string $newsletterLink
     * @return Newsletter
     */
    public function setNewsletterLink($newsletterLink)
    {
        $this->newsletterLink = $newsletterLink;

        return $this;
    }

    /**
     * Get newsletterLink
     *
     * @return string
     */
    public function getNewsletterLink()
    {
        return $this->newsletterLink;
    }

    /**
     * Set newsletterCollectionImg
     *
     * @param string $newsletterCollectionImg
     * @return Newsletter
     */
    public function setNewsletterCollectionImg($newsletterCollectionImg)
    {
        if ($this->newsletterCollectionImg) {
            // Only append the web directory if the given image path is a relative path.
            $imagePath = substr($this->newsletterCollectionImg, 0, 1) == '/' ? $this->newsletterCollectionImg : $this->getWebDirectory() . $this->newsletterCollectionImg;
            $this->oldCollectionImg = new File($imagePath);
        }
        $this->newsletterCollectionImg = $newsletterCollectionImg;

        return $this;
    }

    /**
     * Get newsletterCollectionImg
     *
     * @return string
     */
    public function getNewsletterCollectionImg()
    {
        return $this->newsletterCollectionImg;
    }

    /**
     * Set newsletterProduct1Img
     *
     * @param string $newsletterProduct1Img
     * @return Newsletter
     */
    public function setNewsletterProduct1Img($newsletterProduct1Img)
    {
        if ($this->newsletterProduct1Img) {
            // Only append the web directory if the given image path is a relative path.
            $imagePath = substr($this->newsletterProduct1Img, 0, 1) == '/' ? $this->newsletterProduct1Img : $this->getWebDirectory() . $this->newsletterProduct1Img;
            $this->oldProduct1Img = new File($imagePath);
        }
        $this->newsletterProduct1Img = $newsletterProduct1Img;

        return $this;
    }

    /**
     * Get newsletterProduct1Img
     *
     * @return string
     */
    public function getNewsletterProduct1Img()
    {
        return $this->newsletterProduct1Img;
    }

    /**
     * Set newsletterProduct1Link
     *
     * @param string $newsletterProduct1Link
     * @return Newsletter
     */
    public function setNewsletterProduct1Link($newsletterProduct1Link)
    {
        $this->newsletterProduct1Link = $newsletterProduct1Link;

        return $this;
    }

    /**
     * Get newsletterProduct1Link
     *
     * @return string
     */
    public function getNewsletterProduct1Link()
    {
        return $this->newsletterProduct1Link;
    }

    /**
     * Set newsletterProduct2Img
     *
     * @param string $newsletterProduct2Img
     * @return Newsletter
     */
    public function setNewsletterProduct2Img($newsletterProduct2Img)
    {
        if ($this->newsletterProduct2Img) {
            // Only append the web directory if the given image path is a relative path.
            $imagePath = substr($this->newsletterProduct2Img, 0, 1) == '/' ? $this->newsletterProduct2Img : $this->getWebDirectory() . $this->newsletterProduct2Img;
            $this->oldProduct2Img = new File($imagePath);
        }
        $this->newsletterProduct2Img = $newsletterProduct2Img;

        return $this;
    }

    /**
     * Get newsletterProduct2Img
     *
     * @return string
     */
    public function getNewsletterProduct2Img()
    {
        return $this->newsletterProduct2Img;
    }

    /**
     * Set newsletterProduct2Link
     *
     * @param string $newsletterProduct2Link
     * @return Newsletter
     */
    public function setNewsletterProduct2Link($newsletterProduct2Link)
    {
        $this->newsletterProduct2Link = $newsletterProduct2Link;

        return $this;
    }

    /**
     * Get newsletterProduct2Link
     *
     * @return string
     */
    public function getNewsletterProduct2Link()
    {
        return $this->newsletterProduct2Link;
    }

    /**
     * Set newsletterBlastId
     *
     * @param integer $newsletterBlastId
     * @return Newsletter
     */
    public function setNewsletterBlastId($newsletterBlastId)
    {
        $this->newsletterBlastId = $newsletterBlastId;

        return $this;
    }

    /**
     * Get newsletterBlastId
     *
     * @return integer
     */
    public function getNewsletterBlastId()
    {
        return $this->newsletterBlastId;
    }

    /**
     * Set newsletterBlastScheduleTime
     *
     * @param \DateTime $newsletterBlastScheduleTime
     * @return Newsletter
     */
    public function setNewsletterBlastScheduleTime($newsletterBlastScheduleTime)
    {
        $this->newsletterBlastScheduleTime = $newsletterBlastScheduleTime;

        return $this;
    }

    /**
     * Get newsletterBlastScheduleTime
     *
     * @return \DateTime
     */
    public function getNewsletterBlastScheduleTime()
    {
        return $this->newsletterBlastScheduleTime;
    }

    /**
     * @ORM\PrePersist
     */
    public function processImages()
    {
        if ($this->newsletterCollectionImg instanceof File) {
            $filename = $this->getEncodedFilename($this->newsletterCollectionImg);
            $this->fileCollectionImg        = $this->newsletterCollectionImg;
            $this->tempCollectionImg        = $this->newsletterCollectionImg;
            $this->newsletterCollectionImg  = $this->getUploadDirectory() 
                                                . $filename 
                                                . '.' 
                                                . $this->fileCollectionImg->guessExtension();
        }

        if ($this->newsletterProduct1Img instanceof File) {
            $filename = $this->getEncodedFilename($this->newsletterProduct1Img);
            $this->fileProduct1Img          = $this->newsletterProduct1Img;
            $this->tempProduct1Img          = $this->newsletterProduct1Img;
            $this->newsletterProduct1Img    = $this->getUploadDirectory() 
                                                . $filename 
                                                . '.' 
                                                . $this->fileProduct1Img->guessExtension();
        }

        if ($this->newsletterProduct2Img instanceof File) {
            $filename = $this->getEncodedFilename($this->newsletterProduct2Img);
            $this->fileProduct2Img        = $this->newsletterProduct2Img;
            $this->tempProduct2Img        = $this->newsletterProduct2Img;
            $this->newsletterProduct2Img  = $this->getUploadDirectory() 
                                                    . $filename 
                                                    . '.' 
                                                    . $this->fileProduct2Img->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist
     */
    public function upload()
    {
        if ($this->fileCollectionImg instanceof File) {
            $this->fileCollectionImg->move($this->getUploadRootDirectory(), $this->newsletterCollectionImg);
            if (file_exists($this->tempCollectionImg)) unlink($this->tempCollectionImg);
        }
        if ($this->fileProduct1Img instanceof File) {
            $this->fileProduct1Img->move($this->getUploadRootDirectory(), $this->newsletterProduct1Img);
            if (file_exists($this->tempProduct1Img)) unlink($this->tempProduct1Img);
        }
        if ($this->fileProduct2Img instanceof File) {
            $this->fileProduct2Img->move($this->getUploadRootDirectory(), $this->newsletterProduct2Img);
            if (file_exists($this->tempProduct2Img)) unlink($this->tempProduct2Img);
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
     * @ORM\PostUpdate
     */
    public function checkUpload()
    {
        $this->upload();
        if ($this->oldCollectionImg && $this->oldCollectionImg != $this->getWebDirectory() . $this->newsletterCollectionImg) {
            unlink($this->oldCollectionImg);
        }
        if ($this->oldProduct1Img && $this->oldProduct1Img != $this->getWebDirectory() . $this->newsletterProduct1Img) {
            unlink($this->oldProduct1Img);
        }
        if ($this->oldProduct2Img && $this->oldProduct2Img != $this->getWebDirectory() . $this->newsletterProduct2Img) {
            unlink($this->oldProduct2Img);
        }
    }

    /**
     * @ORM\PostRemove
     */
    public function deleteFile()
    {
        if ($this->newsletterCollectionImg) unlink($this->getWebDirectory() . $this->newsletterCollectionImg);
        if ($this->newsletterProduct1Img)   unlink($this->getWebDirectory() . $this->newsletterProduct1Img);
        if ($this->newsletterProduct2Img)   unlink($this->getWebDirectory() . $this->newsletterProduct2Img);
    }
}
