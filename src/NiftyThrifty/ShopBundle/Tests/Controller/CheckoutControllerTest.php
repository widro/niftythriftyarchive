<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\MoreBasketItemData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BasketData;
use NiftyThrifty\ShopBundle\Tests\Fixture\OrderData;
use NiftyThrifty\ShopBundle\Tests\Fixture\InvoiceData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserCreditsData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserPaymentProfileData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserInvitationData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;
use NiftyThrifty\ShopBundle\Tests\Fixture\CouponData;
use NiftyThrifty\ShopBundle\Tests\Fixture\AddressData;
use NiftyThrifty\ShopBundle\Entity\UserCredits;

class CheckoutControllerTest extends NiftyBaseTestCase
{
    /**
     * Starting an order with no basket redirects to the homepage.
     */
    public function testStartOrderNoBasket()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/checkout/start_order');
        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    /**
     * Starting an order with an empty basket redirects to the basket page.
     */
    public function testStartOrderEmptyBasket()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/checkout/start_order');
        $this->assertTrue($client->getResponse()->isRedirect('/basket/my_basket'));
    }

    /**
     * We will use this order form for all the step one functions.  DRY
     */
    private function _getStartOrderForm($crawler)
    {
        $startOrder = $crawler->selectButton('Proceed to checkout')
                              ->form(array(), 'POST');
        return $startOrder;
    }
    
    /**
     * We will use this order form for all the step two functions.
     */
    private function _getStepTwoForm($crawler)
    {
        $form = $crawler->selectButton('Continue')
                        ->form(array('orderFormStep1[orderShippingAddressFirstName]'    => 'Step',
                                     'orderFormStep1[orderShippingAddressLastName]'     => 'Two',
                                     'orderFormStep1[orderShippingAddressStreet]'       => '200 Rector Place',
                                     'orderFormStep1[orderShippingAddressCity]'         => 'New York',
                                     'orderFormStep1[orderShippingAddressState]'        => 'NY',
                                     'orderFormStep1[orderShippingAddressZipcode]'      => '12180',
                                     'orderFormStep1[orderShippingAddressCountry]'      => 'USA',
                                     'orderFormStep1[orderDuplicateBillingAndShipping]' => 'no',
                                     'orderFormStep1[orderBillingAddressFirstName]'     => 'StepBill',
                                     'orderFormStep1[orderBillingAddressLastName]'      => 'TwoBill',
                                     'orderFormStep1[orderBillingAddressStreet]'        => '141 89th Street',
                                     'orderFormStep1[orderBillingAddressCity]'          => 'Brooklyn',
                                     'orderFormStep1[orderBillingAddressState]'         => 'NY',
                                     'orderFormStep1[orderBillingAddressZipcode]'       => '11209',
                                     'orderFormStep1[orderBillingAddressCountry]'       => 'USA'),
                               'POST');
        return $form;
    }
    
    /**
     * Get the credit card info form.
     */
    private function _getReviewOrder($crawler, $useSavedProfile=false, $saveCard=false)
    {
        $settingsArray = array('orderForm[orderShippingAddressFirstName]' => 'Step',
                                 'orderForm[orderShippingAddressLastName]'  => 'Two',
                                 'orderForm[orderShippingAddressStreet]'    => '200 Rector Place',
                                 'orderForm[orderShippingAddressCity]'      => 'New York',
                                 'orderForm[orderShippingAddressState]'     => 'NY',
                                 'orderForm[orderShippingAddressZipcode]'   => '12180',
                                 'orderForm[orderShippingAddressCountry]'   => 'USA',
                                 'orderForm[orderBillingAddressFirstName]'  => 'StepBill',
                                 'orderForm[orderBillingAddressLastName]'   => 'TwoBill',
                                 'orderForm[orderBillingAddressStreet]'     => '141 89th Street',
                                 'orderForm[orderBillingAddressCity]'       => 'Brooklyn',
                                 'orderForm[orderBillingAddressState]'      => 'NY',
                                 'orderForm[orderBillingAddressZipcode]'    => '11209',
                                 'orderForm[orderBillingAddressCountry]'    => 'USA',
                                 'orderForm[orderShippingMethod]'           => 'classic',
                                 'orderForm[couponCode]'                    => '',
                                 'orderForm[userCredits]'                   => 0,
                                 'orderForm[savedCardProfileId]'            => '',
                                 'orderForm[cardName]'                      => '',
                                 'orderForm[cardNumber]'                    => '',
                                 'orderForm[expirationDateMonth]'           => '',
                                 'orderForm[expirationDateYear]'            => '',
                                 'orderForm[securityCode]'                  => '');
        if ($useSavedProfile) {
            $settingsArray['orderForm[savedCardProfileId]'] = '1';
        } else {
            $settingsArray['orderForm[cardName]']           = 'Test User';
            $settingsArray['orderForm[cardNumber]']         = '4111111111111111';
            $settingsArray['orderForm[expirationDateMonth]']= '2';
            $settingsArray['orderForm[expirationDateYear]'] = '2020';
            $settingsArray['orderForm[securityCode]']       = '444';
        }

        $form = $crawler->selectButton('Review order')
                        ->form($settingsArray, 'POST');

        if ($saveCard) {
            $form['orderForm[saveCard]'][0]->select('yes');
        }

        return $form;
    }
    
    /**
     * Process an order; We need to create a CIM and Validator class for this.
     */
    private function _getProcessOrder($crawler, $user, $order)
    {
        $cardService    = static::$kernel->getContainer()->get('credit_card_validator');
        $cardService->set(array('cardName'      => 'Test User',
                                'cardNumber'    => '4111111111111111',
                                'expireMonth'   => '2',
                                'expireYear'    => '2020',
                                'cvv'           => '444'));
        $cimService = static::$kernel->getContainer()->get('authorize_cim');
        $customer   = $cimService->setCustomer($user);
        $profile    = $cimService->setPaymentProfileByCustomerProfile();
        $form = $crawler->selectButton('Submit order')
                        ->form(array('form[customerId]' => $customer->customerProfileId,
                                     'form[paymentId]'  => $profile->customerPaymentProfileId,
                                     'form[orderId]'    => $order->getOrderId()),
                               'POST');
        return $form;
    }
    
    /**
     * Start an order with no existing order.
     * 
     * Covers case: User granted 20% off for first order.   (testStartOrderFirstOrder)
     * Covers case: User does not have saved addresses.     (testStartOrderNoSavedAddresses)
     * Covers case: Plural items cell                       (testStartOrderPluralItems)
     * Covers case: No addresses                            (testStartOrderNoAddresses)
     *
     * @covers CheckoutController:startOrderFormStep1
     */
    public function testStartOrderNoOrder()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));

        // The address form stuff should be empty
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressFirstName')->attr('value'),                  '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressLastName')->attr('value'),                   '');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderShippingAddressStreet')->text(),                         '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCity')->attr('value'),                       '');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderShippingAddressState > option[selected]')->attr('value'),  '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressZipcode')->attr('value'),                    '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCountry')->attr('value'),                    'USA');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressFirstName')->attr('value'),                   '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressLastName')->attr('value'),                    '');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderBillingAddressStreet')->text(),                          '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCity')->attr('value'),                        '');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderBillingAddressState > option[selected]')->attr('value'),   '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressZipcode')->attr('value'),                     '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCountry')->attr('value'),                     'USA');

        // The duplicate checkbox should be checked as all blank is technically the same.
        $this->assertCount(1, $crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_0[checked]'));

        // Count/Total cells
        $this->assertContains('2items', $crawler->filter('div#stepOneTotalItemCell')->eq(0)->text());
        $this->assertEquals($crawler->filter('div#stepOneSubtotal > span.price')->text(),   '$25.00');
        $this->assertCount(0, $crawler->filter('div#stepOneVat'));
        $this->assertEquals($crawler->filter('div#stepOneCoupon > span.price')->text(),     '-$5.00');
        $this->assertCount(0, $crawler->filter('div#stepOnePreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepOneCredits'));
        $this->assertEquals($crawler->filter('div#stepOneTotal > span.price')->text(),      '$20.00');
    }

    /**
     * Start an order from an existing order.  Note that step one ignores the saved values in the
     * order.  That includes Saved address data in the order.
     *
     * Covered case: Three items shipping
     * Covered case: Overwriting existing unpaid order record with new data.
     * @covers CheckoutController:startOrderFormStep1
     */
    public function testStartOrderExistingOrder()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new CouponData);
        $this->addFixture(new AddressData);
        $this->addFixture(new InvoiceData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));

        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressFirstName')->attr('value'),                  'Standard');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressLastName')->attr('value'),                   'Shipping');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderShippingAddressStreet')->text(),                         '200 Somewhere Street');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCity')->attr('value'),                       'Brooklyn');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderShippingAddressState > option[selected]')->attr('value'),  'NY');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressZipcode')->attr('value'),                    '11209');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCountry')->attr('value'),                    'USA');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressFirstName')->attr('value'),                   'Standard');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressLastName')->attr('value'),                    'Billing');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderBillingAddressStreet')->text(),                          '200 Somewhere Street');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCity')->attr('value'),                        'Brooklyn');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderBillingAddressState > option[selected]')->attr('value'),   'NY');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressZipcode')->attr('value'),                     '11209');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCountry')->attr('value'),                     'USA');

        // Count/Total cells
        $this->assertContains('3items', $crawler->filter('div#stepOneTotalItemCell')->eq(0)->text());
        $this->assertEquals($crawler->filter('div#stepOneSubtotal > span.price')->text(),   '$37.00');
        $this->assertCount(0, $crawler->filter('div#stepOneVat'));
        $this->assertCount(0, $crawler->filter('div#stepOneCoupon'));
        $this->assertCount(0, $crawler->filter('div#stepOnePreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepOneCredits'));
        $this->assertEquals($crawler->filter('div#stepOneTotal > span.price')->text(),      '$37.00');
    }

    /**
     * Start an order that pre-fills saved address data.  The two dresses are -not- duplicate
     *
     * Covered case: User has saved addresses                           (testStartOrderDifferentAddresses)
     * Covered case: Addresses are not duplicate, button is unchecked.  (testStartOrderDifferentAddresses)
     * @covers CheckoutController:startOrderFormStep1
     */
    public function testStartOrderExistingSavedAddresses()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new CouponData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));

        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressFirstName')->attr('value'),                  'Standard');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressLastName')->attr('value'),                   'Shipping');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderShippingAddressStreet')->text(),                         '200 Somewhere Street');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCity')->attr('value'),                       'Brooklyn');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderShippingAddressState > option[selected]')->attr('value'),  'NY');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressZipcode')->attr('value'),                    '11209');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCountry')->attr('value'),                    'USA');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressFirstName')->attr('value'),                   'Standard');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressLastName')->attr('value'),                    'Billing');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderBillingAddressStreet')->text(),                          '200 Somewhere Street');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCity')->attr('value'),                        'Brooklyn');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderBillingAddressState > option[selected]')->attr('value'),   'NY');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressZipcode')->attr('value'),                     '11209');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCountry')->attr('value'),                     'USA');

        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_0')->attr('value'),             'yes');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_0')->attr('checked'),           '');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_1')->attr('value'),             'no');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_1')->attr('checked'),           'checked');
        // Order stuff duplicates previous test and is omitted.
    }

    /**
     * Test the case where the user has duplicate billing and shipping addresses
     *
     * @covers CheckoutController:startOrderFormStep1
     */
    public function testStartOrderExistingSavedAddressesDuplicate()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        $address = $this->em
                        ->getRepository('NiftyThriftyShopBundle:Address')
                        ->find(1);
        $address->setAddressLastName('Shipping');
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));

        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressFirstName')->attr('value'),                  'Standard');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressLastName')->attr('value'),                   'Shipping');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderShippingAddressStreet')->text(),                         '200 Somewhere Street');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCity')->attr('value'),                       'Brooklyn');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderShippingAddressState > option[selected]')->attr('value'),  'NY');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressZipcode')->attr('value'),                    '11209');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderShippingAddressCountry')->attr('value'),                    'USA');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressFirstName')->attr('value'),                   'Standard');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressLastName')->attr('value'),                    'Shipping');
        $this->assertEquals($crawler->filter('textarea#orderFormStep1_orderBillingAddressStreet')->text(),                          '200 Somewhere Street');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCity')->attr('value'),                        'Brooklyn');
        $this->assertEquals($crawler->filter('select#orderFormStep1_orderBillingAddressState > option[selected]')->attr('value'),   'NY');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressZipcode')->attr('value'),                     '11209');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderBillingAddressCountry')->attr('value'),                     'USA');

        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_0')->attr('value'),             'yes');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_0')->attr('checked'),           'checked');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_1')->attr('value'),             'no');
        $this->assertEquals($crawler->filter('input#orderFormStep1_orderDuplicateBillingAndShipping_1')->attr('checked'),           '');
    }
    
    
    
    /**
     * Step two is pretty straight forward.  Everything from the previous step should be in 
     * hidden fields.  The different cases are only whether or not the user has saved
     * payment profiles or not.  We also have to verify if "standard" shipping is preselcted
     * and if the right standard shipping value is selected.
     */
     
    /**
     * Step 2, No basket.
     */
    public function testStepTwoNoBasket()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/checkout/order_form_step_2');
        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    /**
     * STep 2, empty basker
     */
    public function testStepTwoEmptyBasket()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/checkout/order_form_step_2');
        $this->assertTrue($client->getResponse()->isRedirect('/basket/my_basket'));
    }
    
    /**
     * Test getting to step two with standard shipping preselected for 2 item.
     *
     * Covered case: Shipping cost on two items.
     * Covered case: No saved profiles.
     * Covered case: Tax is not added to items under 110 to NY
     */
    public function testStepTwoTwoItemsNoProfiles()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        
        /**
         * When we submit the step two form, all the stuff from the function defined here should be in
         * the page's hidden fields.
         */
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // The step 2 form should have all the hidden fields from step one (defined i nthe
        // Address stuff
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressFirstName')->attr('value'),                  'Step');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressLastName')->attr('value'),                   'Two');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressStreet')->attr('value'),                     '200 Rector Place');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressCity')->attr('value'),                       'New York');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressState')->attr('value'),                      'NY');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressZipcode')->attr('value'),                    '12180');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingAddressCountry')->attr('value'),                    'USA');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressFirstName')->attr('value'),                   'StepBill');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressLastName')->attr('value'),                    'TwoBill');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressStreet')->attr('value'),                      '141 89th Street');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressCity')->attr('value'),                        'Brooklyn');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressState')->attr('value'),                       'NY');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressZipcode')->attr('value'),                     '11209');
        $this->assertEquals($crawler->filter('input#orderForm_orderBillingAddressCountry')->attr('value'),                     'USA');
        
        // Other hidden
        $this->assertEquals($crawler->filter('input#orderForm_orderAmount')->attr('value'),         '25');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountCredits')->attr('value'),  '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountCoupon')->attr('value'),   '5');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountShipping')->attr('value'), '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountVat')->attr('value'),      '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountTotal')->attr('value'),    '20');

        // Verify the payment profile field is not available.
        $this->assertCount(1, $crawler->filter('select#orderForm_savedCardProfileId > option'));
        $this->assertEquals($crawler->filter('div#paymentProfileSelector')->attr('style'), 'display: none;');
        
        // Verify the correct value for classic shipping is selected ($8)
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_0')->attr('value'),   'classic');
        $this->assertCount(1, $crawler->filter('input#orderForm_orderShippingMethod_0[checked]'));
        $this->assertEquals($crawler->filter('label[for="orderForm_orderShippingMethod_0"]')->text(),   'Classic: $8');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_1')->attr('value'),   'express');
        $this->assertCount(0, $crawler->filter('input#orderForm_orderShippingMethod_1[checked]'));
        
        // Cart info
        $this->assertEquals($crawler->filter('div#stepTwoItemCount > span')->eq(0)->text(), '2');
        $this->assertEquals($crawler->filter('div#stepTwoSubtotal > span.price')->text(),   '$25.00');
        $this->assertCount(0, $crawler->filter('div#stepTwoTax'));
        $this->assertEquals($crawler->filter('div#stepTwoCoupon > span.price')->text(),     '-$5.00');
        $this->assertCount(0, $crawler->filter('div#stepTwoPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCredits'));
        $this->assertCount(0, $crawler->filter('div#stepTwoShipping'));
        $this->assertEquals($crawler->filter('div#stepTwoTotal > span.price')->text(),      '$20.00');
    }
    
    /**
     * Test getting to step two with standard shipping preselected for 3 item.  There should be no tax
     * on any of these items because each item is under
     */
    public function testStepTwoThreeItemsWithProfiles()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new UserPaymentProfileData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        
        /**
         * When we submit the step two form, all the stuff from the function defined here should be in
         * the page's hidden fields.
         */
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));

        $this->assertEquals($crawler->filter('input#orderForm_orderAmount')->attr('value'),         '37');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountCredits')->attr('value'),  '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountShipping')->attr('value'), '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountVat')->attr('value'),      '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountTotal')->attr('value'),    '37');

        // Verify the payment profile field is not available.
        $this->assertCount(4, $crawler->filter('select#orderForm_savedCardProfileId > option'));
        $this->assertCount(1, $crawler->filter('div#paymentProfileSelector'));
        $this->assertEquals($crawler->filter('div#paymentProfileSelector')->attr('style'), '');
        
        // Verify the correct value for classic shipping is selected ($9)
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_0')->attr('value'),   'classic');
        $this->assertCount(1, $crawler->filter('input#orderForm_orderShippingMethod_0[checked]'));
        $this->assertEquals($crawler->filter('label[for="orderForm_orderShippingMethod_0"]')->text(),   'Classic: $9');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_1')->attr('value'),   'express');
        $this->assertCount(0, $crawler->filter('input#orderForm_orderShippingMethod_1[checked]'));

        // Cart info
        $this->assertEquals($crawler->filter('div#stepTwoItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#stepTwoSubtotal > span.price')->text(),   '$37.00');
        $this->assertCount(0, $crawler->filter('div#stepTwoTax'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCoupon'));
        $this->assertCount(0, $crawler->filter('div#stepTwoPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCredits'));
        $this->assertCount(0, $crawler->filter('div#stepTwoShipping'));
        $this->assertEquals($crawler->filter('div#stepTwoTotal > span.price')->text(),      '$37.00');
    }
    
    /**
     * Test getting to step two with standard shipping preselected for one item.
     */
    public function testStepTwoOneItem()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->executeFixtures();
        
        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemStatus('expired');
        $basketItem2 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(2);
        $basketItem2->setBasketItemStatus('expired');
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        
        /**
         * When we submit the step two form, all the stuff from the function defined here should be in
         * the page's hidden fields.
         */
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));

        $this->assertEquals($crawler->filter('input#orderForm_orderAmount')->attr('value'),         '12');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountCredits')->attr('value'),  '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountShipping')->attr('value'), '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountVat')->attr('value'),      '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountTotal')->attr('value'),    '12');

        // Verify the payment profile field is not available.
        $this->assertCount(1, $crawler->filter('select#orderForm_savedCardProfileId > option'));
        $this->assertEquals($crawler->filter('div#paymentProfileSelector')->attr('style'), 'display: none;');
        
        // Verify the correct value for classic shipping is selected ($7)
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_0')->attr('value'),   'classic');
        $this->assertCount(1, $crawler->filter('input#orderForm_orderShippingMethod_0[checked]'));
        $this->assertEquals($crawler->filter('label[for="orderForm_orderShippingMethod_0"]')->text(),   'Classic: $7');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_1')->attr('value'),   'express');
        $this->assertCount(0, $crawler->filter('input#orderForm_orderShippingMethod_1[checked]'));

        // Cart info
        $this->assertEquals($crawler->filter('div#stepTwoItemCount > span')->eq(0)->text(), '1');
        $this->assertEquals($crawler->filter('div#stepTwoSubtotal > span.price')->text(),   '$12.00');
        $this->assertCount(0, $crawler->filter('div#stepTwoTax'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCoupon'));
        $this->assertCount(0, $crawler->filter('div#stepTwoPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCredits'));
        $this->assertCount(0, $crawler->filter('div#stepTwoShipping'));
        $this->assertEquals($crawler->filter('div#stepTwoTotal > span.price')->text(),      '$12.00');
    }
    
    /**
     * Test with an item over $110 in NY adds tax.  This also triggers the cart-value free shipping.
     *
     * Cases covered: Tax charged for items over $110 shipping to NY.
     * Cases covered: Free shipping on carts over $70
     */
    public function testStepTwoAddTaxFreeShipping()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->executeFixtures();

        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemPrice(150);
        $basketItem1->getProduct()->setProductPrice(150);
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        
        /**
         * When we submit the step two form, all the stuff from the function defined here should be in
         * the page's hidden fields.
         */
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));

        $this->assertEquals($crawler->filter('input#orderForm_orderAmount')->attr('value'),         '177');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountCredits')->attr('value'),  '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountShipping')->attr('value'), '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountVat')->attr('value'),      '13.31');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountTotal')->attr('value'),    '190.31');

        // Verify the payment profile field is not available.
        $this->assertCount(1, $crawler->filter('select#orderForm_savedCardProfileId > option'));
        $this->assertEquals($crawler->filter('div#paymentProfileSelector')->attr('style'), 'display: none;');
        
        // Verify the correct value for classic shipping is selected ($0)
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_0')->attr('value'),   'classic');
        $this->assertCount(1, $crawler->filter('input#orderForm_orderShippingMethod_0[checked]'));
        $this->assertEquals($crawler->filter('label[for="orderForm_orderShippingMethod_0"]')->text(),   'Free: $0.00');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_1')->attr('value'),   'express');
        $this->assertCount(0, $crawler->filter('input#orderForm_orderShippingMethod_1[checked]'));

        // Cart info
        $this->assertEquals($crawler->filter('div#stepTwoItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#stepTwoSubtotal > span.price')->text(),   '$177.00');
        $this->assertEquals($crawler->filter('div#stepTwoTax > span.price')->text(),        '$13.31');
        $this->assertCount(0, $crawler->filter('div#stepTwoCoupon'));
        $this->assertCount(0, $crawler->filter('div#stepTwoPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCredits'));
        $this->assertCount(0, $crawler->filter('div#stepTwoShipping'));
        $this->assertEquals($crawler->filter('div#stepTwoTotal > span.price')->text(),      '$190.31');
    }
    
    /**
     * Verify tax is not added to items over $110 being shipped to a different state.
     */
    public function testStepTwoNoTaxOutOfNY()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->executeFixtures();

        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemPrice(150);
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        
        /**
         * When we submit the step two form, all the stuff from the function defined here should be in
         * the page's hidden fields.
         */
        $step2 = $this->_getStepTwoForm($crawler);
        $step2->get('orderFormStep1[orderShippingAddressState]')->setValue('MA');
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));

        $this->assertEquals($crawler->filter('input#orderForm_orderAmount')->attr('value'),         '177');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountCredits')->attr('value'),  '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountShipping')->attr('value'), '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountVat')->attr('value'),      '0');
        $this->assertEquals($crawler->filter('input#orderForm_orderAmountTotal')->attr('value'),    '177');

        // Verify the payment profile field is not available.
        $this->assertCount(1, $crawler->filter('select#orderForm_savedCardProfileId > option'));
        $this->assertEquals($crawler->filter('div#paymentProfileSelector')->attr('style'), 'display: none;');
        
        // Verify the correct value for classic shipping is selected ($0)
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_0')->attr('value'),   'classic');
        $this->assertCount(1, $crawler->filter('input#orderForm_orderShippingMethod_0[checked]'));
        $this->assertEquals($crawler->filter('label[for="orderForm_orderShippingMethod_0"]')->text(),   'Free: $0.00');
        $this->assertEquals($crawler->filter('input#orderForm_orderShippingMethod_1')->attr('value'),   'express');
        $this->assertCount(0, $crawler->filter('input#orderForm_orderShippingMethod_1[checked]'));

        // Cart info
        $this->assertEquals($crawler->filter('div#stepTwoItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#stepTwoSubtotal > span.price')->text(),   '$177.00');
        $this->assertCount(0, $crawler->filter('div#stepTwoTax'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCoupon'));
        $this->assertCount(0, $crawler->filter('div#stepTwoPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#stepTwoCredits'));
        $this->assertCount(0, $crawler->filter('div#stepTwoShipping'));
        $this->assertEquals($crawler->filter('div#stepTwoTotal > span.price')->text(),      '$177.00');
    }
    
    /**
     * This is the review order section.  Review order is responsible for creating the order 
     * object if it doesn't exist or overwriting it with new data if it does.
     */
    public function reviewOrderNoBasket()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/checkout/review_order');
        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }
    
    public function reviewOrderEmptyBasket()
    {
        $this->addFixture(new BasketData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/checkout/review_order');
        $this->assertTrue($client->getResponse()->isRedirect('/basket/my_basket'));
    }
    
    /**
     * Test the review order function if no current order exists, that it is created.  Also test using
     * card data and not a profile while not saving card data.
     *
     * Case covered: Order is created.
     * Case covered: Profile data is not saved.
     * Case covered: Card is processed.
     * Case covered: 20% discount on first order.
     * Case covered: 2 item shipping cost.
     */
    public function testReviewOrderCreateOrderWithUnsavedProfile()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $userPaymentProfiles = $user->getUserPaymentProfiles();

        $this->assertContains('Step Two',           $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('200 Rector Place',   $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('New York NY 12180',  $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('StepBill TwoBill',   $crawler->filter('div.resume_billing > div.resume')->text());
        $this->assertContains('141 89th Street',    $crawler->filter('div.resume_billing > div.resume')->text());
        $this->assertContains('Brooklyn NY 11209',  $crawler->filter('div.resume_billing > div.resume')->text());

        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-6'));
        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-7'));

        // Verify the primary 3 hidden inputs
        $this->assertEquals($crawler->filter('input#form_customerId')->attr('value'),  $user->getAuthorizeNetCustomerId());
        $this->assertEquals($crawler->filter('input#form_orderId')->attr('value'),     '1');
        $this->assertNotEmpty($crawler->filter('input#form_paymentId')->attr('value'));
        $this->assertCount(0, $userPaymentProfiles);

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '2');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$25.00');
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),      '-$5.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$8.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$28.00');
    }

    /**
     * Test the same case as above, but verify a coupon overrides the 20% discount.
     *
     * Case covered: Coupon overrides the 20% discount first order.
     * Case covered: card data is saved.
     */
    public function testReviewOrderCreateOrderCoupon()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->addFixture(new CouponData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler, false, true);
        $reviewOrder->get('orderForm[couponCode]')->setValue('PERCENT');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $userPaymentProfiles = $user->getUserPaymentProfiles();

        $this->assertContains('Step Two',           $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('200 Rector Place',   $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('New York NY 12180', $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('StepBill TwoBill',   $crawler->filter('div.resume_billing > div.resume')->text());
        $this->assertContains('141 89th Street',    $crawler->filter('div.resume_billing > div.resume')->text());
        $this->assertContains('Brooklyn NY 11209', $crawler->filter('div.resume_billing > div.resume')->text());

        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-6'));
        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-7'));

        // Verify the primary 3 hidden inputs
        $this->assertEquals($crawler->filter('input#form_customerId')->attr('value'),  $user->getAuthorizeNetCustomerId());
        $this->assertEquals($crawler->filter('input#form_orderId')->attr('value'),     '1');
        $this->assertNotEmpty($crawler->filter('input#form_paymentId')->attr('value'));
        $this->assertCount(1, $userPaymentProfiles);
        $this->assertEquals($crawler->filter('input#form_paymentId')->attr('value'), $userPaymentProfiles[0]->getAuthorizeNetProfileId());

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '2');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$25.00');
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),      '-$6.25');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$8.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$26.75');
    }
    
    /**
     * Test the review order function updating a current order.  Test that the order is saved in this
     * step.  Also test using card data and saving the profile data.
     *
     * Case covered: Existing order is updated.
     * Case covered: 3 item shipping.
     */
    public function testReviewOrderUpdateOrder()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->executeFixtures();
        $originalOrder = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $this->assertEquals(2, $originalOrder->getOrderId());

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[couponCode]')->setValue('PERCENT');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        $user = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $userPaymentProfiles = $user->getUserPaymentProfiles();

        $this->assertContains('Step Two',           $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('200 Rector Place',   $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('New York NY 12180', $crawler->filter('div.resume_shipping > div.resume')->text());
        $this->assertContains('StepBill TwoBill',   $crawler->filter('div.resume_billing > div.resume')->text());
        $this->assertContains('141 89th Street',    $crawler->filter('div.resume_billing > div.resume')->text());
        $this->assertContains('Brooklyn NY 11209', $crawler->filter('div.resume_billing > div.resume')->text());

        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-1'));
        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-2'));
        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-3'));

        // Verify the primary 3 hidden inputs
        $this->assertEquals($crawler->filter('input#form_customerId')->attr('value'),  $user->getAuthorizeNetCustomerId());
        $this->assertEquals($crawler->filter('input#form_orderId')->attr('value'),     '2');
        $this->assertNotEmpty($crawler->filter('input#form_paymentId')->attr('value'));
        $this->assertCount(3, $userPaymentProfiles);

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$37.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),     '-$9.25');
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$9.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$36.75');

        $this->em->clear();
        $updatedOrder = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);

        // Verify the order has been updated
        $this->assertNotEquals($originalOrder->getOrderShippingAddressFirstName(),  $updatedOrder->getOrderShippingAddressFirstName());
        $this->assertNotEquals($originalOrder->getOrderShippingAddressLastName(),   $updatedOrder->getOrderShippingAddressLastName());
        $this->assertNotEquals($originalOrder->getOrderShippingAddressStreet(),     $updatedOrder->getOrderShippingAddressStreet());
        $this->assertNotEquals($originalOrder->getOrderShippingAddressCity(),       $updatedOrder->getOrderShippingAddressCity());
        $this->assertNotEquals($originalOrder->getOrderShippingAddressZipcode(),    $updatedOrder->getOrderShippingAddressZipcode());
        $this->assertNotEquals($originalOrder->getOrderBillingAddressFirstName(),   $updatedOrder->getOrderBillingAddressFirstName());
        $this->assertNotEquals($originalOrder->getOrderBillingAddressLastName(),    $updatedOrder->getOrderBillingAddressLastName());
        $this->assertNotEquals($originalOrder->getOrderBillingAddressStreet(),      $updatedOrder->getOrderBillingAddressStreet());
        $this->assertNotEquals($originalOrder->getOrderBillingAddressCity(),        $updatedOrder->getOrderBillingAddressCity());
        $this->assertNotEquals($originalOrder->getOrderBillingAddressZipcode(),     $updatedOrder->getOrderBillingAddressZipcode());
        $this->assertNotEquals($originalOrder->getOrderAmount(),                    $updatedOrder->getOrderAmount());
        $this->assertNotEquals($originalOrder->getOrderAmountCoupon(),              $updatedOrder->getOrderAmountCoupon());
        $this->assertNotEquals($originalOrder->getOrderAmountShipping(),            $updatedOrder->getOrderAmountShipping());
        $this->assertNotEquals($originalOrder->getOrderAmountTotal(),               $updatedOrder->getOrderAmountTotal());
    }

    /**
     * Same as above, but test with a saved profile.
    public function testReviewOrderSavedProfile()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->executeFixtures();
        $originalOrder = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $this->assertEquals(2, $originalOrder->getOrderId());

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler, true);
        $crawler = $client->submit($reviewOrder);
        echo $crawler->html();
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-1'));
        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-2'));
        $this->assertCount(1, $crawler->filter('table#basket_table > tr#basket-item-3'));

        // Verify the primary 3 hidden inputs
        $this->assertEquals($crawler->filter('input#form_customerId')->attr('value'),  $user->getAuthorizeNetCustomerId());
        $this->assertEquals($crawler->filter('input#form_orderId')->attr('value'),     '2');
        $this->assertNotEmpty($crawler->filter('input#form_paymentId')->attr('value'));
        $this->assertCount(3, $userPaymentProfiles);

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$37.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),     '-$9.25');
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$9.00');
    }

    /**
     * Same as above, but expire two basket items to check shipping.  Non price related assertions
     * are omitted because they're the same as above.
     */
    public function testReviewOrderSingleItemShipping()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->executeFixtures();
        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemStatus('expired');
        $basketItem2 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(2);
        $basketItem2->setBasketItemStatus('expired');
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[couponCode]')->setValue('PERCENT');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '1');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$12.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),     '-$3.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$7.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$16.00');
    }
    
    /**
     * Test the case where you have entered a coupon that includes free shipping.
     *
     * Case covered: coupon includes free shipping.
     */
    public function testReviewOrderCouponFreeShipping()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[couponCode]')->setValue('EMPLOYEE');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$37.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),     '-$11.10');
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$0.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$25.90');
    }

    /**
     * Verify that a cart over $70 triggers free shipping.  This also should test that sales tax is
     * generated for an item over $110.
     *
     * Case covered: Basket > 70 triggers free shipping.
     * Case covered: Item over $110 in NY triggers sales tax.
     * Case covered: Credits applied.
     */
    public function testReviewOrderFreeShipping()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemPrice(150);
        $basketItem1->getProduct()->setProductPrice(150);
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[couponCode]')->setValue('AMOUNT');
        $reviewOrder->get('orderForm[userCredits]')->setValue('14');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$177.00');
        $this->assertEquals($crawler->filter('div#reviewOrderVat > span.price')->text(),        '$13.31');
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),     '-$35.00');
        $this->assertEquals($crawler->filter('div#reviewOrderPreCreditsTotal > span.price')->text(), '$155.31');
        $this->assertEquals($crawler->filter('div#reviewOrderCredits > span.price')->text(),    '-$14.00');
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$0.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$141.31');
    }

    /**
     * Test the case that a large item outside NY is not charged tax.  Test that Credits
     * are applied.
     *
     * Case covered: Tax exempt outside NY shipping.
     * Case covered: Entering more credits than you have.
     */
    public function testReviewOrderTaxExempt()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemPrice(150);
        $basketItem1->getProduct()->setProductPrice(150);
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $step2->get('orderFormStep1[orderShippingAddressState]')->setValue('MA');
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('20');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$177.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat > span.price'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCoupon > span.price'));
        $this->assertEquals($crawler->filter('div#reviewOrderPreCreditsTotal > span.price')->text(), '$177.00');
        $this->assertEquals($crawler->filter('div#reviewOrderCredits > span.price')->text(),    '-$14.00');
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$0.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$163.00');
    }

    /**
     * Verify that choosing express shipping works correctly.
     *
     * Case covered: Express shipping.
     * Case covered: Non-numeric credits translates to zero.
     */
    public function testReviewOrderExpressShipping()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[orderShippingMethod]')->setValue('express');
        $reviewOrder->get('orderForm[userCredits]')->setValue('tom');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$37.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCoupon'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderPreCreditsTotal'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCredits'));
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$19.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$56.00');
    }

    /**
     * Verify express shipping is still charged on large carts
     */
    public function testReviewOrderExpressShippingLargeCart()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $basketItem1 = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem1->setBasketItemPrice(150);
        $basketItem1->getProduct()->setProductPrice(150);
        $this->em->flush();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[orderShippingMethod]')->setValue('express');
        $reviewOrder->get('orderForm[userCredits]')->setValue('14');
        $reviewOrder->get('orderForm[couponCode]')->setValue('PERCENT');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(), '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),   '$177.00');
        $this->assertEquals($crawler->filter('div#reviewOrderVat > span.price')->text(),        '$13.31');
        $this->assertEquals($crawler->filter('div#reviewOrderCoupon > span.price')->text(),     '-$44.25');
        $this->assertEquals($crawler->filter('div#reviewOrderPreCreditsTotal > span.price')->text(), '$165.06');
        $this->assertEquals($crawler->filter('div#reviewOrderCredits > span.price')->text(),    '-$14.00');
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),   '$19.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),      '$151.06');
    }

    /**
     * Verify that entering more credits than the order becomes the cost of the order
     */
    public function testReviewOrderTooManyCredits()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $userCredit = new UserCredits();
        $startTime = new \DateTime("-1 day");
        $endTime = new \DateTime("+1 day");
        $userCredit->setUserId(1)
                   ->setUserCreditsDate($startTime)
                   ->setUserCreditsDateEnd($endTime)
                   ->setUserCreditsValue(100);
        $this->em->persist($userCredit);
        $this->em->flush();

        $creditCount = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals(114, $creditCount);

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2 = $this->_getStepTwoForm($crawler);
        $crawler = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        
        // Get the step 3 stuff
        $reviewOrder = $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('114');
        $crawler = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Cart info
        $this->assertEquals($crawler->filter('div#reviewOrderItemCount > span')->eq(0)->text(),         '3');
        $this->assertEquals($crawler->filter('div#reviewOrderSubtotal > span.price')->text(),           '$37.00');
        $this->assertCount(0, $crawler->filter('div#reviewOrderVat'));
        $this->assertCount(0, $crawler->filter('div#reviewOrderCoupon'));
        $this->assertEquals($crawler->filter('div#reviewOrderPreCreditsTotal > span.price')->text(),    '$46.00');
        $this->assertEquals($crawler->filter('div#reviewOrderCredits > span.price')->text(),            '-$46.00');
        $this->assertEquals($crawler->filter('div#reviewOrderShipping > span.price')->text(),           '$9.00');
        $this->assertEquals($crawler->filter('div#reviewOrderTotal > span.price')->text(),              '$0.00');
    }
    
    /**
     * Test processing the order, a user's first order.  Verify that the invoice is created as a copy of the
     * order.  Verify the cost that is processed is correct.
     *
     * Case covered: 2 item shipping
     * Case covered: 25 credits for the inviting user.
     * Case covered: Item loves are deleted.
     */
    public function testProcessOrder()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();

        $client     = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(3);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'reserved');

        // Test the loves
        $loved2 = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByProductId(2);
        $this->assertCount(1, $loved2);
        foreach ($loved2 as $loved2Item) $this->assertEquals($loved2Item->getIsDeleted(), 0);
        $loved3 = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByProductId(3);
        $this->assertCount(1, $loved3);
        foreach ($loved3 as $loved3Item) $this->assertEquals($loved3Item->getIsDeleted(), 0);

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $invitingUser   = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(3);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(3);
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#1');
        $this->assertEquals(0, $credits);
        $this->assertEquals(14,$inviterCredits);
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(1);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '3');
        $this->assertEquals($invoice->getBasketId(),                        '3');
        $this->assertEquals($invoice->getCouponId(),                        '');
        $this->assertEquals($invoice->getUserId(),                          '3');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '25');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '5');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '0');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '8');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '28');
        $dateStr = date("Ymd");
        $this->assertEquals($invoice->getInvoiceNum(),                      $dateStr . '-3');
        $this->assertEquals($invoice->getInvoiceStatus(),                   'paid');
        $this->assertEquals($invoice->getInvoiceUserFirstName(),            'Inactive');
        $this->assertEquals($invoice->getInvoiceUserLastName(),             'User');
        $this->assertEquals($invoice->getInvoiceUserEmail(),                'ut_inactive@niftythrifty.com');
        $this->assertEquals($invoice->getInvoiceProducts(),                 '1 | Product One | 10 <br> 2 | Product Two | 15');
        $this->assertEquals($invoice->getInvoiceShippingMethod(),           'classic');
        $this->assertEquals($invoice->getInvoiceShippingAddressFirstName(), 'Step');
        $this->assertEquals($invoice->getInvoiceShippingAddressLastName(),  'Two');
        $this->assertEquals($invoice->getInvoiceShippingAddressStreet(),    '200 Rector Place');
        $this->assertEquals($invoice->getInvoiceShippingAddressCity(),      'New York');
        $this->assertEquals($invoice->getInvoiceShippingAddressState(),     'NY');
        $this->assertEquals($invoice->getInvoiceShippingAddressZipcode(),   '12180');
        $this->assertEquals($invoice->getInvoiceShippingAddressCountry(),   'USA');
        $this->assertEquals($invoice->getInvoiceBillingAddressFirstName(),  'StepBill');
        $this->assertEquals($invoice->getInvoiceBillingAddressLastName(),   'TwoBill');
        $this->assertEquals($invoice->getInvoiceBillingAddressStreet(),     '141 89th Street');
        $this->assertEquals($invoice->getInvoiceBillingAddressCity(),       'Brooklyn');
        $this->assertEquals($invoice->getInvoiceBillingAddressState(),      'NY');
        $this->assertEquals($invoice->getInvoiceBillingAddressZipcode(),    '11209');
        $this->assertEquals($invoice->getInvoiceBillingAddressCountry(),    'USA');
        $this->assertEquals($invoice->getInvoiceShippingStatus(),           'processing');
        $this->assertEquals($invoice->getInvoiceUserIpAddress(),            '127.0.0.1');
        $this->assertNotEmpty($invoice->getInvoiceDate());
        $this->assertInstanceOf('\DateTime', $invoice->getInvoiceDate());

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(3);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(39, $inviterCredits);

        // Test the loves were set to deleted for product 1 and 2 across users.
        $loved1 = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByProductId(1);
        $this->assertCount(2, $loved1);
        foreach ($loved1 as $loved1Item) $this->assertEquals($loved1Item->getIsDeleted(), 1);
        $loved2 = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByProductId(2);
        $this->assertCount(1, $loved2);
        foreach ($loved2 as $loved2Item) $this->assertEquals($loved2Item->getIsDeleted(), 1);
        $loved3 = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByProductId(3);
        $this->assertCount(1, $loved3);
        foreach ($loved3 as $loved3Item) $this->assertEquals($loved3Item->getIsDeleted(), 0);
    }

    /**
     * Test checkout with a coupon instead of a first discount.
     */
    public function testProcessOrderWithCoupon()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();
        $basketItem = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(6);
        $this->em->remove($basketItem);
        $this->em->flush();

        $client     = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[couponCode]')->setValue('PERCENT');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(3);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertCount(1, $basketItems);
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $invitingUser   = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(3);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(3);
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#1');
        $this->assertEquals(0, $credits);
        $this->assertEquals(14,$inviterCredits);
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(1);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '3');
        $this->assertEquals($invoice->getBasketId(),                        '3');
        $this->assertEquals($invoice->getCouponId(),                        '1');
        $this->assertEquals($invoice->getUserId(),                          '3');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '15');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '3.75');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '0');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '7');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '18.25');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(3);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertCount(1, $basketItems);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(39, $inviterCredits);
    }
    
    public function testProcessOrderWithFreeShippingCoupon()
    {
        $this->addFixture(new MoreBasketItemData);
        $this->addFixture(new OrderData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();
        $basketItem = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(6);
        $this->em->remove($basketItem);
        $this->em->flush();

        $client     = $this->getLoggedInTestClient('ut_inactive@niftythrifty.com', 'ut_inactivepass');
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[couponCode]')->setValue('EMPLOYEE');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(3);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertCount(1, $basketItems);
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(3);
        $invitingUser   = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(3);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(3);
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#1');
        $this->assertEquals(0, $credits);
        $this->assertEquals(14,$inviterCredits);
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(1);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '3');
        $this->assertEquals($invoice->getBasketId(),                        '3');
        $this->assertEquals($invoice->getCouponId(),                        '4');
        $this->assertEquals($invoice->getUserId(),                          '3');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '15');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '4.50');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '0');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '0.00');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '10.50');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(3);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertCount(1, $basketItems);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(39, $inviterCredits);
    }

    public function testProcessOrderThreeItemsUseCredits()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();

        $client     = $this->getLoggedInTestClient();
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('8');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#2');
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(2);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '2');
        $this->assertEquals($invoice->getBasketId(),                        '2');
        $this->assertEquals($invoice->getCouponId(),                        '');
        $this->assertEquals($invoice->getUserId(),                          '1');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '37');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '0');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '8');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '9');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '38');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(6, $inviterCredits);
    }

    public function testProcessOrderTaxExempt()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();

        $basketItem = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem->setBasketItemPrice(150);
        $basketItem->getProduct()->setProductPrice(150);
        $this->em->flush();

        $client     = $this->getLoggedInTestClient();
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $step2->get('orderFormStep1[orderShippingAddressState]')->setValue('MA');
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('15');
        $reviewOrder->get('orderForm[couponCode]')->setValue('AMOUNT');
        $reviewOrder->get('orderForm[orderShippingMethod]')->setValue('express');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#2');
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(2);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '2');
        $this->assertEquals($invoice->getBasketId(),                        '2');
        $this->assertEquals($invoice->getCouponId(),                        '2');
        $this->assertEquals($invoice->getUserId(),                          '1');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '177');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '35');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '14');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '19');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '147');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(0, $inviterCredits);
    }

    public function testProcessOrderWithTaxExpressShippingCreditsCoupon()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();

        $basketItem = $this->em->getRepository('NiftyThriftyShopBundle:BasketItem')->find(1);
        $basketItem->setBasketItemPrice(150);
        $basketItem->getProduct()->setProductPrice(150);
        $this->em->flush();

        $client     = $this->getLoggedInTestClient();
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('15');
        $reviewOrder->get('orderForm[couponCode]')->setValue('PERCENT');
        $reviewOrder->get('orderForm[orderShippingMethod]')->setValue('express');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#2');
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(2);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '2');
        $this->assertEquals($invoice->getBasketId(),                        '2');
        $this->assertEquals($invoice->getCouponId(),                        '1');
        $this->assertEquals($invoice->getUserId(),                          '1');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '177');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '13.31');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '44.25');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '14');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '19');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '151.06');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(0, $inviterCredits);
    }

    public function testProcessOrderUseTooManyCredits()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();

        $userCredits = new UserCredits();
        $dateStart = new \DateTime("-1 day");
        $dateEnd   = new \DateTime("+1 day");
        $userCredits->setUserId(1)
                    ->setUserCreditsValue(100)
                    ->setUserCreditsDate($dateStart)
                    ->setUserCreditsDateEnd($dateEnd);
        $this->em->persist($userCredits);
        $this->em->flush();

        $client     = $this->getLoggedInTestClient();
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('114');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#2');
        $this->assertEquals(114, $credits);
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(2);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '2');
        $this->assertEquals($invoice->getBasketId(),                        '2');
        $this->assertEquals($invoice->getCouponId(),                        '');
        $this->assertEquals($invoice->getUserId(),                          '1');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '37');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '0');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '9');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '0');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '46');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(68, $inviterCredits);
    }

    public function testProcessOrderEnterMoreCreditsThanIHave()
    {
        $this->addFixture(new BasketItemData);
        $this->addFixture(new InvoiceData);
        $this->addFixture(new CouponData);
        $this->addFixture(new UserPaymentProfileData);
        $this->addFixture(new UserCreditsData);
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();

        $userCredits = new UserCredits();
        $dateStart = new \DateTime("-1 day");
        $dateEnd   = new \DateTime("+1 day");
        $userCredits->setUserId(1)
                    ->setUserCreditsValue(100)
                    ->setUserCreditsDate($dateStart)
                    ->setUserCreditsDateEnd($dateEnd);
        $this->em->persist($userCredits);
        $this->em->flush();

        $client     = $this->getLoggedInTestClient();
        $crawler    = $client->request('GET', 'basket/my_basket');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $startOrder = $this->_getStartOrderForm($crawler);
        $crawler    = $client->submit($startOrder);
        $this->assertCount(1, $crawler->filter('div#orderStepOne'));
        $step2      = $this->_getStepTwoForm($crawler);
        $crawler    = $client->submit($step2);
        $this->assertCount(1, $crawler->filter('div#orderStepTwo'));
        $reviewOrder= $this->_getReviewOrder($crawler);
        $reviewOrder->get('orderForm[userCredits]')->setValue('120');
        $crawler    = $client->submit($reviewOrder);
        $this->assertCount(1, $crawler->filter('div#reviewOrder'));

        // Test the basket
        $order = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems = $order->getBasket()->getBasketItems();
        $this->assertEquals($order->getOrderStatus(),                               'unpaid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'ongoing');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'reserved');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'valid');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'reserved');

        // Process the order
        $user           = $this->em->getRepository('NiftyThriftyShopBundle:User')->find(1);
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->findUnpaidByBasket(2);
        $credits        = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $processOrder   = $this->_getProcessOrder($crawler, $user, $order);
        $crawler        = $client->submit($processOrder);
        $this->assertCount(1, $crawler->filter('div#orderProcessed'));
        $this->assertEquals($crawler->filter('p#orderThankYou > strong')->text(), '#2');
        $this->assertEquals(114, $credits);
        
        // Check that an invoice has been created.
        $invoice = $this->em->getRepository('NiftyThriftyShopBundle:Invoice')->find(2);
        
        // Check the invoice was created correctly.
        $this->assertEquals($invoice->getOrderId(),                         '2');
        $this->assertEquals($invoice->getBasketId(),                        '2');
        $this->assertEquals($invoice->getCouponId(),                        '');
        $this->assertEquals($invoice->getUserId(),                          '1');
        $this->assertEquals($invoice->getInvoiceAmount(),                   '37');
        $this->assertEquals($invoice->getInvoiceAmountVat(),                '0');
        $this->assertEquals($invoice->getInvoiceAmountCoupon(),             '0');
        $this->assertEquals($invoice->getInvoiceAmountShipping(),           '9');
        $this->assertEquals($invoice->getInvoiceAmountTotal(),              '0');
        $this->assertEquals($invoice->getInvoiceAmountCredits(),            '46');

        $this->em->clear();
        $order          = $this->em->getRepository('NiftyThriftyShopBundle:Order')->find(2);
        $basketItems    = $order->getBasket()->getBasketItems();
        $inviterCredits = $this->em->getRepository('NiftyThriftyShopBundle:UserCredits')->getUserCreditTotal(1);
        $this->assertEquals($order->getOrderStatus(),                               'paid');
        $this->assertEquals($order->getBasket()->getBasketStatus(),                 'purchased');
        $this->assertEquals($basketItems[0]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[0]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[1]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[1]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals($basketItems[2]->getBasketItemStatus(),                 'payment');
        $this->assertEquals($basketItems[2]->getProduct()->getProductAvailability(),'sold');
        $this->assertEquals(68, $inviterCredits);
    }
}
