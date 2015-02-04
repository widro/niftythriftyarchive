<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use NiftyThrifty\ShopBundle\Entity\ProductTagtype;

/**
 * The navigation controller contains methods relating to filling out links in the header and
 * footer of the site.  This controller handles no on-site routing, but only contains methods
 * called directly from views.  Responses in here should only generate partial templates.
 */
class NavigationController extends Controller
{
    /**
     * Get all the categories that we should display on the navigation menu.
     * Called from: layout.html.twig::NiftyThriftyShopBundle:Navigation:Categories
     */
    public function categoriesAction()
    {
        $categories = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                           ->findNavigation();
        return $this->render('NiftyThriftyShopBundle:Navigation:_itemList.html.twig',
                             array('title'     => 'Categories',
                                   'prefix'    => 'cat',
                                   'items'     => $categories,
                                   'path_name' => 'show_category'));
    }

    /**
     * Get all the shops we should display on the navigation menu.
     * Called from: layout.html.twig::NiftyThriftyShopBundle:Navigation:Shops
     */
    public function shopsAction()
    {
        $shops = $this->getDoctrine()
                      ->getManager()
                      ->getRepository('NiftyThriftyShopBundle:Collection')
                      ->findShopsForNavigation();
        return $this->render('NiftyThriftyShopBundle:Navigation:_itemList.html.twig',
                             array('title'     => 'Shops',
                                   'prefix'    => 'shp',
                                   'items'     => $shops,
                                   'path_name' => 'show_collection'));
    }

    /**
     * Collections in navigation are split in to ending soon and not ending soon.  The
     * ending soon delineator is 24 hours.  So the "active" collections are ones that
     * expire in more than 24 hours, while the ending function below gets the ones
     * expiring in less than 24 hours.
     */
    public function activeCollectionsAction()
    {
        $em = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('NiftyThriftyShopBundle:Collection');
		$totalcollections = $em->findCountAllActive();
		$activecollections = $totalcollections-7;
        $activecollections = 11;
        $collections = $em->findAllActive($activecollections, $orderBy="collectionDateEnd", $direction="DESC");
        return $this->render('NiftyThriftyShopBundle:Navigation:_itemList.html.twig',
                             array('title'     => 'Current Sales',
                                   'prefix'    => 'coll',
                                   'items'     => $collections,
                                   'path_name' => 'show_collection'));
    }

    /**
     * Collections ending in the next 24 hours
     */
    public function endingCollectionsAction()
    {
        $collections = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('NiftyThriftyShopBundle:Collection')
                            ->findAllActive(7, $orderBy="collectionDateEnd", $direction="ASC");
        return $this->render('NiftyThriftyShopBundle:Navigation:_itemList.html.twig',
                             array('title'     => 'Ending Soon',
                                   'prefix'    => 'coll',
                                   'items'     => $collections,
                                   'path_name' => 'show_collection'));
    }

    /**
     * left sidebar grey filters for all cats
     */
    public function filtersAllSidebarAction($categoryId)
    {
        $categories = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                        ->findCategoriesWomen();

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}
        return $this->render('NiftyThriftyShopBundle:Navigation:_filtersAll.html.twig',
                             array('categories'         => $categories,
                                   'activeCategoryId'   => $categoryId,
                                   'sizes'              => $sizes));
    }

    /**
     * left sidebar grey filters for search
     */
    public function filtersShopSidebarAction($activeId="")
    {
        $categories = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                        ->findCategoriesWomen();

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}
        return $this->render('NiftyThriftyShopBundle:Navigation:_filtersShop.html.twig',
                             array('categories'         => $categories,
                                   'activeId'           => $activeId,
                                   'sizes'              => $sizes));
    }

    /**
     * get total credits for top nav.
     * Called from: layout.html.twig::NiftyThriftyShopBundle:Navigation:Shops
     */
    public function userCreditTopNavAction()
    {
        $currentUser = $this->getUser();
        if ($currentUser instanceof \NiftyThrifty\ShopBundle\Entity\User) {
			$usercredits = $this->getDoctrine()
						  ->getManager()
						  ->getRepository('NiftyThriftyShopBundle:UserCredits')
						  ->getUserCreditTotal($this->getUser()->getUserId());
        } else {
        	$usercredits = 0;
        }
		return new Response($usercredits);
    }
    
    /**
     * Return a banner image.
     */
    public function getBannerAction($bannerTypeName)
    {
        $banner = $this->getDoctrine()
                       ->getRepository('NiftyThriftyShopBundle:Banner')
                       ->findDisplayBannerByType($bannerTypeName);
                       
        return $this->render('NiftyThriftyShopBundle:Navigation:_banner.html.twig',
                                array('banner' => $banner));
    }
}
