<?php

namespace NiftyThrifty\ShopBundle\Form\DataTransformer;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\TaskBundle\Entity\Issue;

class CollectionTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (collection) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function reverseTransform($collection)
    {
        if (null === $collection) {
            return "";
        }

        return $collection->getCollectionId();
    }

    /**
     * Transforms a string (number) to an object (collection).
     *
     * @param  string $number
     * @return Issue|null
     * @throws TransformationFailedException if object (collection) is not found.
     */
    public function transform($number)
    {
        if (!$number) {
            return null;
        }

        $issue = $this->om
            ->getRepository('NiftyThriftyShopBundle:Collection')
            ->findOneBy(array('collectionId' => $number))
        ;

        if (null === $issue) {
            throw new TransformationFailedException(sprintf(
                'An collection with id "%s" does not exist!',
                $number
            ));
        }

        return $issue;
    }
}
