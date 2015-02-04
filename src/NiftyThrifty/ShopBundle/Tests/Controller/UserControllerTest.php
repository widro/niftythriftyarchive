<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserCreditsData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserInvitationData;
use NiftyThrifty\ShopBundle\Tests\Fixture\AddressData;
use NiftyThrifty\ShopBundle\Tests\Fixture\StateData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;
use NiftyThrifty\ShopBundle\Tests\Fixture\InvoiceData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserLovedProductData;

class UserControllerTest extends NiftyBaseTestCase
{
    /**
     * Test the is logged in action returns the correct thing if the user isn't logged on.  Hitting
     * any of the content controller pages allows unlogged in access, so we will use the
     * about-us page.
     *
     * @covers UserController:isLoggedIn
     */
    public function testIsLoggedInNotLoggedIn()
    {
        $client = static::createClient();
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        // Check the IDs from the logged out partial.
        $this->assertTrue($crawler->filter('a#login_fake')->count() == 1);
        $this->assertTrue($crawler->filter('a#register_fake')->count() == 1);

        // And the logged in partial doesn't exist.
        $this->assertTrue($crawler->filter('div#loginWelcome')->count() == 0);
    }
    
    /**
     * Test the isLoggedIn action when the user is logged in.
     *
     * @covers UserController:isLoggedIn
     * @covers UserController:_getLoggedIn
     */
    public function testIsLoggedInLoggedIn()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());

        // Check the IDs from the logged out partial.
        $this->assertTrue($crawler->filter('a#login_fake')->count() == 0);
        $this->assertTrue($crawler->filter('a#register_fake')->count() == 0);

        // And the logged in partial doesn't exist.
        $this->assertTrue($crawler->filter('span#loginWelcome')->count() == 1);
        $this->assertEquals('Standard!', $crawler->filter('span#loginWelcome')->text());
    }
    
    /**
     * Test logging in fails and shows an error message.
     *
     * @covers UserController:loginInterceptAction
     */
    public function testLoginBadPassword()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client     = static::createClient();
        
        // Get the crawler to the form page.
        $crawler    = $client->request('GET', '/login');
        $loginForm  = $crawler->selectButton('login')
                              ->form(array('userEmail'   => 'ut_user',
                                           'userPassword'=> 'xxxxxxxx'),
                                     'POST');
                                
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($loginForm);
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
        
        // and now verify the error.
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('div#loginForm')->count() == 1);
        $this->assertTrue($crawler->filter('div#loginErrors')->count() == 1);
        
        $this->assertContains('Bad credentials', $crawler->filter('div#loginErrors')->text());
    }
    
    /**
     * Test that login works
     *
     * @covers UserController:loginInterceptAction
     */
    public function testUserLogin()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client     = static::createClient();
        
        // Get the crawler to the form page.
        $crawler    = $client->request('GET', '/login');
        $this->assertCount(1, $crawler->filter('div#loginForm'));
        $loginForm  = $crawler->selectButton('login')
                              ->form(array('userEmail'   => 'ut_user',
                                           'userPassword'=> 'ut_userpass'),
                                     'POST');
        $client->setServerParameter('HTTP_REFERRER', '/');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($loginForm);
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/'));
        
        // Check for log in credentials on the front page.
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('span#loginWelcome')->count() == 1);
        $this->assertEquals($crawler->filter('span#loginWelcome')->text(), 'Standard!');
    }
    
    /**
     * Test the remember me functionality
     *
     * @covers UserController:loginInterceptAction 
     */
    public function testUserLoginRememberMe()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = static::createClient();
        
        // Get the crawler to the form page.
        $crawler = $client->request('GET', '/login');
        $this->assertCount(1, $crawler->filter('div#loginForm'));
        $loginForm = $crawler->selectButton('login')
                             ->form(array('userEmail'   => 'ut_user',
                                          'userPassword'=> 'ut_userpass',
                                          '_remember_me'=> 1),
                                    'POST');
        $client->setServerParameter('HTTP_REFERRER', '/');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($loginForm);
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/'));
        
        // Check for log in credentials on the front page.
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('span#loginWelcome')->count() == 1);
        $this->assertEquals($crawler->filter('span#loginWelcome')->text(), 'Standard!');
        
        // Store the remember me cookie
        $rememberMeCookie = $client->getCookieJar()->get('REMEMBERME');

        // Restart the client and set the remember me cookie.
        $client->restart();
        $client->getCookieJar()->set($rememberMeCookie);
        $newCrawler = $client->request('GET', '/');
        $this->assertEquals($newCrawler->filter('span#loginWelcome')->text(), 'Standard!');

        $newClient = static::createClient();
        $newClient->getCookieJar()->set($rememberMeCookie);
        
        // Going to the front page should still report logged in.
        $newCrawler = $newClient->request('GET', '/');
        $this->assertEquals($newCrawler->filter('span#loginWelcome')->text(), 'Standard!');
    }
    
    /**
     * Test showing the user's account information
     *
     * @covers UserController:showUserAccountAction
     */
   public function testShowUserAccountAction()
    {
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        
        // Add default billing and shipping addresses to test.
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
                     
        $shipping = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Address')
                         ->find(3);
        
        $billing = $this->em
                         ->getRepository('NiftyThriftyShopBundle:Address')
                         ->find(1);

        $user->setAddressShipping($shipping);
        $user->setAddressBilling($billing);
        $this->em->flush();
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/my_account');
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $this->assertContains('ut_user',    $crawler->filter('div#accountUserName')->text());
        $this->assertContains('Standard',   $crawler->filter('div#accountFirstName')->text());
        $this->assertContains('User',       $crawler->filter('div#accountLastName')->text());
        
        $this->assertContains('Standard',               $crawler->filter('div#shippingFirstName')->text());
        $this->assertContains('Shipping',               $crawler->filter('div#shippingLastName')->text());
        $this->assertContains('200 Somewhere Street',   $crawler->filter('div#shippingStreet')->text());
        $this->assertContains('Brooklyn',               $crawler->filter('div#shippingCity')->text());
        $this->assertContains('NY',                     $crawler->filter('div#shippingState')->text());
        $this->assertContains('11209',                  $crawler->filter('div#shippingZip')->text());
        
        $this->assertContains('Standard',               $crawler->filter('div#billingFirstName')->text());
        $this->assertContains('Billing',                $crawler->filter('div#billingLastName')->text());
        $this->assertContains('200 Somewhere Street',   $crawler->filter('div#billingStreet')->text());
        $this->assertContains('Brooklyn',               $crawler->filter('div#billingCity')->text());
        $this->assertContains('NY',                     $crawler->filter('div#billingState')->text());
        $this->assertContains('11209',                  $crawler->filter('div#billingZip')->text());
    }
    
    /**
     * Test that a user must be fully authenticated to see account info
     *
     * @covers UserController:showUserAccountAction
     */
    public function testShowUserAccountActionNeedsLogin()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $rememberMeCookie = $this->getRememberMeCookie();

        $client = static::createClient();
        $client->getCookieJar()->set($rememberMeCookie);
        
        // Accessing the user info page when not fully authenticated should redirect to log in.
        $crawler = $client->request('GET', '/user/my_account');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('div#loginForm')->count() == 1);
    }
    
    /**
     * Test displaying the registration form.  Should work with a user who isn't logged in.
     *
     * @covers UserController:showRegistrationForm
     */
    public function testRegisterForm()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/register');

        $this->assertEquals($crawler->filter('h1')->text(), 'Register');
        $this->assertTrue($crawler->filter('div#registration')->count() == 1);
        $this->assertEquals($crawler->filter('input#registration_tokenType')->attr('value'), null);
        $this->assertEquals($crawler->filter('input#registration_inviteToken')->attr('value'), null);
    }

    /**
     * A logged in user going to the registration form should redirect to the home page
     */
    public function testRegisterFormLoggedIn()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/register');

        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }
    
    /**
     * Registering a user should add the user to the database
     *
     * @covers UserController:registerUser
     */
    public function testRegisterUserPasses()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('newuser@niftythrifty.com');
        $this->assertCount(0, $user);

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'newuser@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('newuser@niftythrifty.com');
        $this->assertCount(1, $user);
        
        $this->assertEquals($user[0]->getUserFirstName(),   'New');
        $this->assertEquals($user[0]->getUserLastName(),    'User');
        $this->assertEquals($user[0]->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
    }
    
    /**
     * Failure should display the same page with errors.
     *
     * @covers UserController:registerUser
     */
    public function testRegisterUserFails()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('newuser@niftythrifty.com');
        $this->assertCount(0, $user);

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'newuser@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'xxxxxxxx'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('newuser@niftythrifty.com');
        $this->assertCount(0, $user);
        
        $this->assertEquals($crawler->filter('h1')->text(), 'Register');
        $this->assertContains('The password fields must match.', $crawler->text());
    }

    /**
     * Registering a user if there is already a logged in user should redirect
     */
    public function testRegisterUserLoggedInRedirects()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/register_user');

        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    /**
     * Show the recover password form with a user populates the e-mail address form
     *
     * @covers UserController::recoverPasswordForm
     */
    public function testRecoverPasswordFormWithUser()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/recover_password');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#recoverPassword'));
        $this->assertEquals(trim($crawler->filter('div#recoverPassword > h1')->text()), 'Recover Password');
        $this->assertEquals($crawler->filter('input#form_userEmail')->attr('value'), 'ut_user');
    }

    /**
     * Test the recover password form with an anonymous user.
     *
     * @covers UserController::recoverPasswordForm
     */
    public function testRecoverPasswordFormNoUser()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/recover_password');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#recoverPassword'));
        $this->assertEquals(trim($crawler->filter('div#recoverPassword > h1')->text()), 'Recover Password');
        $this->assertEquals($crawler->filter('input#form_userEmail')->attr('value'), null);
    }

    /**
     * Submitting the recover password form when the e-mail address exists.
     *
     * @covers UserController::recoverPasswordForm
     * @covers UserController::recoverPassword
     */
    public function testRecoverPasswordUserExists()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(3);
        $oldPassword = $user->getUserPassword();

        $client = static::createClient();
        $client->enableProfiler();
        $crawler = $client->request('GET', '/user/recover_password');
        $recover = $crawler->selectButton('Recover')
                           ->form(array('form[userEmail]' => 'ut_inactive@niftythrifty.com'),
                                  'POST');
        $crawler = $client->submit($recover);
        //$mailCollector = $client->getProfile()->getCollector('sailthru');
        //$this->assertEquals(1, $mailCollector->getMessageCount());
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#passwordRecovered > div#passwordMessage'));

        $this->em->clear();
        $newUser = $this->em
                        ->getRepository('NiftyThriftyShopBundle:User')
                        ->find(3);
        $this->assertNotEquals($newUser->getUserPassword(), $oldPassword);
    }

    /**
     * Submitting the recover password form when the e-mail address doesn't exist.
     *
     * @covers UserController::recoverPasswordForm
     * @covers UserController::recoverPassword
     */
    public function testRecoverPasswordUserDoesNotExist()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $crawler = $client->request('GET', '/user/recover_password');
        $recover = $crawler->selectButton('Recover')
                           ->form(array('form[userEmail]' => 'userdoesnotexist@niftythrifty.com'),
                                  'POST');
        $crawler = $client->submit($recover);
        //$mailCollector = $client->getProfile()->getCollector('sailthru');
        //$this->assertEquals(1, $mailCollector->getMessageCount());
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#recoverPassword'));
        $this->assertEquals(trim($crawler->filter('div#recoverPassword > h1')->text()), 'Recover Password');
        $this->assertEquals($crawler->filter('input#form_userEmail')->attr('value'), 'userdoesnotexist@niftythrifty.com');
        $this->assertContains('No account is registered for this e-mail address.', $crawler->text());
    }

    /**
     * Test the shipping address form is displayed.
     *
     * @covers UserController:editAddressFormAction
     */
    public function testEditAddressFormActionShipping()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/change_address/shipping');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('div.account > h2')->text(), 'Update Shipping Address');
        $this->assertTrue($crawler->filter('div#address')->count() == 1);
    }

    /**
     * Test the billing address form is displayed.
     *
     * @covers UserController:editAddressFormAction
     */
    public function testEditAddressFormActionBilling()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/change_address/billing');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('div.account > h2')->text(), 'Update Billing Address');
        $this->assertTrue($crawler->filter('div#address')->count() == 1);
    }

    /**
     * Test that something other than billing or shipping is a 404 error.
     *
     * @covers UserController:editAddressFormAction
     */
    public function testEditAddressFormActionOtherFails()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/change_address/xxxxxx');
        $this->assertEquals('404', $client->getResponse()->getStatusCode());
    }

    /**
     * Test that full authentication is required.
     *
     * @covers UserController:editAddressFormAction
     */
    public function testEditAddressFormActionFullyAuthenticated()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('GET', '/user/change_address/shipping');

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    /**
     * Update a user's empty shipping address with a new address
     *
     * @covers UserController
     */
    public function testUpdateAddressNewShipping()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new StateData);
        $this->executeFixtures();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertNull($user->getAddressIdShipping());

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/change_address/shipping');
        $register= $crawler->selectButton('Save')
                           ->form(array('address[addressFirstName]' => 'New',
                                        'address[addressLastName]'  => 'Address',
                                        'address[addressStreet]'    => '123 New Street',
                                        'address[addressCity]'      => 'Queens',
                                        'address[state]'            => '1',
                                        'address[addressZipcode]'   => '12345',
                                        'address[addressCountry]'   => 'USA',
                                        'address[userId]'           => '1',
                                        'address[addressType]'      => 'shipping'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/user/my_account'));
        $this->em->clear();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertNotNull($user->getAddressIdShipping());
        $this->assertEquals($user->getAddressShipping()->getAddressFirstName(), 'New');
        $this->assertEquals($user->getAddressShipping()->getAddressLastName(),  'Address');
        $this->assertEquals($user->getAddressShipping()->getAddressStreet(),    '123 New Street');
        $this->assertEquals($user->getAddressShipping()->getAddressCity(),      'Queens');
        $this->assertEquals($user->getAddressShipping()->getStateId(),          '1');
        $this->assertEquals($user->getAddressShipping()->getAddressZipcode(),   '12345');
        $this->assertEquals($user->getAddressShipping()->getAddressCountry(),   'USA');
        $this->assertEquals($user->getAddressShipping()->getUserId(),           '1');
    }
    
    /**
     * If a user already has a shipping address, update it.
     *
     * @covers UserController
     */
    public function testUpdateAddressUpdateShipping()
    {
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals($user->getAddressIdShipping(), 3);
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/change_address/shipping');
        $register= $crawler->selectButton('Save')
                           ->form(array('address[addressFirstName]' => 'New',
                                        'address[addressLastName]'  => 'Address',
                                        'address[addressStreet]'    => '123 New Street',
                                        'address[addressCity]'      => 'Queens',
                                        'address[state]'            => '1',
                                        'address[addressZipcode]'   => '12345',
                                        'address[addressCountry]'   => 'USA',
                                        'address[userId]'           => '1',
                                        'address[addressType]'      => 'shipping'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/user/my_account'));
        $this->em->clear();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals(3, $user->getAddressIdShipping());
        $this->assertEquals($user->getAddressShipping()->getAddressFirstName(), 'New');
        $this->assertEquals($user->getAddressShipping()->getAddressLastName(),  'Address');
        $this->assertEquals($user->getAddressShipping()->getAddressStreet(),    '123 New Street');
        $this->assertEquals($user->getAddressShipping()->getAddressCity(),      'Queens');
        $this->assertEquals($user->getAddressShipping()->getStateId(),          '1');
        $this->assertEquals($user->getAddressShipping()->getAddressZipcode(),   '12345');
        $this->assertEquals($user->getAddressShipping()->getAddressCountry(),   'USA');
        $this->assertEquals($user->getAddressShipping()->getUserId(),           '1');
    }
    
    /**
     * Test adding a new billing address
     *
     * @covers UserController
     */
    public function testUpdateAddressNewBilling()
    {
        $this->addFixture(new UserData);
        $this->addFixture(new StateData);
        $this->executeFixtures();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertNull($user->getAddressIdBilling());

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/change_address/billing');
        $register= $crawler->selectButton('Save')
                           ->form(array('address[addressFirstName]' => 'New',
                                        'address[addressLastName]'  => 'Address',
                                        'address[addressStreet]'    => '123 New Street',
                                        'address[addressCity]'      => 'Queens',
                                        'address[state]'            => '1',
                                        'address[addressZipcode]'   => '12345',
                                        'address[addressCountry]'   => 'USA',
                                        'address[userId]'           => '1',
                                        'address[addressType]'      => 'billing'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/user/my_account'));
        $this->em->clear();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertNotNull($user->getAddressIdBilling());
        $this->assertEquals($user->getAddressBilling()->getAddressFirstName(), 'New');
        $this->assertEquals($user->getAddressBilling()->getAddressLastName(),  'Address');
        $this->assertEquals($user->getAddressBilling()->getAddressStreet(),    '123 New Street');
        $this->assertEquals($user->getAddressBilling()->getAddressCity(),      'Queens');
        $this->assertEquals($user->getAddressBilling()->getStateId(),          '1');
        $this->assertEquals($user->getAddressBilling()->getAddressZipcode(),   '12345');
        $this->assertEquals($user->getAddressBilling()->getAddressCountry(),   'USA');
        $this->assertEquals($user->getAddressBilling()->getUserId(),           '1');
    }

    /**
     * Test updating the billing address changes the address
     *
     * @covers UserController:updateAddress
     */
    public function testUpdateAddressUpdateBilling()
    {
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals($user->getAddressIdBilling(), 1);
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/change_address/shipping');
        $register= $crawler->selectButton('Save')
                           ->form(array('address[addressFirstName]' => 'New',
                                        'address[addressLastName]'  => 'Address',
                                        'address[addressStreet]'    => '123 New Street',
                                        'address[addressCity]'      => 'Queens',
                                        'address[state]'            => '1',
                                        'address[addressZipcode]'   => '12345',
                                        'address[addressCountry]'   => 'USA',
                                        'address[userId]'           => '1',
                                        'address[addressType]'      => 'billing'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/user/my_account'));
        $this->em->clear();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals(1, $user->getAddressIdBilling());
        $this->assertEquals($user->getAddressBilling()->getAddressFirstName(), 'New');
        $this->assertEquals($user->getAddressBilling()->getAddressLastName(),  'Address');
        $this->assertEquals($user->getAddressBilling()->getAddressStreet(),    '123 New Street');
        $this->assertEquals($user->getAddressBilling()->getAddressCity(),      'Queens');
        $this->assertEquals($user->getAddressBilling()->getStateId(),          '1');
        $this->assertEquals($user->getAddressBilling()->getAddressZipcode(),   '12345');
        $this->assertEquals($user->getAddressBilling()->getAddressCountry(),   'USA');
        $this->assertEquals($user->getAddressBilling()->getUserId(),           '1');
    }
    
    /**
     * Test a failure reloads the form.
     *
     * @covers UserController:updateAddress
     */
    public function testUpdateAddressFails()
    {
        $this->addFixture(new AddressData);
        $this->executeFixtures();
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/change_address/shipping');
        $register= $crawler->selectButton('Save')
                           ->form(array('address[addressFirstName]' => 'New',
                                        'address[addressLastName]'  => 'Address',
                                        'address[addressStreet]'    => '123 New Street',
                                        'address[addressCity]'      => 'Queens',
                                        'address[state]'            => '1',
                                        'address[addressZipcode]'   => '123456',
                                        'address[addressCountry]'   => 'USA',
                                        'address[userId]'           => '1',
                                        'address[addressType]'      => 'shipping'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue($crawler->filter('div#address')->count() == 1);
    }

    /**
     * Test that updating address requires full authentication
     *
     * @covers UserController:updateAddress
     */
    public function testUpdateAddressFullAuthentication()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('POST', '/user/update_address');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    /**
     * Display the edit account info form.
     *
     * @covers UserController:editAccountInfoFormAction
     */
    public function testEditAccountInfoFormDisplay()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/edit_account_info');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('h1')->text(), 'Update Account Info');
        $this->assertTrue($crawler->filter('div#userInfo')->count() == 1);
    }
    
    /**
     * The edit account info form should require full authentication.
     *
     * @covers UserController:editAccountInfoFormAction
     */
    public function testEditAccountInfoRequiresFullAuth()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('POST', '/user/edit_account_info');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    /**
     * Assert that updating the user's account succeeds
     *
     * @covers UserController:updateAccountInfoAction
     */
    public function testUpdateAccountInfoSuccess()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals($user->getUserFirstName(),  'Standard');
        $this->assertEquals($user->getUserLastName(),   'User');
        $this->assertEquals($user->getUserEmail(),      'ut_user');

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/edit_account_info');
        $update  = $crawler->selectButton('Save')
                           ->form(array('userInfo[userFirstName]' => 'New',
                                        'userInfo[userLastName]'  => 'Username',
                                        'userInfo[userEmail]'     => 'test@niftythrifty.com'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($update);
        $this->assertTrue($client->getResponse()->isRedirect('/user/my_account'));
        $this->em->clear();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals($user->getUserFirstName(),  'New');
        $this->assertEquals($user->getUserLastName(),   'Username');
        $this->assertEquals($user->getUserEmail(),      'test@niftythrifty.com');
    }
    
    /**
     * Test updating the account info when it fails.
     *
     * @covers UserController:updateAccountInfoAction
     */
    public function testUpdateAccountInfoFails()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/edit_account_info');
        $update  = $crawler->selectButton('Save')
                           ->form(array('userInfo[userFirstName]' => 'New',
                                        'userInfo[userLastName]'  => 'Username',
                                        'userInfo[userEmail]'     => 'test@test@test.com'),
                                  'POST');
        
        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($update);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('h1')->text(), 'Update Account Info');
        $this->assertTrue($crawler->filter('div#userInfo')->count() == 1);
    }
    
    /**
     * Full auth is required to update account info
     *
     * @covers UserController:updateAccountInfoAction
     */
    public function testUpdateAccountRequiresFullAuth()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('POST', '/user/update_account_info');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    /**
     * Test displaying the password change form.
     *
     * @covers UserController:editPasswordFormAction
     */
    public function testEditPasswordFormDisplay()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/edit_password');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(trim($crawler->filter('div#user_account > h1')->text()), 'Change Password');
        $this->assertTrue($crawler->filter('div#changePassword')->count() == 1);
    }
    
    /**
     * Test displaying the password change form requires full auth.
     *
     * @covers UserController:editPasswordFormAction
     */
    public function testEditPasswordFormDisplayRequiresFullAuth()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('POST', '/user/edit_password');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }
    
    /**
     * Test updating the password succeeds.
     *
     * @covers UserController:updatePasswordFormAction
     */
    public function testUpdatePasswordSuccess()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals($user->getUserPassword(), 'ee59fc9d98f6e781f7063396ca7489f9e2e05b34');

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/edit_password');
        $update  = $crawler->selectButton('Save')
                           ->form(array('changePassword[currentPassword]'     => 'ut_userpass',
                                        'changePassword[userPassword][first]' => 'testuser',
                                        'changePassword[userPassword][second]'=> 'testuser'),
                                  'POST');

        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($update);
        $this->assertTrue($client->getResponse()->isRedirect('/user/my_account'));
        $this->em->clear();
        
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertEquals($user->getUserPassword(), '45c571a156ddcef41351a713bcddee5ba7e95460');
    }
    
    /**
     * Test that updating the password fails.
     *
     * @covers UserController:updatePasswordFormAction
     */
    public function testUpdatePasswordFails()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('POST', '/user/edit_password');
        $update  = $crawler->selectButton('Save')
                           ->form(array('changePassword[currentPassword]'     => 'ut_userpass',
                                        'changePassword[userPassword][first]' => 'testuser',
                                        'changePassword[userPassword][second]'=> 'xxxxx'),
                                  'POST');

        // Verify the submission and the redirect back to the login page with errors.
        $crawler = $client->submit($update);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals(trim($crawler->filter('div#user_account > h1')->text()), 'Change Password');
        $this->assertTrue($crawler->filter('div#changePassword')->count() == 1);
    }

    /**
     * Updating passwords requires full auth.
     *
     * @covers UserController:updatePasswordFormAction
     */
    public function testUpdatePasswordRequiresFullAuth()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('POST', '/user/update_password');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    /**
     * Test displaying the user's order history
     *
     * @UserController:viewOrderHistoryAction
     */
    public function testViewOrderHistory()
    {
        $this->addFixture(new InvoiceData);
        $this->executeFixtures();
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/my_orders');
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $this->assertTrue($crawler->filter('div#noOrders')->count()     == 0);
        $this->assertTrue($crawler->filter('div#orderList')->count()    == 1);
        
        $this->assertTrue($crawler->filter('div#invoice1')->count() == 1);
        $this->assertContains('1',          $crawler->filter('div#invoice1_orderId')->text());
        $this->assertNotNull($crawler->filter('div#invoice1_orderDate')->text());
        $this->assertContains('shipped',    $crawler->filter('div#invoice1_status')->text());
    }

    /**
     * Test displaying the user's order history if there are no orders
     *
     * @UserController:viewOrderHistoryAction
     */
    public function testViewOrderHistoryNoOrders()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/my_orders');
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $this->assertTrue($crawler->filter('div#noOrders')->count()     == 1);
        $this->assertTrue($crawler->filter('div#orderList')->count()    == 0);
    }

    /**
     * Test the invite friends page when there are invites in the database.
     *
     * @covers UserController::inviteFriendsAction
     */
    public function testInviteFriendFormWithInviteList()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#user_invite'));
        $this->assertEquals('$14', trim($crawler->filter('span#totalUserCredits')->text()));

        // Verify the invite link
        $this->assertContains('user/share/b4d7b6b9f0-1', $crawler->filter('input#form_inviteLink')->attr('value'));

        // Verify the message text was prepopulated
        $this->assertNotEmpty($crawler->filter('textarea#form_inviteText')->text());

        // Check the table of this user's (id: 1) invites
        $this->assertTrue($crawler->filter('div#noInvitationsSent')->count() == 0);
        $invitationRows = $crawler->filter('div#invitationList')->children();
        $this->assertCount(5, $invitationRows);

        $this->assertEquals($invitationRows->eq(0)->attr('id'), 'invitation4');
        $this->assertEquals($invitationRows->eq(1)->attr('id'), 'invitation2');
        $this->assertEquals($invitationRows->eq(2)->attr('id'), 'invitation1');
        $this->assertEquals($invitationRows->eq(3)->attr('id'), 'invitation3');
        $this->assertEquals($invitationRows->eq(4)->attr('id'), 'invitation5');
    }

    /**
     * Test the invite friends page when there aren't invites in the database.
     *
     * @covers UserController::inviteFriendsAction
     */
    public function testInviteFriendsFormNoInvites()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#user_invite'));
        $this->assertEquals('$0', trim($crawler->filter('span#totalUserCredits')->text()));

        // Verify the invite link
        $this->assertContains('user/share/b4d7b6b9f0-1', $crawler->filter('input#form_inviteLink')->attr('value'));

        // Verify the message text was prepopulated
        $this->assertNotEmpty($crawler->filter('textarea#form_inviteText')->text());

        // Check the table of this user's (id: 1) invites
        $this->assertTrue($crawler->filter('div#invitationList > div#noInvitationsSent')->count() == 1);
        $this->assertTrue($crawler->filter('div#invitationList > invitation1')->count() == 0);
    }

    /**
     * Test that you need full authentication to access the invite friends form.
     *
     * @covers UserController::inviteFriendsAction
     */
    public function testInviteFriendRequiresFullAuth()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $client->getCookieJar()->set($this->getRememberMeCookie());
        $crawler = $client->request('GET', '/user/invite_friend');
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));
    }

    /**
     * Test the invitiation script.
     * TODO: Checking the e-mail was sent still needs to be checked.
     *
     * @covers UserController::inviteFriends
     */
    public function testInviteFriends()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertCount(5, $user->getUserInvitations());

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $inviteForm = $crawler->selectButton('Invite')
                              ->form(array('form[inviteLink]'       => 'https://www.niftythrifty.com',
                                           'form[emailAddresses]'   => 'newinvite1@niftythrifty.com, newinvite2@niftythrifty.com',
                                           'form[inviteText]'       => 'You are invited to nifty thrifty.'),
                                     'POST');
        $crawler = $client->submit($inviteForm);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('span#inviteCount')->text(), 2);
        $this->assertTrue($crawler->filter('div#invalidEmails')->count() == 0);

        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertCount(7, $user->getUserInvitations());
        $newInvite1 = $this->em
                           ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                           ->findOneByUserInvitationEmail('newinvite1@niftythrifty.com');
        $this->assertEquals($newInvite1->getUserInvitationStatus(), 'pending');
        $this->assertEquals($newInvite1->getUserInvitationType(),   'mail');
        $this->assertEquals($newInvite1->getUserInvitationEmail(),  'newinvite1@niftythrifty.com');
        $this->assertEquals($newInvite1->getUserId(),               1);
        $newInvite2 = $this->em
                           ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                           ->findOneByUserInvitationEmail('newinvite2@niftythrifty.com');
        $this->assertEquals($newInvite2->getUserInvitationStatus(), 'pending');
        $this->assertEquals($newInvite2->getUserInvitationType(),   'mail');
        $this->assertEquals($newInvite2->getUserInvitationEmail(),  'newinvite2@niftythrifty.com');
        $this->assertEquals($newInvite2->getUserId(),               1);
    }

    /**
     * Invite form submission fails if no e-mail addresses were included.
     *
     * @covers UserController::inviteFriends
     */
    public function testInviteFriendsFailsNoAddresses()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $inviteForm = $crawler->selectButton('Invite')
                              ->form(array('form[inviteLink]'       => 'https://www.niftythrifty.com',
                                           'form[emailAddresses]'   => '',
                                           'form[inviteText]'       => 'You are invited to nifty thrifty.'),
                                     'POST');
        $crawler = $client->submit($inviteForm);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#user_invite'));
        $this->assertContains('At least one e-mail address must be provided.', $crawler->text());
    }

    /**
     * Invite form submission fails if any addresses are invalid.
     *
     * @covers UserController::inviteFriends
     */
    public function testInviteFriendsFailsInvalidAddresses()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $inviteForm = $crawler->selectButton('Invite')
                              ->form(array('form[inviteLink]'       => 'https://www.niftythrifty.com',
                                           'form[emailAddresses]'   => 'newinvitation1@niftythrifty.com, test',
                                           'form[inviteText]'       => 'You are invited to nifty thrifty.'),
                                     'POST');
        $crawler = $client->submit($inviteForm);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#user_invite'));
        $this->assertContains('Invitations could not be sent because not all e-mail addresses are vaild.', $crawler->text());
    }

    /**
     * Invite form submission fails if no text is defined.
     *
     * @covers UserController::inviteFriends
     */
    public function testInviteFriendsFailsNoInviteText()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $inviteForm = $crawler->selectButton('Invite')
                              ->form(array('form[inviteLink]'       => 'https://www.niftythrifty.com',
                                           'form[emailAddresses]'   => 'newinvitation1@niftythrifty.com',
                                           'form[inviteText]'       => ''),
                                     'POST');
        $crawler = $client->submit($inviteForm);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('div#user_invite'));
        $this->assertContains('There must be some invite text defined.', $crawler->text());
    }

    /**
     * Invite form submission partially fails if there is a non-unique e-mail invited.
     *
     * @covers UserController::inviteFriends
     */
    public function testInviteFriendsNonUniqueEmails()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();

        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertCount(5, $user->getUserInvitations());

        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/user/invite_friend');
        $inviteForm = $crawler->selectButton('Invite')
                              ->form(array('form[inviteLink]'    => 'https://www.niftythrifty.com',
                                           'form[emailAddresses]'=> 'newinvite1@niftythrifty.com, newinvite2@niftythrifty.com, test1@niftythrifty.com',
                                           'form[inviteText]'    => 'You are invited to nifty thrifty.'),
                                     'POST');
        $crawler = $client->submit($inviteForm);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals($crawler->filter('span#inviteCount')->text(), 2);
        $this->assertTrue($crawler->filter('div#invitations_list')->count() == 1);
        $children = $crawler->filter('div#invitationList')->children();
        $this->assertCount(1, $children);
        $this->assertEquals(trim($crawler->filter('div#invitationList > div.line > div.email')->text()), 'test1@niftythrifty.com');

        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->find(1);
        $this->assertCount(7, $user->getUserInvitations());
        $newInvite1 = $this->em
                           ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                           ->findOneByUserInvitationEmail('newinvite1@niftythrifty.com');
        $this->assertEquals($newInvite1->getUserInvitationStatus(), 'pending');
        $this->assertEquals($newInvite1->getUserInvitationType(),   'mail');
        $this->assertEquals($newInvite1->getUserInvitationEmail(),  'newinvite1@niftythrifty.com');
        $this->assertEquals($newInvite1->getUserId(),               1);
        $newInvite2 = $this->em
                           ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                           ->findOneByUserInvitationEmail('newinvite2@niftythrifty.com');
        $this->assertEquals($newInvite2->getUserInvitationStatus(), 'pending');
        $this->assertEquals($newInvite2->getUserInvitationType(),   'mail');
        $this->assertEquals($newInvite2->getUserInvitationEmail(),  'newinvite2@niftythrifty.com');
        $this->assertEquals($newInvite2->getUserId(),               1);
    }

    /**
     * If you come to the registration form via the share link, verify the inputs are correct.
     *
     * @covers UserController::userRegisterFriendForm
     */
    public function testRegisterFormViaShareLink()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/share/testing-123');

        $this->assertEquals($crawler->filter('h1')->text(), 'Register');
        $this->assertTrue($crawler->filter('div#registration')->count() == 1);
        $this->assertEquals($crawler->filter('input#registration_tokenType')->attr('value'), 'userId');
        $this->assertEquals($crawler->filter('input#registration_inviteToken')->attr('value'), 'testing-123');
    }

    /**
     * Register a user gets credit for a registration via the shared link.
     *
     * @covers UserController::registerUser
     */
    public function testRegisterUserViaShareLink()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $client = static::createClient();
        
        /**
         * We are using test1@niftythrifty.com, who was invited by user 1.
         */
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('test@niftythrifty.com');
        $this->assertCount(0, $user);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('pending',                $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'test1@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser',
                                        'registration[tokenType]'           => 'userId',
                                        'registration[inviteToken]'         => 'test-1'),
                                  'POST');
        
        // The submission whould redirect to the account page.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        // The new address should be a user, and the inviting user should have +1 credit.
        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail('test1@niftythrifty.com');
        $this->assertEquals($user->getUserFirstName(),   'New');
        $this->assertEquals($user->getUserLastName(),    'User');
        $this->assertEquals($user->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(15, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('accepted',               $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());
        $this->assertEquals($user->getUserId(),       $userInvitation->getUserInvitationUserId());
        $this->assertEquals($user->getUserFirstName(),$userInvitation->getUserInvitationFirstName());
        $this->assertEquals($user->getUserLastName(), $userInvitation->getUserInvitationLastName());
    }
    
    /**
     * If you come to the registration form via the invitation link, verify the inputs are correct.
     *
     * @covers UserController::userRegisterFriendInvitationForm
     */
    public function testRegisterFormViaEmailLink()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = static::createClient();
        $crawler = $client->request('GET', '/user/invitation/testing-123');
        
        $this->assertEquals($crawler->filter('h1')->text(), 'Register');
        $this->assertTrue($crawler->filter('div#registration')->count() == 1);
        $this->assertEquals($crawler->filter('input#registration_tokenType')->attr('value'), 'invitationId');
        $this->assertEquals($crawler->filter('input#registration_inviteToken')->attr('value'), 'testing-123');
    }

    /**
     * If the user registers via an e-mail link.
     *
     * @covers UserController:registerUser
     */
    public function testRegisterUserViaEmailLink()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $client = static::createClient();
        
        /**
         * We are using test1@niftythrifty.com, who was invited by user 1.
         */
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('test@niftythrifty.com');
        $this->assertCount(0, $user);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('pending',                $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'test1@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser',
                                        'registration[tokenType]'           => 'invitationId',
                                        'registration[inviteToken]'         => 'test-1'),
                                  'POST');
        
        // The submission whould redirect to the account page.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        // The new address should be a user, and the inviting user should have +1 credit.
        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail('test1@niftythrifty.com');
        $this->assertEquals($user->getUserFirstName(),   'New');
        $this->assertEquals($user->getUserLastName(),    'User');
        $this->assertEquals($user->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(15, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('accepted',               $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());
        $this->assertEquals($user->getUserId(),       $userInvitation->getUserInvitationUserId());
        $this->assertEquals($user->getUserFirstName(),$userInvitation->getUserInvitationFirstName());
        $this->assertEquals($user->getUserLastName(), $userInvitation->getUserInvitationLastName());
    }

    /**
     * If the user being registered via a share link was already invited by another user, nobody
     * should get a credit, but the user should still be registered
     *
     * @covers UserController:registerUser
     */
    public function testRegisterUserEmailLinkAlreadyInvited()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $client = static::createClient();
        
        /**
         * We are using test1@niftythrifty.com, who was invited by user 1.
         */
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('test1@niftythrifty.com');
        $this->assertCount(0, $user);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(2);
        $this->assertEquals(25, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('pending',                $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'test1@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser',
                                        'registration[tokenType]'           => 'userId',
                                        'registration[inviteToken]'         => 'test-2'),
                                  'POST');
        
        // The submission whould redirect to the account page.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        // The new address should be a user, and the inviting user should NOT +1 credit.
        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail('test1@niftythrifty.com');
        $this->assertEquals($user->getUserFirstName(),   'New');
        $this->assertEquals($user->getUserLastName(),    'User');
        $this->assertEquals($user->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(2);
        $this->assertEquals(25, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('pending',                $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());
        $this->assertNotEquals($user->getUserId(),       $userInvitation->getUserInvitationUserId());
        $this->assertNotEquals($user->getUserFirstName(),$userInvitation->getUserInvitationFirstName());
    }

    /**
     * If the user being registered via a share link does not have a pending invitation, nobody
     * gets a credit.
     *
     * @covers UserController:registerUser
     */
    public function testRegisterUserEmailNotPendingInvitation()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $client = static::createClient();
        
        /**
         * We are using test3@niftythrifty.com, who was invited by user 1.
         */
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('test3@niftythrifty.com');
        $this->assertCount(0, $user);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(3);
        $this->assertEquals(0, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(5);
        $this->assertEquals('test3@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('spend',                  $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'test3@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser',
                                        'registration[tokenType]'           => 'userId',
                                        'registration[inviteToken]'         => 'test-3'),
                                  'POST');
        
        // The submission whould redirect to the account page.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        // The new address should be a user, and the inviting user should NOT +1 credit.
        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail('test3@niftythrifty.com');
        $this->assertEquals($user->getUserFirstName(),   'New');
        $this->assertEquals($user->getUserLastName(),    'User');
        $this->assertEquals($user->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(3);
        $this->assertEquals(0, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(5);
        $this->assertEquals('test3@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('spend',                  $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());
        $this->assertNotEquals($user->getUserId(),       $userInvitation->getUserInvitationUserId());
        $this->assertNotEquals($user->getUserFirstName(),$userInvitation->getUserInvitationFirstName());
    }

    /**
     * Test registering a user via an invitation id, which should come in via an e-mail invitation.
     *
     * @covers UserController::registerUser
     */
    public function testRegisterUserViaInvitation()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $client = static::createClient();
        
        /**
         * We are using test1@niftythrifty.com, who was invited by user 1.
         */
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('test1@niftythrifty.com');
        $this->assertCount(0, $user);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('pending',                $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'test1@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser',
                                        'registration[tokenType]'           => 'invitationId',
                                        'registration[inviteToken]'         => 'test-1'),
                                  'POST');
        
        // The submission whould redirect to the account page.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        // The new address should be a user, and the inviting user should have +1 credit.
        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail('test1@niftythrifty.com');
        $this->assertEquals($user->getUserFirstName(),   'New');
        $this->assertEquals($user->getUserLastName(),    'User');
        $this->assertEquals($user->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(15, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(1);
        $this->assertEquals('test1@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('accepted',               $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());
        $this->assertEquals($user->getUserId(),       $userInvitation->getUserInvitationUserId());
        $this->assertEquals($user->getUserFirstName(),$userInvitation->getUserInvitationFirstName());
        $this->assertEquals($user->getUserLastName(), $userInvitation->getUserInvitationLastName());
    }

    /**
     * This should be an illegal state, but test if a user registers whose invitation
     * is already accepted does not lead to a credit.
     *
     * @covers UserController::registerUser
     */
    public function testRegisterUserViaInvitationNotPending()
    {
        $this->addFixture(new UserInvitationData);
        $this->addFixture(new UserCreditsData);
        $this->executeFixtures();
        $client = static::createClient();
        
        /**
         * We are using test3@niftythrifty.com, who was invited by user 1.
         */
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findByUserEmail('test3@niftythrifty.com');
        $this->assertCount(0, $user);
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(5);
        $this->assertEquals('test3@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('spend',                  $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());

        // Get the crawler to the form page.
        $crawler = $client->request('POST', '/user/register');
        $register= $crawler->selectButton('Submit')
                           ->form(array('registration[userFirstName]'       => 'New',
                                        'registration[userLastName]'        => 'User',
                                        'registration[userEmail]'           => 'test3@niftythrifty.com',
                                        'registration[userPassword][first]' => 'testuser',
                                        'registration[userPassword][second]'=> 'testuser',
                                        'registration[tokenType]'           => 'invitationId',
                                        'registration[inviteToken]'         => 'test-5'),
                                  'POST');
        
        // The submission whould redirect to the account page.
        $crawler = $client->submit($register);
        $this->assertTrue($client->getResponse()->isRedirect('/content/registerbonus#registerbonus'));
        
        // The new address should be a user, and the inviting user should NOT +1 credit.
        $this->em->clear();
        $user = $this->em
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail('test3@niftythrifty.com');
        $this->assertEquals($user->getUserFirstName(),   'New');
        $this->assertEquals($user->getUserLastName(),    'User');
        $this->assertEquals($user->getUserPassword(),    '45c571a156ddcef41351a713bcddee5ba7e95460');
        $userCredits = $this->em
                            ->getRepository('NiftyThriftyShopBundle:UserCredits')
                            ->getUserCreditTotal(1);
        $this->assertEquals(14, $userCredits);
        $userInvitation = $this->em
                               ->getRepository('NiftyThriftyShopBundle:UserInvitation')
                               ->find(5);
        $this->assertEquals('test3@niftythrifty.com', $userInvitation->getUserInvitationEmail());
        $this->assertEquals('spend',                  $userInvitation->getUserInvitationStatus());
        $this->assertEquals(1,                        $userInvitation->getUserId());
        $this->assertNotEquals($user->getUserId(),       $userInvitation->getUserInvitationUserId());
        $this->assertNotEquals($user->getUserFirstName(),$userInvitation->getUserInvitationFirstName());
        $this->assertNotEquals($user->getUserLastName(), $userInvitation->getUserInvitationLastName());
    }

    /**
     * Tests for loving an item
     */
    public function testLoveItemNewItem()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        try {
            $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,5);
        } catch (\Doctrine\ORM\NoResultException $e) {}
        $this->assertInstanceOf('\Doctrine\ORM\NoResultException', $e);

        $crawler = $client->request('GET', '/user/love_item/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->em->clear();

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,5);
        $this->assertEquals($loved->getProductId(), 5);
        $this->assertEquals($loved->getUserId(),    1);
        $this->assertEquals($loved->getLoveType(),  'link');
        $this->assertEquals($loved->getIsDeleted(), 0);
    }

    /**
     * Test that loving an item with an isDeleted = 1 sets it back to 0.  A basket love type should not
     * be overrode by a link love type.
     */
    public function testLoveItemPreviouslyDeleted()
    {
        $this->addFixture(new UserLovedProductData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,1);
        $this->assertEquals($loved->getIsDeleted(), 1);
        $this->assertEquals($loved->getLoveType(),  'basket');

        $crawler = $client->request('GET', '/user/love_item/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->em->clear();

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,1);
        $this->assertEquals($loved->getProductId(), 1);
        $this->assertEquals($loved->getUserId(),    1);
        $this->assertEquals($loved->getLoveType(),  'basket');
        $this->assertEquals($loved->getIsDeleted(), 0);
    }

    /**
     * Loving a not found item will return a 404.
     *
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function testLoveItemNotFound()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/love_item/99999');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
        $this->em->clear();

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,99999);
    }

    /**
     * Tests for unloving an item.
     */
    public function testUnloveItem()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,4);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getIsDeleted(), 0);

        $crawler = $client->request('GET', '/user/unlove_item/4');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->em->clear();

        $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,4);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserLovedProduct', $loved);
        $this->assertEquals($loved->getIsDeleted(), 1);
    }

    /**
     * Unloving an item that's not loved.
     */
    public function testUnloveItemNotLoved()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        try {
            $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,5);
        } catch (\Doctrine\ORM\NoResultException $e) {}
        $this->assertInstanceOf('\Doctrine\ORM\NoResultException', $e);

        $crawler = $client->request('GET', '/user/unlove_item/5');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->em->clear();

        try {
            $loved = $this->em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')->findByUserAndProduct(1,5);
        } catch (\Doctrine\ORM\NoResultException $e) {}
        $this->assertInstanceOf('\Doctrine\ORM\NoResultException', $e);
    }

    /**
     * Unloving an item that was not found.
     */
    public function testUnloveItemNotFound()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/unlove_item/99999');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testUnloveItemTextHack()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/unlove_item/select * from user');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    /**
     * Display my loved items.
     */
    public function testShowLovedItems()
    {
        $this->addFixture(new UserLovedProductData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/things_i_love');
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $this->assertCount(1, $crawler->filter('div#products > div.product > div#addtocarthoverdiv > span#love4'));
        $this->assertCount(1, $crawler->filter('div#products > div.product > div#addtocarthoverdiv > span#love2'));
        $this->assertCount(0, $crawler->filter('div#noResults'));
    }

    public function testShowLovedItemsNoItems()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/things_i_love');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('div.product'));
        $this->assertCount(1, $crawler->filter('div#noResults'));
    }

    /**
     * Display another user's loved items.
     */
    public function testShowOtherUserLovedItems()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/things_loved_by/ut_admin');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertCount(1, $crawler->filter('div#lovedProducts'));
        $children = $crawler->filter('div#productList')->children();
        $this->assertCount(2, $children);

        $this->assertEquals($children->eq(0)->attr('id'), 'lovedProduct_1');
        $this->assertEquals($children->eq(1)->attr('id'), 'lovedProduct_3');
        $this->assertCount(0, $crawler->filter('div#noLovedItems'));
    }

    public function testShowOtherUserLovedItemsNoItems()
    {
        $this->addFixture(new UserLovedProductData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/things_loved_by/ut_inactive@niftythrifty.com');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertCount(0, $crawler->filter('div#productList'));
        $this->assertCount(1, $crawler->filter('div#noLovedItems'));
    }

    public function testShowOtherUserNotFound()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/user/things_loved_by/delete from user');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
    }
}
