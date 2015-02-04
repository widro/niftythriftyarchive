<?php

namespace NiftyThrifty\ShopBundle\Listener;

use Doctrine\ORM\Events;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * This is an event listener for the Nifty Bundle.  Most of these items should be convertable
 * in to doctrine Lifecycle Callbacks when we upgrade to Doctrine 2.4.
 */
class NiftyThriftyEventListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $em = $args->getObjectManager();
        
        if ($entity instanceof BasketItem) {
            /**
             * Before saving this item to a user's cart, verify it's not currently active in any
             * other user's carts.
             */
            $dql = "SELECT COUNT(b.basketItemId)
                      FROM NiftyThrifty\ShopBundle\Entity\BasketItem b
                     WHERE b.productId = :productId
                       AND b.basketItemStatus = 'valid'";
            $query = $event->getEntityManager()
                           ->createQuery($dql)
                           ->setParameter('productId', $entity->getProductId());
            
            if ($query->getSingleScalarResult()) {
                throw new ValidateException('This item is not available for reservation.');
            }
        }
    }
}