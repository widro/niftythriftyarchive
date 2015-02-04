<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * The Shop controller.  This handles shopping in means other than collections.  This does
 * category, tag, look, and any other non-collection group method.
 */
class ShopController extends Controller
{
    /**
     * Given a category, show all the items in that category.
     *
     * @Route("category/{slug}", name="show_category")
     */
    public function showCategoryItemsAction($slug)
    {
        $itemArray  = explode('-', $slug);
        $categoryId = $itemArray[sizeof($itemArray)-1];
        $em         = $this->getDoctrine()->getManager();

        $category   = $em->getRepository('NiftyThriftyShopBundle:ProductCategory')
                         ->find($categoryId);

        if (!$category) {
            $products       = array();
            $title          = 'Category was not found';
            $productscount  = 0;
        } else {
            $products       = $em->getRepository('NiftyThriftyShopBundle:Product')
                                 ->findByCategory($categoryId, array('pageSize'         => 12,
                                                                     'pageNumber'       => 1,
                                                                     'orderBy' => 'productId',
                                                                     'orderDirection' => 'DESC'));
            $title          = $category->getProductCategoryName();

            $productscount  = $em->getRepository('NiftyThriftyShopBundle:Product')
                                 ->findCountByCategory($categoryId);



        }

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Shop:displayProductList.html.twig',
                             array('products' => $products,
                                   'productscount'    => $productscount,
                                   'title'    => $title,
                                   'activeCategoryId'    => $categoryId,
                                   'tagId'    => '',
                                   'collectionsForFilter'    => $collectionsForFilter,
                                   'description' => null));
    }

    /**
     * Given a size, show all items that share that size.
     *
     * @Route("size/{slug}", name="show_size")
     */
    public function showSizeItems($slug)
    {
        $itemArray  = explode('-', $slug);
        $sizeId     = $itemArray[sizeof($itemArray)-1];
        $em         = $this->getDoctrine()->getManager();
        $size       = $em->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                         ->find($sizeId);

        if (!$size) {
            $products       = array();
            $title          = 'Size was not found';
            $productscount  = 0;
        } else {
            $products   = $em->getRepository('NiftyThriftyShopBundle:Product')
                              ->findBySize($sizeId);
            $title      = $size->getProductCategorySizeName();
            $productscount   = $em->getRepository('NiftyThriftyShopBundle:Product')
                              ->findCountBySize($sizeId);
        }

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Shop:displayProductList.html.twig',
                             array('products' => $products,
                                   'productscount'    => $productscount,
                                   'title'    => $title,
                                   'tagId'    => '',
                                   'activeCategoryId'    => '',
                                   'collectionsForFilter'    => $collectionsForFilter,
                                   'description' => null));
    }

    /**
     * public function showDesignerItems($slug)
     *
     * @Route("designer/{slug}", name="show_designer")
     */
    public function showDesignerItems($slug)
    {
        $itemArray  = explode('-', $slug);
        $designerId = $itemArray[sizeof($itemArray)-1];
        $em         = $this->getDoctrine()->getManager();
        $designer   = $em->getRepository('NiftyThriftyShopBundle:Designer')
                         ->find($designerId);

        if (!$designer) {
            $products   = array();
            $title      = 'Designer was not found';
            $productscount = 0;
        } else {
            $products   = $em->getRepository('NiftyThriftyShopBundle:Product')
                             ->findByDesigner($designerId);
            $title      = $designer->getDesignerName();
            $productscount   = $em->getRepository('NiftyThriftyShopBundle:Product')
                             ->findCountByDesigner($designerId);
        }

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Shop:displayProductList.html.twig',
                             array('products'   => $products,
                                   'productscount'    => $productscount,
                                   'title'      => $title,
                                   'tagId'    => '',
                                   'activeCategoryId'    => '',
                                   'collectionsForFilter'    => $collectionsForFilter,
                                   'description'=> null));
    }

    /**
     * Show all items in a collection.  We don't restrict by 'sale' here because
     * we show sold items in a collection.
     *
     * @Route("collection/{slug}", name="show_collection_other")
     */
    public function showCollectionItems($slug)
    {
        $itemArray      = explode('-', $slug);
        $collectionId   = $itemArray[sizeof($itemArray)-1];
        $em             = $this->getDoctrine()->getManager();
        $collection     = $em->getRepository('NiftyThriftyShopBundle:Collection')
                             ->find($collectionId);

        if (!$collection) {
            $products       = array();
            $title          = 'Collection was not found.';
            $description    = null;
            $productscount  = 0;
        } else {
            $products       = $em->getRepository('NiftyThriftyShopBundle:Product')
                                 ->findByCollection($collectionId);
            $title          = $collection->getCollectionName();
            $description    = $collection->getCollectionDescription();
            $productscount  = $this->getDoctrine()
                                   ->getRepository('NiftyThriftyShopBundle:Product')
                                   ->findCountByCollection($collectionId);
        }

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Shop:displayProductList.html.twig',
                             array('products'   => $products,
                                   'title'      => $title,
                                   'productscount' => $productscount,
                                   'tagId'    => '',
                                   'activeCategoryId'    => '',
                                   'collectionsForFilter'    => $collectionsForFilter,
                                   'description'=> $description));
    }

    /**
     * Get a list of the tag items.
     *
     * @Route("show_tag/{slug}", name="show_tag")
     */
    public function showTagItemsAction($slug)
    {
        $itemArray  = explode('-', $slug);
        $tagId = $itemArray[sizeof($itemArray)-1];
        $em         = $this->getDoctrine()->getManager();

        $tag   = $em->getRepository('NiftyThriftyShopBundle:ProductTag')
                         ->find($tagId);

        if (!$tag) {
            $products = array();
            $title    = 'Tag was not found';
            $productscount = 0;
        } else {
            $products = $em->getRepository('NiftyThriftyShopBundle:Product')
                           ->findByLook($tagId, array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'DESC'));
            $title    = $tag->getProductTagName();

            $productscount   = $em->getRepository('NiftyThriftyShopBundle:Product')
                             ->findCountByLook($tagId);
        }

        $collectionsForFilter = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->collectionsForFilter();

        return $this->render('NiftyThriftyShopBundle:Shop:displayProductList.html.twig',
                             array('products' => $products,
                                   'productscount'    => $productscount,
                                   'title'    => $title,
                                   'tagId'    => $tagId,
                                   'activeCategoryId'    => $tagId,
                                   'collectionsForFilter'    => $collectionsForFilter,
                                   'description' => null));
    }

    /**
     * Show a single item.
     *
     * @Route("show_item/{slug}", name="show_item")
     */
    public function showSingleItemAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $itemArray = explode('-', $slug);
        $itemId = $itemArray[sizeof($itemArray)-1];
        $product = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->find($itemId);

        if (!$product) {
            throw $this->createNotFoundException('Page not found');
        }

        /**
         * Log the viewed item if this user is logged in.
         */
         $userhasloved = "";
        if ($this->getUser() instanceof \NiftyThrifty\ShopBundle\Entity\User) {
            $viewed  = new \NiftyThrifty\ShopBundle\Entity\UserViewedProduct();
            $nowTime = new \DateTime();
            $viewed->setUserId($this->getUser()->getUserId())
                   ->setProductId($product->getProductId())
                   ->setUser($this->getUser())
                   ->setProduct($product)
                   ->setDateViewed($nowTime);
            $errors = $this->get('validator')->validate($viewed);

            /**
             * Ignore a validation error if it happens, we don't care if this fails because of a
             * duplicate record
             */
            if (!$errors->count()) {
                $this->getDoctrine()->getManager()->persist($viewed);
                $this->getDoctrine()->getManager()->flush();
            }
            /**
             * check if the user has loved this item
             */
			try {
				$lovedItem = $em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
								->findByUserAndProduct($this->getUser()->getUserId(), $itemId);
				$userhasloved = "yes";

			} catch (NoResultException $e) {
				$userhasloved = "no";
			}
        }

        $suggestproducts = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Product')
                        ->findByCollectionForSuggest($product->getCollectionId(), array('pageSize' => 3, 'pageNumber' => 1));


        return $this->render('NiftyThriftyShopBundle:Shop:displayItem.html.twig',
                                array('product'         => $product,
                                      'userhasloved'    => $userhasloved,
                                      'suggestproducts' => $suggestproducts));
    }

    /**
     * Show a single collection.
     *
     * @Route("show_collection/{slug}", name="show_collection")
     */
    public function showSingleCollectionAction($slug)
    {
        $itemArray = explode('-', $slug);
        $itemId = $itemArray[sizeof($itemArray)-1];
        $collection = $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:Collection')
                        ->find($itemId);

        if (!$collection) {
            throw $this->createNotFoundException('Page not found');
        }

        $products = $this->getDoctrine()
                         ->getRepository('NiftyThriftyShopBundle:Product')
                         ->findByCollection($itemId, array('pageSize' => 12, 'pageNumber' => 1, 'orderBy' => 'productId', 'orderDirection' => 'DESC'));
        $productcount = $this->getDoctrine()
                             ->getRepository('NiftyThriftyShopBundle:Product')
                             ->findCountByCollection($itemId);
        $categories = $this->getDoctrine()
                           ->getRepository('NiftyThriftyShopBundle:ProductCategory')
                           ->findCategoriesInCollection($itemId);

		$sizes = array();
		foreach($categories as $thiscategory){
			$sizes[$thiscategory['productCategoryId']] =  $this->getDoctrine()
                        ->getRepository('NiftyThriftyShopBundle:ProductCategorySize')
                        ->findByCategoryId($thiscategory['productCategoryId']);
		}

        return $this->render('NiftyThriftyShopBundle:Shop:collection.html.twig',
                                array('collection' => $collection,
                                'products' => $products,
                                'categories' => $categories,
                                'productcount' => $productcount,
                                'sizes' => $sizes));
    }

    /**
     * Show lookbook splash
     *
     * @Route("lookbook", name="lookbook_splash")
     */
    public function showLookbookSplash()
    {
		$collections = $this->getDoctrine()
							->getManager()
							->getRepository('NiftyThriftyShopBundle:Collection')
							->findAllActive(3);

        return $this->render('NiftyThriftyShopBundle:Shop:lookbook.html.twig',
                                array('collections' => $collections));
    }


    /**
     * Show shops splash
     *
     * @Route("collaborators", name="shops_splash")
     */
    public function showShopSplash()
    {
		$collections = $this->getDoctrine()
							->getManager()
							->getRepository('NiftyThriftyShopBundle:Collection')
							->findAllShops();
        $categories = $this->getDoctrine()
							->getManager()
							->getRepository('NiftyThriftyShopBundle:ProductCategory')->findNavigation();

        return $this->render('NiftyThriftyShopBundle:Shop:shops.html.twig',
                                array('collections' => $collections,
                                'categories' => $categories));
    }


    /**
     * Show collections splash
     *
     * @Route("collections", name="collections_splash")
     */
    public function showCollectionsSplash()
    {
        $collections = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('NiftyThriftyShopBundle:Collection')
                            ->findActiveNotEndingSoon();

        $endingcollections = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('NiftyThriftyShopBundle:Collection')
                            ->findEndingSoon();

        return $this->render('NiftyThriftyShopBundle:Shop:collections.html.twig',
                                array('collections' => $collections, 'endingcollections' => $endingcollections));
    }
}
