<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Doctrine\ORM\NoResultException;
use NiftyThrifty\ShopBundle\Form\Type\OrderFormType;
use NiftyThrifty\ShopBundle\Form\Type\OrderFormStep1Type;
use NiftyThrifty\ShopBundle\Form\Type\OrderFormStep2Type;
use NiftyThrifty\ShopBundle\Entity\Address;
use NiftyThrifty\ShopBundle\Entity\Basket;
use NiftyThrifty\ShopBundle\Entity\BasketItem;
use NiftyThrifty\ShopBundle\Entity\Coupon;
use NiftyThrifty\ShopBundle\Entity\Order;
use NiftyThrifty\ShopBundle\Entity\Invoice;
use NiftyThrifty\ShopBundle\Entity\Product;
use NiftyThrifty\ShopBundle\Entity\UserPaymentProfile;
use NiftyThrifty\ShopBundle\Entity\UserCredits;
use NiftyThrifty\ShopBundle\Entity\UserInvitation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

class CheckoutController extends Controller
{
    /**
     * Start the checkout process.  This is only called from the BasketController when a user
     * is viewing their basket (BasketController::showBasketAction)
     *
     * @Route("/start_order", name="start_order")
     */
    public function startOrder(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /**
         * Get the current basket.  If the user somehow gets here with no basket defined, it's URL
         * tomfoolery, so redirect them to homepage.  This should never happen through regular navigation.
         */
        $basket = $em->getRepository('NiftyThriftyShopBundle:Basket')
                     ->findByUserOngoing($this->getUser()->getUserId());
        if (!$basket) return $this->redirect('/');

        /**
         * Don't let the user checkout an empty basket.  Reroute them to their basket page.
         */
        $basketItems= $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                         ->findByBasket($basket, $em);
        if (!sizeof($basketItems)) return $this->redirect($this->generateUrl('user_my_basket'));

        /**
         * If there's an unpaid order, get it.  Otherwise, create a new order object.
         */
        $order  = $em->getRepository('NiftyThriftyShopBundle:Order')
                     ->findUnpaidByBasket($basket->getBasketId());
        if (!$order) $order = new Order();

        /**
         * If this is the user's first order and they haven't selected a coupon,
         * apply the first order discount.
         */
        if (!$this->getUser()->getInvoices()->count()) {
            $order->setOrderAmountCoupon($basket->getBasketItemTotal() * .2);
        } else {
            $order->setOrderAmountCoupon(0);
        }

        $order->setBasket($basket)
              ->setBasketId($basket->getBasketId())
              ->setOrderStatus(Order::STATUS_UNPAID)
              ->setOrderUserFirstName($this->getUser()->getUserFirstName())
              ->setOrderUserLastName($this->getUser()->getUserLastName())
              ->setOrderUserEmail($this->getUser()->getUserEmail())
              ->setOrderUserIpAddress($request->getClientIp())
              ->setOrderAmount($basket->getBasketItemTotal())
              ->setOrderAmountCredits(0)
              ->setOrderAmountVat(0)
              ->setOrderAmountShipping(0)
              ->setCouponId(null)
              ->setCoupon(null)
              ->setOrderProducts($basket->getOrderProductList())
              ->setOrderAmountTotal($order->getOrderTotal());

        // Show the order processing page
        return $this->showOrderFormStep1($order, $basket, $em);
    }

    /**
     * This is the form that asks for the shipping/billing address.
     *
     */
    public function showOrderFormStep1($order, $basket, $em)
    {
        /**
         * See if the user has a saved, default billing and shipping address, if so, pre-populate these fields.  We don't
         * just save address ids here since we want to persist the actual address it was shipped to, not what is stored
         * in the address table at the current moment.
         */
        if ($this->getUser()->getAddressIdShipping()) {
            $shippingAddress = $this->getUser()->getAddressShipping();
            $order->setOrderShippingAddressFirstName($shippingAddress->getAddressFirstName())
                  ->setOrderShippingAddressLastName($shippingAddress->getAddressLastName())
                  ->setOrderShippingAddressStreet($shippingAddress->getAddressStreet())
                  ->setOrderShippingAddressCity($shippingAddress->getAddressCity())
                  ->setOrderShippingAddressState($shippingAddress->getState()->getStateCode())
                  ->setOrderShippingAddressZipcode($shippingAddress->getAddressZipcode())
                  ->setOrderShippingAddressCountry($shippingAddress->getAddressCountry());
        }

        if ($this->getUser()->getAddressIdBilling()) {
            $billingAddress = $this->getUser()->getAddressBilling();
            $order->setOrderBillingAddressFirstName($billingAddress->getAddressFirstName())
                  ->setOrderBillingAddressLastName($billingAddress->getAddressLastName())
                  ->setOrderBillingAddressStreet($billingAddress->getAddressStreet())
                  ->setOrderBillingAddressCity($billingAddress->getAddressCity())
                  ->setOrderBillingAddressState($billingAddress->getState()->getStateCode())
                  ->setOrderBillingAddressZipcode($billingAddress->getAddressZipcode())
                  ->setOrderBillingAddressCountry($billingAddress->getAddressCountry());
        }
        
        // If the user has gone to checkout, extend the basket timeout.
        $basketItems= $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                         ->findByBasket($basket, $em);
        $extendTime = new \DateTime("+20 minutes");
        foreach ($basketItems as $basketItem) {
            $basketItem->setBasketItemDateEnd($extendTime);
        }
        $em->flush();
        $orderForm  = $this->createForm(new OrderFormStep1Type($this->getUser()),
                                          $order,
                                          array('method' => 'POST',
                                                'action' => 'order_form_step_2'));

        // If the addresses are the same, pre-check the duplicate box.
        if ($order->areAddressesDuplicate()) {
            $orderForm->get('orderDuplicateBillingAndShipping')->setData('yes');
        } else {
            $orderForm->get('orderDuplicateBillingAndShipping')->setData('no');
        }

        return $this->render('NiftyThriftyShopBundle:Checkout:orderFormStep1.html.twig',
                             array('orderForm'  => $orderForm->createView(),
                                   'order'      => $order,
                                   'profiles'   => array(),
                                   'basketItems'=> $basketItems));
    }

    /**
     * This is the form that asks for the credit card info.
     *
     * @Route("/order_form_step_2", name="order_form_step_2")
     * @Method({"POST"})
     */
    public function showOrderFormStep2()
    {
        $em         = $this->getDoctrine()->getManager();
        $basket     = $em->getRepository('NiftyThriftyShopBundle:Basket')
                         ->findByUserOngoing($this->getUser()->getUserId());
        if (!$basket) return $this->redirect('/');
        $basketItems= $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                         ->findByBasket($basket, $em);
        if (!sizeof($basketItems)) return $this->redirect($this->generateUrl('user_my_basket'));
        $extendTime = new \DateTime("+20 minutes");
        foreach ($basketItems as $basketItem) {
            $basketItem->setBasketItemDateEnd($extendTime);
        }
        $em->flush();
        $order      = $em->getRepository('NiftyThriftyShopBundle:Order')
                         ->findUnpaidByBasket($basket->getBasketId());
        if (!($order)) $order = new Order();
        $profiles   = $em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')
                         ->findByUserId($this->getUser()->getUserId());
        $request = $this->getRequest();
        $shipper = $this->get('shipping_manager')
                        ->setUser($this->getUser())
                        ->setItemCount(sizeof($basketItems));

        /**
         * See if the user has a saved, default billing and shipping address, if so, pre-populate these fields.  We don't
         * just save address ids here since we want to persist the actual address it was shipped to, not what is stored
         * in the address table at the current moment.
         */
        $orderFormStep1 = $request->request->get('orderFormStep1');
        $order->setOrderShippingAddressFirstName($orderFormStep1['orderShippingAddressFirstName'])
              ->setOrderShippingAddressLastName($orderFormStep1['orderShippingAddressLastName'])
              ->setOrderShippingAddressStreet($orderFormStep1['orderShippingAddressStreet'])
              ->setOrderShippingAddressCity($orderFormStep1['orderShippingAddressCity'])
              ->setOrderShippingAddressState($orderFormStep1['orderShippingAddressState'])
              ->setOrderShippingAddressZipcode($orderFormStep1['orderShippingAddressZipcode'])
              ->setOrderShippingAddressCountry($orderFormStep1['orderShippingAddressCountry']);
        $order->setOrderBillingAddressFirstName($orderFormStep1['orderBillingAddressFirstName'])
              ->setOrderBillingAddressLastName($orderFormStep1['orderBillingAddressLastName'])
              ->setOrderBillingAddressStreet($orderFormStep1['orderBillingAddressStreet'])
              ->setOrderBillingAddressCity($orderFormStep1['orderBillingAddressCity'])
              ->setOrderBillingAddressState($orderFormStep1['orderBillingAddressState'])
              ->setOrderBillingAddressZipcode($orderFormStep1['orderBillingAddressZipcode'])
              ->setOrderBillingAddressCountry($orderFormStep1['orderBillingAddressCountry']);

        $orderCouponAmount = (!$this->getUser()->getInvoices()->count())    ? $basket->getBasketItemTotal() * .2 : 0;
        $salesTax          = $order->getOrderShippingAddressState() == 'NY' ? $basket->calculateSalesTax()       : 0;
        $order->setBasket($basket)
              ->setBasketId($basket->getBasketId())
              ->setOrderStatus(Order::STATUS_UNPAID)
              ->setOrderAmount($basket->getBasketItemTotal())
              ->setOrderAmountCredits(0)
              ->setOrderAmountCoupon($orderCouponAmount)
              ->setOrderAmountVat($salesTax)
              ->setOrderProducts($basket->getOrderProductList())
              ->setOrderAmountShipping(0)
              ->setOrderAmountTotal($order->getOrderTotal());
        $shipper->setOrderTotal($order->getShippingCostTotal());
        $orderForm  = $this->createForm(new OrderFormStep2Type($this->getUser(), $shipper),
                                          $order,
                                          array('method' => 'POST',
                                                'action' => 'review_order'));
        $orderForm->get('orderShippingMethod')->setData('classic');
        $orderForm->get('userCredits')
                  ->setData($em->getRepository('NiftyThriftyShopBundle:UserCredits')
                               ->getUserCreditTotal($this->getUser()->getUserId()));

        return $this->render('NiftyThriftyShopBundle:Checkout:orderFormStep2.html.twig',
                             array('orderForm'  => $orderForm->createView(),
                                   'order'      => $order,
                                   'profiles'   => $profiles,
                                   'basketItems'=> $basketItems));
    }

    /**
     * Save all the order stuff in to an order record, and display it for the user's review.
     *
     * @Route("/review_order", name="review_order")
     * @Method({"POST"})
     */
    public function reviewOrder(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $basket     = $em->getRepository('NiftyThriftyShopBundle:Basket')
                         ->findByUserOngoing($this->getUser()->getUserId());
        if (!$basket) return $this->redirect('/');
        $basketItems= $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                         ->findByBasket($basket, $em);
        if (!sizeof($basketItems)) return $this->redirect($this->generateUrl('user_my_basket'));
        $extendTime = new \DateTime("+20 minutes");
        foreach ($basketItems as $basketItem) {
            $basketItem->setBasketItemDateEnd($extendTime);
        }
        $em->flush();
        $order      = $em->getRepository('NiftyThriftyShopBundle:Order')
                         ->findUnpaidByBasket($basket->getBasketId());
        if (!($order)) $order = new Order();
        $profiles   = $em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')
                         ->findByUserId($this->getUser()->getUserId());
        $cimService = $this->get('authorize_cim');
        $environment = $this->get('kernel')->getEnvironment();
        if (($environment == 'test')) $cimService->setTestMode();
        $shipper    = $this->get('shipping_manager')
                           ->setUser($this->getUser())
                           ->setItemCount(sizeof($basketItems));
                           
        // Set the basket item total.
        $order->setOrderAmount($basket->getBasketItemTotal());

        /**
         * Calculate the sales tax.  The current rule is we only need to even check
         * for sales tax calculation if the shipping address is in NY.
         */
        $salesTax           = $order->getOrderShippingAddressState() == 'NY' ? $basket->calculateSalesTax()         : 0;

        /**
         * Set the Tax and coupon values here if they were selected.
         */
        $order->setOrderAmountVat($salesTax)
              ->setOrderDateCreation(new \DateTime())
              ->setOrderDateEnd(new \DateTime("now +12 hours"))
              ->setBasket($basket)
              ->setBasketId($basket->getBasketId())
              ->setOrderUserFirstName($this->getUser()->getUserFirstName())
              ->setOrderUserLastName($this->getUser()->getUserLastName())
              ->setOrderUserEmail($this->getUser()->getUserEmail())
              ->setOrderStatus(Order::STATUS_UNPAID)
              ->setOrderUserIpAddress($request->getClientIp())
              ->setOrderProducts($basket->getOrderProductList())
              ->setOrderAmount($basket->getBasketItemTotal());
        $order->setOrderAmountTotal($order->getOrderTotal());

        /**
         * When the values are set, process the form.
         */
        $orderForm = $this->createForm(new OrderFormStep2Type($this->getUser(), $shipper),
                                       $order,
                                       array('method' => 'POST',
                                             'action' => 'review_order'));
        $orderForm->handleRequest($request);

        // Process the coupon.  This does not have to exist.
        try {
            $couponCode = $orderForm->get('couponCode')->getData();
            $coupon     = $em->getRepository('NiftyThriftyShopBundle:Coupon')
                             ->findUnexpiredByCouponCode($couponCode);
            $order->setOrderAmountCoupon($coupon->getDiscount($basket->getBasketItemTotal()))
                  ->setCoupon($coupon);
            $shipper->setCoupon($coupon);

        } catch (NoResultException $e) {
            // Only add an error message if coupon code wasn't blank, as blank coupon codes are fine
            if ($couponCode) {
                $orderForm->get('couponCode')->addError(new FormError('Coupon code is invalid'));
                $order->setOrderAmountCoupon(0);
            } else {
                $orderCouponAmount = (!$this->getUser()->getInvoices()->count()) ? $basket->getBasketItemTotal() * .2 : 0;
                $order->setOrderAmountCoupon($orderCouponAmount)
                      ->setCouponId(null)
                      ->setCoupon(null);
            }
        }

        // Shipping
        $shipper->setOrderTotal($order->getShippingCostTotal());
        $order->setOrderShippingMethod($order->getOrderShippingMethod(), $shipper);

        // Credits
        $credits = $em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal($this->getUser()->getUserId());
        if ($credits) {
            $preCreditTotal = $order->getOrderTotalPreCredits();
            $userCredits = $orderForm->get('userCredits')->getData();
            if (is_numeric($userCredits)) {
                // The user can not use more credits than he has.
                if ($userCredits > $credits) {
                    $totalCredits = $credits;

                // A non integer should be floored
                } else if (!is_integer($userCredits)) {
                    $totalCredits = floor($userCredits);
                } else {
                    $totalCredits = $userCredits;
                }
            } else {
                $totalCredits = 0;
            }

            if ($totalCredits > $preCreditTotal) {
                $totalCredits = floor($preCreditTotal);
            }
        } else {
            $totalCredits = 0;
        }
        $order->setOrderAmountCredits($totalCredits);
        $shipper->setOrderTotal($order->getShippingCostTotal());
        $order->setOrderShippingMethod($order->getOrderShippingMethod(), $shipper);
        $order->setOrderAmountTotal($order->getOrderTotal());


        /**
         * If the user does not have a saved customerid but has submitted a saved card, this is an
         * illegal state.  We can't recover from this.
         */
        if ($orderForm->get('savedCardProfileId')->getData() && !$this->getUser()->getAuthorizeNetCustomerId()) {
            $orderForm->get('savedCardProfileId')->addError(new FormError('Saved card profile is invalid.'));
        }

        /**
         * Credit card information is not processed by the because it isn't saved in the order, so
         * check for those issues here.
         */
        if (!$orderForm->get('savedCardProfileId')->getData()) {
            $cardService = $this->get('credit_card_validator');
            $cardService->set(array('cardNumber'    => $orderForm->get('cardNumber')->getData(),
                                    'expireMonth'   => $orderForm->get('expirationDateMonth')->getData(),
                                    'expireYear'    => $orderForm->get('expirationDateYear')->getData(),
                                    'cvv'           => $orderForm->get('securityCode')->getData(),
                                    'cardName'      => $orderForm->get('cardName')->getData()));
            $validator  = $this->get('validator');
            $errors     = $validator->validate($cardService);
            if (sizeof($errors)) {
                foreach ($errors as $error) {
                    $orderForm->get($error->getPropertyPath())->addError(new FormError($error->getMessage()));
                }
            }
        }

        // Save the order
        if ($orderForm->isValid()) {
            $em->persist($order);

            /**
             * Create the profile
             */
            try {
                $cimService = $this->get('authorize_cim');
                $cimService->setCustomer($this->getUser());
                $this->getUser()->setAuthorizeNetCustomerId($cimService->getCustomer()->customerProfileId);
                $em->persist($this->getUser());

                // If we are fetching a saved profile, do it here
                if ($orderForm->get('savedCardProfileId')->getData()) {
                    $profile = $em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')
                                  ->findOneByUserPaymentProfileId($orderForm->get('savedCardProfileId')->getData());
                    $cimService->setPaymentProfileById($profile->getAuthorizeNetProfileId());

                /**
                 * Or else we are processing a new card, which we validated against our validator, but still have
                 * to validate via Authorize.
                 */
                } else {

                    try {
                        $paymentProfile = $cimService->setPaymentProfileByCardService($cardService, $order);

                    /**
                     * It's possible for the user to get in to an invalid state, so if this fails, try to recreate the user
                     * then get the payment profile again.  If it exceptions the second time, then we really do have to fail.
                     */
                    } catch (\AuthorizeNetException $e) {
                        $cimService->regenerateUser($this->getUser());
                        $this->getUser()->setAuthorizeNetCustomerId($cimService->getCustomer()->customerProfileId);
                        $paymentProfile = $cimService->setPaymentProfileByCardService($cardService, $order);
                    }
                }
            } catch (\AuthorizeNetException $e) {
                $orderForm->get('cardNumber')->addError(new FormError($e->getMessage()));
                return $this->render('NiftyThriftyShopBundle:Checkout:orderFormStep2.html.twig',
                                     array('orderForm'      => $orderForm->createView(),
                                           'order'          => $order,
                                           'basketItems'    => $basketItems,
                                           'profiles'       => $profiles,
                                           'invalidCoupon'  => false));
            }

            // If the user selected to save the card, save it.
           if ($orderForm->get('saveCard')->getData()) {
                $userPaymentProfile = new UserPaymentProfile();
                $userPaymentProfile->setUserId($this->getUser()->getUserId())
                                   ->setUser($this->getUser())
                                   ->setCardDigits($cardService->getSavedDigits())
                                   ->setAuthorizeNetProfileId($paymentProfile->customerPaymentProfileId)
                                   ->setExpirationDate($cardService->getFormattedDate());
                $em->persist($userPaymentProfile);
           }
           $em->flush();

            /**
             * This form displays only as the button, the profile stuff saved by authorize and the order
             * number is in hidden fields so we don't have to transmit card data and stuff.
             */
            $formData   = array('customerId' => (int)$cimService->getCustomer()->customerProfileId,
                                'paymentId'  => (int)$cimService->getPaymentProfile()->customerPaymentProfileId,
                                'orderId'    => (int)$order->getOrderId());

            // This is the form that stores everything needed to process the transaction
            $reviewForm = $this->createFormBuilder($formData)
                               ->add('customerId',  'hidden')
                               ->add('paymentId',   'hidden')
                               ->add('orderId',     'hidden')
                               ->add('Submit Order','submit')
                               ->setMethod('POST')
                               ->setAction($this->generateUrl('process_order'))
                               ->getForm();

            // Create a form with orderid and submit button to submit
            return $this->render('NiftyThriftyShopBundle:Checkout:reviewOrder.html.twig',
                                   array('order'            => $order,
                                         'basketItems'      => $basketItems,
                                         'reviewForm'       => $reviewForm->createView()));

        // the form is invalid.
        } else {
            return $this->render('NiftyThriftyShopBundle:Checkout:orderFormStep2.html.twig',
                                 array('orderForm'      => $orderForm->createView(),
                                       'order'          => $order,
                                       'basketItems'    => $basketItems,
                                       'profiles'       => $profiles,
                                       'invalidCoupon'  => false));
        }
    }

    /**
     * Process the order, convert it to an invoice.  This expects three hidden variables to be
     * passed via POST.  Without any of these items, this will fail.
     *      customerId: This is the CIM Customer ID provided by Authorize.net for the user
     *      paymentId:  This is the CIM Payment Profile ID provided by Authorize.net for the given card
     *      orderId:    The order number we are processing.
     * We don't process these with the form interface because they are hidden and should just be there.
     * To note: stupidly, symfony gets POST variables via a function called get().
     * Note 2: This should never be called from anywhere other than the 'review_order' page.
     *
     * @Route("/process", name="process_order")
     * @Method({"POST"})
     */
    public function processOrder(Request $request)
    {
        $orderRequestForm = $request->request->get('form');
        $orderId    = $orderRequestForm['orderId'];
        $customerId = $orderRequestForm['customerId'];
        $paymentId  = $orderRequestForm['paymentId'];
        $mailer     = new \Sailthru_Client($this->container->getParameter('sailthru_api_key'),
                                           $this->container->getParameter('sailthru_api_secret'));
        $environment = $this->get('kernel')->getEnvironment();

        /**
         * You should only ever get here from the review order page, with these three variables
         * properly set from the review order form.  Any other method with these three things
         * unset is probable tomfoolery.
         */
        if (!$orderId || !$customerId || !$paymentId) {
            throw new \Exception('URL Tomfoolery!!');
        }

        $em             = $this->getDoctrine()->getManager();
        $basket         = $em->getRepository('NiftyThriftyShopBundle:Basket')
                             ->findByUserOngoing($this->getUser()->getUserId());
        $order          = $em->getRepository('NiftyThriftyShopBundle:Order')
                             ->find($orderId);
        $paymentProfile = $em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')
                             ->findOneByUserPaymentProfileId($this->getUser()->getUserId());

        /**
         * Only do this if the order total is greater than zero.  It is possible to 
         * have an order of 0 with the credit system.
         */
        if ($order->getOrderAmountTotal() > 0) {
            $cimService = $this->get('authorize_cim');
            if (($environment == 'test')) $cimService->setTestMode();
            try {
                $cimService->setCustomerById($customerId);
                $cimService->setPaymentProfileById($paymentId);
                $cimService->createTransaction($order->getOrderAmountTotal());
                $cimService->executeTransaction();

            /**
             * If the transaction fails, go back to step two where you entered the credit card info
             */
            } catch (\Exception $e) {
                
                // Ignore the duplicate transaction message and display the thank you note.
                $messageBody = "Transaction failed: Order: $orderId, Customer $customerId, Payment: $paymentId\n" . 
                                "User: " . $this->getUser()->getUserEmail() . "\n" . 
                                "Error: " . $e->getMessage() . "\n" . 
                                "Message: " . $e->getTraceAsString();
                $message = \Swift_Message::newInstance()->setSubject('Error processing transaction')
                                                        ->setFrom('authorizeexception@niftythrifty.com')
                                                        ->setTo('tom@niftythrifty.com')
                                                        ->setBody($messageBody);
                $this->get('mailer')->send($message);
                
                /**
                 * If the error we get here is duplicate transaction and the order has been paid, then
                 * we should just display the success message, since it probably means the user just
                 * double clicked the submit button.
                 */
                if (substr_count($e->getMessage(), 'duplicate transaction')) {
                    if ($order->getOrderStatus() == Order::STATUS_PAID) {
                        $invoice = $em->getRepository('NiftyThriftyShopBundle:Invoice')->findOneByOrderId($order->getOrderId());
                        return $this->render('NiftyThriftyShopBundle:Checkout:thankYou.html.twig',
                                             array('invoice' => $invoice));
                    }
                }
                
                /**
                 * Other errors get displayed as normal and the user gets brought back to the card number page.
                 */
                $profiles   = $em->getRepository('NiftyThriftyShopBundle:UserPaymentProfile')
                                 ->findByUserId($this->getUser()->getUserId());
                $basketItems= $em->getRepository('NiftyThriftyShopBundle:BasketItem')
                                 ->findByBasket($basket, $em);
                $shipper    = $this->get('shipping_manager')
                                   ->setUser($this->getUser())
                                   ->setItemCount(sizeof($basketItems))
                                   ->setOrderTotal($order->getShippingCostTotal())
                                   ->setCoupon($order->getCoupon());
                $orderForm  = $this->createForm(new OrderFormStep2Type($this->getUser(), $shipper),
                                                  $order,
                                                  array('method' => 'POST',
                                                        'action' => 'review_order'));
                $orderForm->get('cardName')->addError(new FormError($e->getMessage()));
                
                return $this->render('NiftyThriftyShopBundle:Checkout:orderFormStep2.html.twig',
                                     array('orderForm'      => $orderForm->createView(),
                                           'order'          => $order,
                                           'basketItems'    => $basketItems,
                                           'profiles'       => $profiles,
                                           'invalidCoupon'  => false));
            }
        }
        
        try {
            // Update everything to sold.
            $order->setOrderStatus(Order::STATUS_PAID);
            $basket->setBasketStatus(Basket::PURCHASED);
    
            /**
             * Update any valid items to payment.  Leave expired and deleted items alone.
             */
             $products_html='';
            foreach ($basket->getBasketItems() as $basketItem) {
                if ($basketItem->getBasketItemStatus() == BasketItem::VALID) {
                    $basketItem->setBasketItemStatus(BasketItem::PAYMENT);
                    $basketItem->getProduct()->setProductAvailability(Product::SOLD);
    
    				if($basketItem->getProduct()->getProductVisual1Large()==''){
    					$product_visual1 = $basketItem->getProduct()->getProductVisual1();
    				} else {
    					$product_visual1 = $basketItem->getProduct()->getProductVisual1Large();
    				}
    				
    
                    //build products html for email
    				$products_html .= '<tr height="100" style="border-bottom:1px solid #c0c0c0;">';
    				$products_html .=		'<td>';
    				$products_html .=			'<img width="56" height="56" src="https://d2tqxpnkaovy9w.cloudfront.net/Public/Files/'.$product_visual1.'" alt="'.$basketItem->getProduct()->getProductName().'" title="'.$basketItem->getProduct()->getProductName().'">';
    				$products_html .=		'</td>';
    				$products_html .=		'<td style="color:#878787;font-size:11px;font-family:Verdana;text-align:left;line-height:14px;">';
    				$products_html .=			$basketItem->getProduct()->getProductName().' <br /> '.$basketItem->getProduct()->getProductDescription().' <br />'.$basketItem->getProduct()->getProductMeasurements();
    				$products_html .=		'</td>';
    				$products_html .=		'<td style="color:#878787;font-size:11px;font-family:Verdana;">';
    				$products_html .=			'$ '.$basketItem->getProduct()->getProductPrice();
    				$products_html .=		'</td>';
    				$products_html .=		'<td style="color:#878787;font-size:11px;font-family:Verdana;">';
    				$products_html .=			'$ '.$basketItem->getProduct()->getProductPrice();
    				$products_html .=		'</td></tr>';
                }
            }
            
            // Create the invoice
            $invoice = new Invoice();
            $invoice->setFromOrder($order);
            $invoice->setUser($this->getUser());
            $invoice->setUserId($this->getUser()->getUserId());
            $em->persist($invoice);
    
            // If credits were used, remove them.
            if ($order->getOrderAmountCredits()) {
                $credits = new UserCredits();
                $credits->setNegativeCredits($this->getUser(), $order->getOrderAmountCredits());
                $em->persist($credits);
            }
            $em->flush();

            /**
             * Assuming all the previous happened correctly, mark all "loved" records for these
             * items as deleted.
             */
            foreach ($basket->getBasketItems() as $item) {
    		    $q = $em->createQuery('UPDATE NiftyThrifty\ShopBundle\Entity\UserLovedProduct ul 
    			                          SET ul.isDeleted = 1
    			                        WHERE ul.productId = ?1');
                $q->setParameter(1, $item->getProduct()->getProductId());
                $q->execute();
            }
            $em->flush();

            /**
             * If this is the user's first order, and there's an accepted invitation for the user
             * then credit the inviting user with 25 credits and notify her.
             */
            $invoices = $em->getRepository('NiftyThriftyShopBundle:Invoice')
                           ->findByUserId($this->getUser()->getUserId());
            if (sizeof($invoices) == 1) {
                $invitation = $em->getRepository('NiftyThriftyShopBundle:UserInvitation')
                                 ->findAcceptedByUserId($this->getUser()->getUserId());
                if ($invitation) {
                    $invitation->setUserInvitationStatus(UserInvitation::STATUS_SPEND);
                    $firstBuyCredits = new UserCredits();
                    $firstBuyCredits->setFirstBuyCredits($invitation->getInvitingUser());
                    $em->persist($firstBuyCredits);
                    $em->flush();
                    $mailer->send('transa_invitefirstbuy',
                                  $invitation->getInvitingUser()->getUserEmail(),
                                  array('inviter_first_name'    => $invitation->getInvitingUser()->getUserFirstName(),
                                        'inviter_last_name'     => $invitation->getInvitingUser()->getUserLastName(),
                                        'inviter_totalcredits'  => UserCredits::FIRST_PURCHASE_CREDITS,
                                        'invitee_first_name'    => $this->getUser()->getUserFirstName(),
                                        'invitee_last_name'     => $this->getUser()->getUserLastName()));
                }
            }
    
            /* If the coupon was single use, mark it
            if ($order->getCouponId()) {
    
            }*/
    
            /** Mark this user as free shipping for the next 24 hours.
            $user->freeShipping();
            */
    
    
    		$emailarray = array();
    		$emailarray['shipping_address'] = $invoice->getInvoiceShippingAddressFirstName() . " " . $invoice->getInvoiceShippingAddressLastName()
    		. "<br>" . $invoice->getInvoiceShippingAddressStreet()
    		. "<br>" . $invoice->getInvoiceShippingAddressCity() . ", " . $invoice->getInvoiceShippingAddressState() . " " . $invoice->getInvoiceShippingAddressZipcode();
    
    		$emailarray['billing_address'] = $invoice->getInvoiceBillingAddressFirstName() . " " . $invoice->getInvoiceBillingAddressLastName()
    		. "<br>" . $invoice->getInvoiceBillingAddressStreet()
    		. "<br>" . $invoice->getInvoiceBillingAddressCity() . ", " . $invoice->getInvoiceBillingAddressState() . " " . $invoice->getInvoiceBillingAddressZipcode();
    
    		$emailarray['order_number']             = $invoice->getInvoiceNum();
    		$emailarray['invoice_date']             = $invoice->getInvoiceDate();
    		$emailarray['invoice_amount']           = $invoice->getInvoiceAmount();
    		$emailarray['invoice_amount_shipping']  = $invoice->getInvoiceAmountShipping();
    		$emailarray['invoice_amount_vat']       = $invoice->getInvoiceAmountVat();
    		$emailarray['invoice_amount_coupon']    = $invoice->getInvoiceAmountCoupon();
    		$emailarray['invoice_amount_credits']   = $invoice->getInvoiceAmountCredits();
    		$emailarray['invoice_amount_total']     = $invoice->getInvoiceAmountTotal();
    		$emailarray['products_html']            = $products_html;
    
    		$sendnow = $mailer->send('transa_orderconfirm',$this->getUser()->getUserEmail() ,$emailarray);
            $em->flush();
    
            // Render the thank you screen.
            return $this->render('NiftyThriftyShopBundle:Checkout:thankYou.html.twig',
                                       array('invoice' => $invoice));
        } catch (\Exception $e) {
            $invoiceId = $invoice ? $invoice->getInvoiceId() : 'no invoice';
            $messageBody = "Transaction succeeded but post transaction failed: Invoice: $invoiceId, Order: $orderId, Customer $customerId, Payment: $paymentId\n" . 
                            "User: " . $this->getUser()->getUserEmail() . "\n" . 
                            "Error: " . $e->getMessage() . "\n" . 
                            "Message: " . $e->getTraceAsString();
            $message = \Swift_Message::newInstance()->setSubject('Error processing transaction')
                                                    ->setFrom('authorizeexception@niftythrifty.com')
                                                    ->setTo('tom@niftythrifty.com')
                                                    ->setBody($messageBody);
            $this->get('mailer')->send($message);
        }
    }
}
