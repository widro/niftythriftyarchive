<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use NiftyThrifty\ShopBundle\Form\StartOrderFromBasketType;

class BasketController extends Controller
{
    /**
     * This gets the number of items that are in the user's current basket.  It will
     * also create a new ongoing empty basket if the user doesn't have one.
     *
     * Called from layout.html.twig::NiftyThriftyShopBundle:Basket:getBasketCount
     */
    public function getBasketCountAction()
    {
        $currentUser = $this->getUser();
        if ($currentUser instanceof \NiftyThrifty\ShopBundle\Entity\User) {
            $em = $this->getDoctrine()->getManager();
            $basket = $em->getRepository('NiftyThriftyShopBundle:Basket')
                         ->findByUserOngoing($currentUser->getUserId());
            // If the user doesn't have an ongoing basket, make it.
            if (!$basket) {
                $basket = $this->_newBasket($em);
            }
            $basket->expireItems($em);
            $itemCount = $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                            ->getItemCountByBasket($basket, $em);
            $description = $itemCount == 1 ? 'item' : 'items';
        } else {
            $itemCount  = 0;
            $description= 'items';
        }

        if ($itemCount) {
            $link = $this->generateUrl('user_my_basket');
            return new Response('<a href="'.$link.'"><span class="items_in_cart" id="items_in_cart">'.$itemCount.'</span> '.$description.'</a>');
        } else {
            return new Response('<span class="items_in_cart" id="items_in_cart">0</span> items');
        }
    }

    /**
     * Generate a user's shopping cart.
     *
     * @Route("/my_basket", name="user_my_basket")
     * @Method({"GET"})
     */
    public function showBasketAction()
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('NiftyThriftyShopBundle:Basket')
                     ->findByUserOngoing($this->getUser()->getUserId());
        $firstOrder = $this->getUser()->getInvoices()->count() == 0;

        if (!$basket) {
            $basket     = $this->_newBasket($em);
            $basketItems= array();
        } else {
            $basketItems= $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                             ->findByBasket($basket, $em);
        }
        $firstOrderDiscount = $firstOrder ? $basket->getBasketItemTotal() * .2 : 0;

        // See if this basket has an order
        $order = $em->getRepository('NiftyThriftyShopBundle:Order')
                    ->findUnpaidByBasket($basket->getBasketId());

        /**
         * Show the beginning of the order stuff
         */
        if (!$order) {
            $basketOrder = new \NiftyThrifty\ShopBundle\Entity\Order();
            $formAction = 'start_order';
        } else {
            $basketOrder = $order;
            $formAction  = 'enter_shipping';
            $formAction = 'start_order';
        }

        // New form
        $basketStartOrderForm = $this->createForm(new StartOrderFromBasketType(),
                                                    $basketOrder,
                                                    array('method' => 'POST',
                                                          'action' => $this->generateUrl($formAction)));
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()){
            return $this->render('NiftyThriftyShopBundle:Basket:showUserBasketAjax.html.twig',
            					 array('basketItems'        => $basketItems,
            						   'user'               => $this->getUser(),
            						   'firstOrderDiscount' => $firstOrderDiscount,
            						   'startOrder'         => $basketStartOrderForm->createView()));
        } else {
            return $this->render('NiftyThriftyShopBundle:Basket:showUserBasket.html.twig',
            					 array('basketItems'        => $basketItems,
            						   'user'               => $this->getUser(),
            						   'firstOrderDiscount' => $firstOrderDiscount,
            						   'startOrder'         => $basketStartOrderForm->createView()));
        }
    }

    /**
     * If a user doesn't have an ongoing basket, we have to make one.
     *
     * @param   $em     EntityManager
     * @return  NiftyThriftyShopBundle:Basket
     */
    private function _newBasket($em)
    {
        $newBasket = new \NiftyThrifty\ShopBundle\Entity\Basket();
        $newBasket->setUser($this->getUser());
        $em->persist($newBasket);
        $em->flush();
        return $newBasket;
    }

    /**
     * Add an item to the shopping cart.
     *
     * @Route("/add_item_to_basket/{productId}", name="add_item_to_basket", requirements={"productId" = "\d+"})
     * @Method({"GET"})
     */
    public function addToBasketAction($productId, Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $currentUser= $this->getUser();
        $product    = $em->getRepository('NiftyThriftyShopBundle:Product')
                         ->find($productId);
        $basket     = $em->getRepository('NiftyThriftyShopBundle:Basket')
                         ->findByUserOngoing($currentUser->getUserId());
        if (!$basket) {
            $basket = $this->_newBasket($em);
        }
        $basket->expireItems($em);

        /**
         * See if there's a favorite record for this item for this user.
         */
        try {
            $lovedProduct = $em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                               ->findByUserAndProduct($this->getUser()->getUserId(), $product->getProductId());
            $lovedProduct->setIsDeleted(0)
                         ->setLoveType(\NiftyThrifty\ShopBundle\Entity\UserLovedProduct::LOVE_TYPE_BASKET);
        } catch (\Doctrine\ORM\NoResultException $e) {
            $nowTime      = new \DateTime();
            $lovedProduct = new \NiftyThrifty\ShopBundle\Entity\UserLovedProduct();
            $lovedProduct->setUserId($this->getUser()->getUserId())
                         ->setProductId($product->getProductId())
                         ->setDateLoved($nowTime)
                         ->setLoveType(\NiftyThrifty\ShopBundle\Entity\UserLovedProduct::LOVE_TYPE_BASKET)
                         ->setUser($this->getUser())
                         ->setProduct($product)
                         ->setIsDeleted(0);
            $em->persist($lovedProduct);
        }
        $em->flush();

        /**
         * In a futre universe, this should all be transactional and, if successful, it should
         * also notify a Java queue that there is an item pending expiration.
         */
        $basketItem = new \NiftyThrifty\ShopBundle\Entity\BasketItem();
        $basketItem->setBasket($basket);
        $basketItem->setProduct($product);
        $basketItem->setBasketItemPrice($product->getProductPrice());
        $basketItem->setBasketItemDiscount(0);
        $basketItem->setBasketItemStatus(\NiftyThrifty\ShopBundle\Entity\BasketItem::VALID);
        $em->persist($basketItem);

        /**
         * Before saving this item to a user's cart, verify it's not currently active in any
         * other user's carts.
         *
         * This should be moved in to an event listener in Doctrine 2.4.  Currently on 2.3
         */
        if ($product->getProductAvailability() != \NiftyThrifty\ShopBundle\Entity\Product::SALE) {
            return new Response("Product can not be reserved.");
        } else {
            $basket->setBasketDateUpdate(new \DateTime("now"));
            $product->setProductAvailability(\NiftyThrifty\ShopBundle\Entity\Product::RESERVED);
            $em->flush();
        }

		if ($request->isXmlHttpRequest()){
			$response = new Response(json_encode(array('productId' => $productId)));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		} else {
			return $this->redirect($request->headers->get('referer'));
		}
    }

    /**
     * Remove an item from an active shopping cart.
     *
     * @Route("/remove_item_from_basket/{productId}", name="remove_item_from_basket", requirements={"productId" = "\d+"})
     * @Method({"GET"})
     */
    public function removeFromBasketAction($productId, Request $request)
    {
        $currentUser= $this->getUser();
        $em         = $this->getDoctrine()->getManager();
        $basket     = $em->getRepository('NiftyThriftyShopBundle:Basket')
                         ->findByUserOngoing($currentUser->getUserId());
        $basketItem = $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                         ->findByBasketAndProduct($em, $basket, $productId);

        // If there's a basket item, Delete it from the basket.
        if ($basketItem) {
            $basketItem->getProduct()->setProductAvailability(\NiftyThrifty\ShopBundle\Entity\Product::SALE);
            $em->remove($basketItem);
            $basket->setBasketDateUpdate(new \DateTime("now"));
            $em->flush();
        }
		if ($request->isXmlHttpRequest()){
			$response = new Response(json_encode(array('basketItem' => $basketItem)));
			$response->headers->set('Content-Type', 'application/json');
			return $response;
		} else {
			return $this->redirect($request->headers->get('referer'));
		}
    }
}
