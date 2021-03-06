<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BannerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BannerRepository extends EntityRepository
{
    /**
     * Return one banner to be displayed in the given zone.  The process of which is as follows:
     *  1) Find all banners that are valid in the given time range.
     *      a) If there is only one, return it.
     *      b) If there is more than one, return one at random.
     *  2) If no banners fit in the given time range, return the default banner for that type.
     *  3) If there is no default; return null.
     */
    public function findDisplayBannerByType($bannerType)
    {
        $dql = "SELECT b
                  FROM NiftyThrifty\ShopBundle\Entity\Banner b
                 WHERE b.bannerType = :bannerType
                   AND b.rotationEndTime > CURRENT_TIMESTAMP()
                   AND b.rotationStartTime < CURRENT_TIMESTAMP()";
        $params = array('bannerType'=> $bannerType);
        $banners = $this->runQuery($dql, $params);
        
        if (!$banners) {
            return $this->findDefaultDisplayBannerByType($bannerType);
        }

        return $this->returnRandomBanner($banners);
    }
    
    /**
     * Return the default banner for a type.  There should only be one of these
     * but if there is more than one, return one at random.
     */
    public function findDefaultDisplayBannerByType($bannerType)
    {
        $dql = "SELECT b
                  FROM NiftyThrifty\ShopBundle\Entity\Banner b
                 WHERE b.bannerType = :bannerType
                   AND b.isDefault    = :yes";
        $params = array('bannerType'=> $bannerType,
                        'yes'       => 'yes');

        return $this->returnRandomBanner($this->runQuery($dql, $params));
    }
    
    /**
     * Given multiple banners in an ArrayCollection, return one of them at random.
     */
    public function returnRandomBanner($banners)
    {
        if (!$banners || sizeof($banners) == 0) {
            return null;
        } else if (sizeof($banners) == 1) {
            return $banners[0];
        } else {
            $key = mt_rand(0, sizeof($banners)-1);
            return $banners[$key];
        }
    }
}
