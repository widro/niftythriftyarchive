<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class allows Entities to handle file uploads
 * Wrapper class so that certain upload tools can be removed from the entity classes
 */
abstract class FileUploadEntity
{
    private $files;

    public function __set($name, File $value)
    {
        $this->files[$name] = $value;
    }

    public function __get($name)
    {
        if (!is_array($this->files)) $this->files = array();
        
        if (!array_key_exists($name, $this->files)) {
            return null;
        }
        return $this->files[$name];
    }
    
    public function getFiles()
    {
        if (!$this->files) $this->files = array();
        return $this->files;
    }

    public function getUploadRootDirectory()
    {
        return $this->getWebDirectory() . $this->getUploadDirectory();
    }

    /**
     * This is working under the assumption:
     *      __DIR__ = ~/src/NiftyThrifty/ShopBundle/Entity
     */
    public function getWebDirectory()
    {
        return __DIR__ . "/../../../../web/";
    }

    /**
     * Uploads should go to the directory web/images/uploads/<currentYear>/<currentMonth>
     */
    public function getUploadDirectory()
    {
        $year = date("Y");
        $month= date("m");

        return "images/uploads/$year/$month/";
    }
    
    /**
     * Get a unique sha for each images, which will be saved in the database row as the file name.
     */
    public function getEncodedFilename($name)
    {
        return sha1($name . uniqid(mt_rand(), true));
    }
    
    /**
     * The following abstract methods should be defined in each class that extends this class.  This
     * class should be used in any Symfony Entity that requires image uploads and these functions
     * should be defined in Doctrine lifecycle events.
     */
    /**
     * This should be a Symfony/Doctrine PrePersist method
     */
    abstract public function processImages();
    
    /**
     * This should be a Symfony/Doctrine PreUpdate method
     */
    abstract public function checkImages();
    
    /**
     * This should be a Symfony/Doctrine PostPersist method.  This makes sure the uploaded
     * file can be moved.
     */
    abstract public function upload();
    
    /**
     * This should be a Symfony/Doctrine PostUpdate method.  This makes sure the uploaded
     * file can be moved and that the old file has been deleted.
     */
    abstract public function checkUpload();
    
    /**
     * This should be a Symfony/Doctrine PostRemove method.  This deletes the file if the
     * entity has been deleted.
     */
    abstract public function deleteFile();
}
