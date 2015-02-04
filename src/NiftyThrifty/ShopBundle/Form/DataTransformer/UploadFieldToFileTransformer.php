<?php

namespace NiftyThrifty\ShopBundle\Form\DataTransformer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class UploadFieldToFileTransformer implements DataTransformerInterface
{

    /**
     * We save a filepath, but the form expects a file.
     */
    public function transform($filePath)
    {
        if (null === $filePath) {
            return '';
        }
        
        try {
            $file = new File($filePath);
        } catch (\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e) {
            $file = new File("/var/www/Symfony/web/$filePath");
        }
        
        return $file;
    }

    /**
     * Reverse transform is not necessary, but is included for the interface.
     */
    public function reverseTransform($file)
    {
        return $file;
    }
}
