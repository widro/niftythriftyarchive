<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SearchController extends Controller
{
    /**
     * This function returns items by search terms.
     *
     * @Route("get_items_by_search/{itemValue}", name="get_items_by_search")
     * @Method("GET")
     */
    public function getItemsBySearchAction($itemValue)
    {
        $trimmedValue   = trim($itemValue);

        $items = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product')
                           ->findByTerms($trimmedValue, array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'DESC'));

        $productscount = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product')
                           ->findCountByTerms($trimmedValue);

        $description= strtolower($trimmedValue);

        $categories = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                        ->findCategoriesWomen();

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();


        return $this->render('NiftyThriftyShopBundle:Search:searchResults.html.twig',
                             array('products'   => $items,
							 'productscount'    => $productscount,
                             'categories' => $categories,
 							 'activeCategoryId'    => '',
                             'sizes' => $sizes,
                             'collectionsForFilter' => $collectionsForFilter,
                             'description'=> "Results:"));
    }

    /**
     * This handles returning all items that fits a certain value description.  It can handle
     * "under" and "over" and any number separated by a hyphen.  If only a value is defined,
     * it will default to "under".
     *
     * @Route("get_items_by_value/{itemValue}", name="get_items_by_value")
     * @Method("GET")
     */
    public function getItemsByValueAction($itemValue)
    {
        $pattern        = "/(over|under)-[0-9]+/";
        $trimmedValue   = trim($itemValue);
        if (!preg_match($pattern, $trimmedValue)) {
            throw $this->createNotFoundException('Page not found.');
        }

        $values     = explode('-', $trimmedValue);
        $repository = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product');
        $number     = $values[1];
        $description= strtolower($values[0]);
        if ($description == 'under') {
            $items      = $repository->findByPriceUnder($number, array('pageSize' => 12, 'pageNumber' => 1));
            $productscount      = $repository->findCountByPriceUnder($number, array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'DESC'));
        } else if ($description == 'over') {
            $items      = $repository->findByPriceOver($number, array('pageSize' => 12, 'pageNumber' => 1));
            $productscount      = $repository->findCountByPriceOver($number, array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'DESC'));
        }


        $categories = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                        ->findCategoriesWomen();

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();


        return $this->render('NiftyThriftyShopBundle:Search:searchResults.html.twig',
                             array('products'   => $items,
							 'productscount'    => $productscount,
                             'categories' => $categories,
 							 'activeCategoryId'    => '',
                             'sizes' => $sizes,
                             'collectionsForFilter' => $collectionsForFilter,
                             'description'=> "Results: " . ucfirst($description) . " \$$number"));
    }

    /**
     * This handles returning all items that are on sale
     *
     * @Route("get_items_by_sale", name="get_items_by_sale")
     * @Method("GET")
     */
    public function getItemsBySale()
    {
        $repository = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product');

		$items      = $repository->findByPriceOld(array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'ASC'));

		$productscount      = $repository->findCountByPriceOld(array('pageSize' => 12, 'pageNumber' => 1));

        $categories = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                        ->findCategoriesWomen();

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Search:searchResults.html.twig',
                             array('products'   => $items,
							 'productscount'    => $productscount,
                             'categories' => $categories,
 							 'activeCategoryId'    => '',
                             'sizes' => $sizes,
                             'collectionsForFilter' => $collectionsForFilter,
                             'description'=> "Clearance"));
    }


    /**
     * This handles returning all items that are on sale
     *
     * @Route("get_all_items", name="get_all_items")
     * @Method("GET")
     */
    public function getItemsByAll()
    {
        $repository = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product');

		$items      = $repository->findAll(array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'DESC'));

		$productscount      = $repository->findCountAll();

        $categories = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                        ->findCategoriesWomen();

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Search:searchResults.html.twig',
                             array('products'   => $items,
							 'productscount'    => $productscount,
                             'categories' => $categories,
 							 'activeCategoryId'    => '',
                             'sizes' => $sizes,
                             'collectionsForFilter' => $collectionsForFilter,
                             'description'=> "Full Catalog"));
    }
}
