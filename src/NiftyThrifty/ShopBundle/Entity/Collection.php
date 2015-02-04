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
 * NiftyThrifty\ShopBundle\Entity\Collection
 *
 * @ORM\Table(name="collection")
 * @ORM\Entity
 */
class Collection extends FileUploadEntity
{
    /**
     * @var integer $collectionId
     *
     * @ORM\Column(name="collection_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $collectionId;

    /**
     * @var string $collectionCode
     *
     * @ORM\Column(name="collection_code", type="string", length=5, nullable=true)
     */
    private $collectionCode;

    /**
     * @var string
     */
    private $isShop;

    /**
     * @var string $collectionName
     *
     * @ORM\Column(name="collection_name", type="string", length=63, nullable=false)
     */
    private $collectionName;

    /**
     * @var string $collectionDescription
     *
     * @ORM\Column(name="collection_description", type="text", nullable=false)
     */
    private $collectionDescription;

    /**
     * @var string $collectionType
     *
     * @ORM\Column(name="collection_type", type="string", nullable=true)
     */
    private $collectionType;

    /**
     * @var \DateTime $collectionDateStart
     *
     * @ORM\Column(name="collection_date_start", type="datetime", nullable=false)
     */
    private $collectionDateStart;

    /**
     * @var \DateTime $collectionDateEnd
     *
     * @ORM\Column(name="collection_date_end", type="datetime", nullable=false)
     */
    private $collectionDateEnd;

    /**
     * @var string $collectionActive
     *
     * @ORM\Column(name="collection_active", type="string", nullable=false)
     */
    private $collectionActive;

    /**
     * @var string $collectionVisualHomeHero
     *
     * @ORM\Column(name="collection_visual_home_hero", type="string", length=255, nullable=true)
     */
    private $collectionVisualHomeHero;

    /**
     * @var string $collectionVisualMainPanel
     *
     * @ORM\Column(name="collection_visual_main_panel", type="string", length=255, nullable=true)
     */
    private $collectionVisualMainPanel;

    /**
     * @var string $collectionVisualMainPanelBw
     *
     * @ORM\Column(name="collection_visual_main_panel_bw", type="string", length=255, nullable=true)
     */
    private $collectionVisualMainPanelBw;

    /**
     * @var string $collectionVisualSaleHero
     *
     * @ORM\Column(name="collection_visual_sale_hero", type="string", length=255, nullable=true)
     */
    private $collectionVisualSaleHero;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="collection")
     */
    protected $products;

    /**
     * Object validation.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // It either is or is not a shop.
        $metadata->addPropertyConstraint('isShop', new Assert\NotBlank(array('message' => 'Select if this is a shop')));
        $metadata->addPropertyConstraint('isShop', new Assert\Choice(array('choices' => array('yes', 'no'),
                                                                           'message' => 'You must choose if this is a shop')));

        // Collection code may be either blank or exactly 3 characters.
        $metadata->addPropertyConstraint('collectionCode',
                                         new Assert\Length(array('max'          => 3,
                                                                 'min'          => 3,
                                                                 'exactMessage' => 'Collection code must be blank or 3 characters')));

        // Collection name must be not blank and less than 60 characters
        $metadata->addPropertyConstraint('collectionName', new Assert\NotBlank(array('message' => 'Collection name can not be blank')));
        $metadata->addPropertyConstraint('collectionName', new Assert\Length(array('max'    => 60,
                                                                                   'maxMessage' => 'Collection name must be less than 60 characters')));

        // Collection description must be not blank
        $metadata->addPropertyConstraint('collectionDescription', new Assert\NotBlank(array('message' => 'Collection description can not be blank')));

        // Collection description must be one of three selections or null.
        $metadata->addPropertyConstraint('collectionType',
                                         new Assert\Choice(array('choices' => array('Women','Men','Home'),
                                                                 'message' => 'Collection type must be Women, Men, Home, or not set')));

        // Dates may not be blank
        $metadata->addPropertyConstraint('collectionDateStart', new Assert\NotBlank(array('message' => 'Start date must be defined')));
        $metadata->addPropertyConstraint('collectionDateEnd',   new Assert\NotBlank(array('message' => 'End date must be defined')));

        // Active or inactive must be defined
        $metadata->addPropertyConstraint('collectionActive',    new Assert\NotBlank(array('message' => 'Select whether the shop is active')));
        $metadata->addPropertyConstraint('collectionActive',    new Assert\Choice(array('choices' => array('yes', 'no'),
                                                                                        'message' => 'You must choose if this collection is active')));

        // Images
        $metadata->addPropertyConstraint('collectionVisualHomeHero',    new Assert\Image());
        $metadata->addPropertyConstraint('collectionVisualMainPanel',   new Assert\Image());
        $metadata->addPropertyConstraint('collectionVisualMainPanelBw', new Assert\Image());
        $metadata->addPropertyConstraint('collectionVisualSaleHero',    new Assert\Image());
    }

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getCollectionName();
    }

    /**
     * Sanitized getId and getName for the Navigation Controller
     */
    public function getId()
    {
        return $this->getCollectionId();
    }
    public function getName()
    {
        return $this->getCollectionName();
    }

    /**
     * Get collectionId
     *
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Set collectionCode
     *
     * @param string $collectionCode
     * @return Collection
     */
    public function setCollectionCode($collectionCode)
    {
        $this->collectionCode = $collectionCode;

        return $this;
    }

    /**
     * Get collectionCode
     *
     * @return string
     */
    public function getCollectionCode()
    {
        return $this->collectionCode;
    }

    /**
     * Set collectionName
     *
     * @param string $collectionName
     * @return Collection
     */
    public function setCollectionName($collectionName)
    {
        $this->collectionName = $collectionName;

        return $this;
    }

    /**
     * Get collectionName
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     * Set collectionDescription
     *
     * @param string $collectionDescription
     * @return Collection
     */
    public function setCollectionDescription($collectionDescription)
    {
        $this->collectionDescription = $collectionDescription;

        return $this;
    }

    /**
     * Get collectionDescription
     *
     * @return string
     */
    public function getCollectionDescription()
    {
        return $this->collectionDescription;
    }

    /**
     * Set collectionType
     *
     * @param string $collectionType
     * @return Collection
     */
    public function setCollectionType($collectionType)
    {
        $this->collectionType = $collectionType;

        return $this;
    }

    /**
     * Get collectionType
     *
     * @return string
     */
    public function getCollectionType()
    {
        return $this->collectionType;
    }

    /**
     * Set collectionDateStart
     *
     * @param \DateTime $collectionDateStart
     * @return Collection
     */
    public function setCollectionDateStart($collectionDateStart)
    {
        $this->collectionDateStart = $collectionDateStart;

        return $this;
    }

    /**
     * Get collectionDateStart
     *
     * @return \DateTime
     */
    public function getCollectionDateStart()
    {
        return $this->collectionDateStart;
    }

    /**
     * Set collectionDateEnd
     *
     * @param \DateTime $collectionDateEnd
     * @return Collection
     */
    public function setCollectionDateEnd($collectionDateEnd)
    {
        $this->collectionDateEnd = $collectionDateEnd;

        return $this;
    }

    /**
     * Get collectionDateEnd
     *
     * @return \DateTime
     */
    public function getCollectionDateEnd()
    {
        return $this->collectionDateEnd;
    }

    /**
     * Set collectionActive
     *
     * @param string $collectionActive
     * @return Collection
     */
    public function setCollectionActive($collectionActive)
    {
        $this->collectionActive = $collectionActive;

        return $this;
    }

    /**
     * Get collectionActive
     *
     * @return string
     */
    public function getCollectionActive()
    {
        return $this->collectionActive;
    }

    /**
     * Set collectionVisualHomeHero
     *
     * @param UploadedFile $file
     * @return Collection
     */
    public function setCollectionVisualHomeHero($collectionVisualHomeHero)
    {
        if ($this->collectionVisualHomeHero) {
            $this->oldVisualHomeHero = new File($this->getWebDirectory() . $this->collectionVisualHomeHero);
        }
        $this->collectionVisualHomeHero = $collectionVisualHomeHero;

        return $this;
    }

    /**
     * Get collectionVisualHomeHero
     *
     * @return string
     */
    public function getCollectionVisualHomeHero()
    {
        return $this->collectionVisualHomeHero;
    }

    /**
     * Set collectionVisualMainPanel
     *
     * @param string $collectionVisualMainPanel
     * @return Collection
     */
    public function setCollectionVisualMainPanel($collectionVisualMainPanel)
    {
        if ($this->collectionVisualMainPanel) {
            $this->oldVisualMainPanel = new File($this->getWebDirectory() . $this->collectionVisualMainPanel);
        }
        $this->collectionVisualMainPanel = $collectionVisualMainPanel;

        return $this;
    }

    /**
     * Set collectionVisualMainPanelBw
     *
     * @param string $collectionVisualMainPanelBw
     * @return Collection
     */
    public function setCollectionVisualMainPanelBw($collectionVisualMainPanelBw)
    {
        if ($this->collectionVisualMainPanelBw) {
            $this->oldVisualMainPanelBw = new File($this->getWebDirectory() . $this->collectionVisualMainPanelBw);
        }
        $this->collectionVisualMainPanelBw = $collectionVisualMainPanelBw;

        return $this;
    }

    /**
     * Get collectionVisualMainPanel
     *
     * @return string
     */
    public function getCollectionVisualMainPanel()
    {
        return $this->collectionVisualMainPanel;
    }

    /**
     * Get collectionVisualMainPanelBw
     *
     * @return string
     */
    public function getCollectionVisualMainPanelBw()
    {
        return $this->collectionVisualMainPanelBw;
    }

    /**
     * Set collectionVisualSaleHero
     *
     * @param string $collectionVisualSaleHero
     * @return Collection
     */
    public function setCollectionVisualSaleHero($collectionVisualSaleHero)
    {
        if ($this->collectionVisualSaleHero) {
            $this->oldVisualSaleHero = new File($this->getWebDirectory() . $this->collectionVisualSaleHero);
        }
        $this->collectionVisualSaleHero = $collectionVisualSaleHero;

        return $this;
    }

    /**
     * Get collectionVisualSaleHero
     *
     * @return string
     */
    public function getCollectionVisualSaleHero()
    {
        return $this->collectionVisualSaleHero;
    }

    /**
     * Add products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     * @return Collection
     */
    public function addProduct(\NiftyThrifty\ShopBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param NiftyThrifty\ShopBundle\Entity\Product $products
     */
    public function removeProduct(\NiftyThrifty\ShopBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set isShop
     *
     * @param string $isShop
     * @return Collection
     */
    public function setIsShop($isShop)
    {
        $this->isShop = $isShop;

        return $this;
    }

    /**
     * Get isShop
     *
     * @return string
     */
    public function getIsShop()
    {
        return $this->isShop;
    }

    /**
     * Return the time left for collection.
     */
	public function getCollectionEndDateDiff(){
		$date_end_val = $this->getCollectionDateEnd()->format('Y-m-d H:i:s');
		$date_end = strtotime($date_end_val);
		//$date_end = strtotime($this->getCollectionDateEnd());
		$date_current = time();

		$diff = $date_end-$date_current;
		$d = $this->getDiffText($diff);

		return $d;
	}

	public function getDiffText($diff)
	{
		if($diff < 0)
			return '';

		// nb days
		$diff_days = floor($diff/24/60/60);
		$diff -= $diff_days*24*60*60;

		// nb hours
		$diff_hours = floor($diff/60/60);
		$diff -= $diff_hours*60*60;

		// nb minutes
		$diff_minutes = floor($diff/60);
		$diff -= $diff_minutes*60;

		// nb secondes
		$diff_secondes = $diff;

		// format
		$d = '';
		if($diff_days > 0)
		{
			$d .= $diff_days. ' day';
			if($diff_days > 1)
				$d .= 's';
			if($diff_hours > 0){
				$d .= ', '.$diff_hours.' hour';
				if($diff_hours > 1)
					$d .= 's';
			}
		}
		else if($diff_hours > 0)
		{
			$d .= $diff_hours. ' hour';
			if($diff_hours > 1)
				$d .= 's';
			if($diff_minutes > 0){
				$d .= ', '.$diff_minutes.' min';
			}
		}
		else{
			$d .= $diff_minutes. ' min';
			if($diff_secondes > 0){
				$d .= ', '.$diff_secondes.' sec';
			}
		}
		return $d;
	}

    /**
     * Pre-persist checks if fields are uploaded files, and if they are, transform them in to
     * strings to save to the database.  PostPersist handles moving the files.
     *
     * @ORM\PrePersist
     */
    public function processImages()
    {
        if ($this->collectionVisualHomeHero instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->collectionVisualHomeHero);
            $this->fileVisualHomeHero       = $this->collectionVisualHomeHero;
            $this->tempVisualHomeHero       = $this->collectionVisualHomeHero;
            $this->collectionVisualHomeHero = $this->getUploadDirectory()
                                                . $filename
                                                . '.'
                                                . $this->fileVisualHomeHero->guessExtension();
        }

        if ($this->collectionVisualMainPanel instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->collectionVisualMainPanel);
            $this->fileVisualMainPanel      = $this->collectionVisualMainPanel;
            $this->tempVisualMainPanel      = $this->collectionVisualMainPanel;
            $this->collectionVisualMainPanel= $this->getUploadDirectory()
                                                . $filename
                                                . '.'
                                                . $this->fileVisualMainPanel->guessExtension();
        }

        if ($this->collectionVisualMainPanelBw instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->collectionVisualMainPanelBw);
            $this->fileVisualMainPanelBw        = $this->collectionVisualMainPanelBw;
            $this->tempVisualMainPanelBw        = $this->collectionVisualMainPanelBw;
            $this->collectionVisualMainPanelBw  = $this->getUploadDirectory()
                                                    . $filename
                                                    . '.'
                                                    . $this->fileVisualMainPanelBw->guessExtension();
        }

        if ($this->collectionVisualSaleHero instanceof UploadedFile) {
            $filename = $this->getEncodedFilename($this->collectionVisualSaleHero);
            $this->fileVisualSaleHero       = $this->collectionVisualSaleHero;
            $this->tempVisualSaleHero       = $this->collectionVisualSaleHero;
            $this->collectionVisualSaleHero = $this->getUploadDirectory()
                                                . $filename
                                                . '.'
                                                . $this->fileVisualSaleHero->guessExtension();
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
     * If there's been a file uploaded, move it to a permanent location.
     *
     * @ORM\PostPersist
     */
    public function upload()
    {
        if ($this->fileVisualHomeHero instanceof UploadedFile) {
            $this->fileVisualHomeHero->move($this->getUploadRootDirectory(), $this->collectionVisualHomeHero);
            if (file_exists($this->tempVisualHomeHero)) unlink($this->tempVisualHomeHero);
        }
        if ($this->fileVisualMainPanel instanceof UploadedFile) {
            $this->fileVisualMainPanel->move($this->getUploadRootDirectory(), $this->collectionVisualMainPanel);
            if (file_exists($this->tempVisualMainPanel)) unlink($this->tempVisualMainPanel);
        }
        if ($this->fileVisualMainPanelBw instanceof UploadedFile) {
            $this->fileVisualMainPanelBw->move($this->getUploadRootDirectory(), $this->collectionVisualMainPanelBw);
            if (file_exists($this->tempVisualMainPanelBw)) unlink($this->tempVisualMainPanelBw);
        }
        if ($this->fileVisualSaleHero instanceof UploadedFile) {
            $this->fileVisualSaleHero->move($this->getUploadRootDirectory(), $this->collectionVisualSaleHero);
            if (file_exists($this->tempVisualSaleHero)) unlink($this->tempVisualSaleHero);
        }
    }

    /**
     * The file setters set an old value for the file.  If the file is being up
     *
     * @ORM\PostUpdate
     */
    public function checkUpload()
    {
        $this->upload();
        if ($this->oldVisualHomeHero && $this->oldVisualHomeHero != $this->getWebDirectory() . $this->collectionVisualHomeHero) {
            unlink($this->oldVisualHomeHero);
        }
        if ($this->oldVisualMainPanel && $this->oldVisualMainPanel != $this->getWebDirectory() . $this->collectionVisualMainPanel) {
            unlink($this->oldVisualMainPanel);
        }
        if ($this->oldVisualMainPanelBw && $this->oldVisualMainPanelBw != $this->getWebDirectory() . $this->collectionVisualMainPanelBw) {
            unlink($this->oldVisualMainPanelBw);
        }
        if ($this->oldVisualSaleHero && $this->oldVisualSaleHero != $this->getWebDirectory() . $this->collectionVisualSaleHero) {
            unlink($this->oldVisualSaleHero);
        }
    }

    /**
     * If this entity is deleted, delete the files if they exist.
     *
     * @ORM\PostRemove
     */
    public function deleteFile()
    {
        if ($this->collectionVisualHomeHero)    unlink($this->getWebDirectory() . $this->collectionVisualHomeHero);
        if ($this->collectionVisualMainPanel)   unlink($this->getWebDirectory() . $this->collectionVisualMainPanel);
        if ($this->collectionVisualMainPanelBw) unlink($this->getWebDirectory() . $this->collectionVisualMainPanelBw);
        if ($this->collectionVisualSaleHero)    unlink($this->getWebDirectory() . $this->collectionVisualSaleHero);
    }
}
