<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CollectionRepository
 *
 * Symfony wants its FINDers in a different class.  This is that class for
 * finding various types of collections.
 */
class CollectionRepository extends EntityRepository
{
    /**
     * Find all active collections
     */
    public function findAllActive($limit = 100, $orderBy="collectionDateStart", $direction="DESC")
    {
        $dql = "SELECT c
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateEnd > CURRENT_TIMESTAMP()
                   AND c.collectionDateStart < CURRENT_TIMESTAMP()
                   AND c.collectionActive = :activeVal
                 ORDER BY c.$orderBy $direction
					";
        $params = array('activeVal' => 'yes');

        return $this->runQuery($dql, $params,$limit,1);
    }

    /**
     * Find count active collections
     */
    public function findCountAllActive()
    {
        $dql = "SELECT count(c.collectionId)
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateEnd > CURRENT_TIMESTAMP()
                   AND c.collectionDateStart < CURRENT_TIMESTAMP()
                   AND c.collectionActive = :activeVal
					";
        $params = array('activeVal' => 'yes');

        return $this->returnScalarResult($dql, $params);
    }

    /**
     * Find all active collections
     */
    public function findUpcoming()
    {
        $dql = "SELECT c
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateStart > CURRENT_TIMESTAMP()
                   AND c.collectionActive = :activeVal
                   AND c.isShop = :noValue
                 ORDER BY c.collectionDateStart ASC";
        $params = array('activeVal' => 'yes',
                        'noValue'   => 'no');

        return $this->runQuery($dql, $params);
    }

    /**
     * Find all active collections
     */
    public function findEndingSoon($orderBy="collectionDateEnd", $direction="ASC")
    {
        $dql = "SELECT c
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateEnd BETWEEN CURRENT_TIMESTAMP() AND DATE_ADD(CURRENT_TIMESTAMP(), 1, 'DAY')
                   AND c.collectionActive = :activeVal
                   AND c.isShop = :noValue
                 ORDER BY c.$orderBy $direction";
        $params = array('activeVal' => 'yes',
                        'noValue'   => 'no');

        return $this->runQuery($dql, $params);
    }

    /**
     * Find all active collections
     */
    public function findActiveNotEndingSoon($orderBy="collectionDateEnd", $direction="ASC")
    {
        $dql = "SELECT c
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateEnd > DATE_ADD(CURRENT_TIMESTAMP(), 1, 'DAY')
                   AND c.collectionDateStart < CURRENT_TIMESTAMP()
                   AND c.collectionActive = :activeVal
                   AND c.isShop = :noValue
                 ORDER BY c.$orderBy $direction";

        $params = array('activeVal' => 'yes',
                        'noValue'   => 'no');

        return $this->runQuery($dql, $params);
    }

    /**
     * Return the collections that should be displayed in the top navigation stuff.
     */
    public function findShopsForNavigation()
    {
        $dql = "SELECT c
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateEnd > CURRENT_TIMESTAMP()
                   AND c.collectionDateStart < CURRENT_TIMESTAMP()
                   AND c.collectionActive = :yesActive
                   AND c.isShop = :yesShop
                 ORDER BY c.collectionName ASC";
        $params = array('yesActive' => 'yes',
                        'yesShop'   => 'yes');

        return $this->runQuery($dql, $params);
    }

    /**
     * Return the shops to show on the shop splash page.
     */
    public function findAllShops()
    {
        $dql = "SELECT c
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateEnd > CURRENT_TIMESTAMP()
                   AND c.collectionDateStart < CURRENT_TIMESTAMP()
                   AND c.collectionActive = :yesActive
                   AND c.isShop = :yesShop
                 ORDER BY c.collectionDateStart DESC";
        $params = array('yesActive' => 'yes',
                        'yesShop'   => 'yes');

        return $this->runQuery($dql, $params);
    }


    /**
     * Return the collection ids to be passed to js for filters.
     */
    public function collectionsForFilter()
    {
        $dql = "SELECT c.collectionId
                  FROM NiftyThrifty\ShopBundle\Entity\Collection c
                 WHERE c.collectionDateStart < CURRENT_TIMESTAMP()
                   AND c.collectionActive = :yesActive
                 ORDER BY c.collectionDateStart DESC";
        $params = array('yesActive' => 'yes');

        return $this->runQuery($dql, $params);
    }







}
