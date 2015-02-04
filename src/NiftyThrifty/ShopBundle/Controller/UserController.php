<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Doctrine\ORM\Events;
use Doctrine\ORM\NoResultException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use NiftyThrifty\ShopBundle\Entity\UserInvitation;
use NiftyThrifty\ShopBundle\Entity\UserCredits;
use NiftyThrifty\ShopBundle\Form\Type\AddressType;
use NiftyThrifty\ShopBundle\Form\Type\ChangePasswordType;
use NiftyThrifty\ShopBundle\Form\Type\RegistrationType;
use NiftyThrifty\ShopBundle\Form\Type\UserInfoType;

/**
 * Handles all user operations except logging in and out, which is managed by
 * Symfony.
 */
class UserController extends Controller
{
    /**
     * If an unlogged in user attempts to go to the checkout or the admin (backoffice)
     * area, they are redirected here to log in.  They are also redirected here
     * if login fails.
     */
    public function loginInterceptAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($session->get(SecurityContext::LAST_USERNAME)) {
            $lastUsername = $session->get(SecurityContext::LAST_USERNAME);
        } else {
            $currentUser = $this->getUser();
            $lastUsername = $currentUser instanceof \NiftyThrifty\ShopBundle\Entity\User ? $currentUser->getUserEmail() : null;
        }
        return $this->render('NiftyThriftyShopBundle:User:loginFull.html.twig',
                                array('last_username' => $lastUsername,
                                      'error'         => $error));
    }

    /**
     * Check to see if a user is logged in.  If he is not, route to the login form
     * partial.  If he is, route to the say hello partial.  This is called on each
     * page via the main template.
     */
    public function isLoggedInAction()
    {
        $currentUser = $this->getUser();
        if ($currentUser instanceof \NiftyThrifty\ShopBundle\Entity\User) {
            return $this->_getLoggedIn();
        } else {
            return $this->_getLoggedOut();
        }
    }

    /**
     * routed from isLoggedInAction() if a user is not logged in.
     */
    private function _getLoginForm()
    {
        $user = new \NiftyThrifty\ShopBundle\Entity\User();
        $loginForm = $this->createFormBuilder($user)
                          ->add('userEmail', 'text', array('label' => 'E-mail Address'))
                          ->add('userPassword', 'password', array('label' => 'Password'))
                          ->getForm();
        return $this->render('NiftyThriftyShopBundle:User:loginPartial.html.twig',
                             array('form'           => $loginForm->createView(),
                                   'last_username'  => null));
    }

    /**
     * Routed from isLoggedInAction() if a user is logged in.
     */
    private function _getLoggedIn()
    {
        return $this->render('NiftyThriftyShopBundle:User:loggedInPartial.html.twig',
                             array('user' => $this->getUser()));
    }

    /**
     * Routed from isLoggedInAction() if a user is logged out.
     */
    private function _getLoggedOut()
    {
        return $this->render('NiftyThriftyShopBundle:User:loggedOutPartial.html.twig',
                             array('user' => $this->getUser()));
    }

    /**
     * Primary index page for a user's account.
     *
     * @Route("/my_account", name="user_account")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     */
    public function showUserAccountAction()
    {
        return $this->render('NiftyThriftyShopBundle:User:showUserAccount.html.twig',
                                array('user' => $this->getUser()));
    }

    /**
     * Display the form that the user may use to register.  Contains first/last name, e-mail,
     * password, and password confirm.
     *
     * @Route("/register", name="register")
     */
    public function showRegistrationFormAction($type="")
    {
        $user = new \NiftyThrifty\ShopBundle\Entity\User();
        $registrationForm = $this->createForm(new RegistrationType(),
                                                $user,
                                                array('method' => 'POST',
                                                      'action' => $this->generateUrl('register_user')));

        if($type=="overlay"){
			return $this->render('NiftyThriftyShopBundle:User:registerUserOverlay.html.twig',
								 array('editForm' => $registrationForm->createView()));
        } else{
            if ($this->getUser()) return $this->redirect('/');
			return $this->render('NiftyThriftyShopBundle:User:registerUser.html.twig',
								 array('editForm' => $registrationForm->createView()));
		}
    }

    /**
     * Try to register the user.
     *
     * @Route("/register_user", name="register_user")
     * @Method({"POST"})
     */
    public function registerUser(Request $request)
    {
        if ($this->getUser()) return $this->redirect('/');
        $user               = new \NiftyThrifty\ShopBundle\Entity\User();
        $em                 = $this->getDoctrine()->getManager();
        $factory            = $this->get('security.encoder_factory');
        $registrationForm   = $this->createForm(new RegistrationType(),
                                                $user,
                                                array('method' => 'POST',
                                                      'action' => $this->generateUrl('register_user')));
        $encoder            = $factory->getEncoder($user);
        $mailer             = new \Sailthru_Client($this->container->getParameter('sailthru_api_key'),
                                                   $this->container->getParameter('sailthru_api_secret'));
        // Get the existing password so you can compare it later.
        $registrationForm->handleRequest($request);
        // If the password passes validation, encode it before you save it.
        if ($registrationForm->isValid()) {
            $user->setUserPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
            $em->persist($user);
            $em->flush();
            /**
             * This will be populated on an invited user, not for a regular registration.
             */
            if ($registrationForm->get('inviteToken')->getData()) {
                $tokens = explode('-', $registrationForm->get('inviteToken')->getData());
                $tokenId = $tokens[1];

                /**
                 * If the user accessed the registration form via the public share link, there may or may
                 * not be an invitation pending for the user.  A user will receive a credit if:
                 *      - The registering user was invited by the user who provided the share link
                 *      - The registering user was not previously invited by anyone.
                 * If the registering user was invited by User A and uses the public share link of User B,
                 * neither User A nor User B will receive the credit as we have no way to decide who should get
                 * the credit.
                 */
                /**
                 * If the new user was invited by the userid in the token, give the user a credit.  If the
                 * user was invited by another user, nobody gets a credit.
                 */
                if ($registrationForm->get('tokenType')->getData() == 'userId') {
                    $invitation = $em->getRepository('NiftyThriftyShopBundle:UserInvitation')
                                     ->findOneByUserInvitationEmail($user->getUserEmail());

                    // If no invitation was found, create an accepted invitation record
                    if (!($invitation)) {
                        $invitingUser = $em->getRepository('NiftyThriftyShopBundle:User')->find($tokenId);
                        $invitation = new UserInvitation();
                        $invitation->setUserInvitationStatus(UserInvitation::STATUS_ACCEPTED)
                                   ->setUserInvitationType(UserInvitation::TYPE_LINK)
                                   ->setUserInvitationDate(new \DateTime())
                                   ->setUserInvitationUserId($user->getUserId())
                                   ->setUserId($tokenId)
                                   ->setInvitingUser($invitingUser);
                        $em->persist($invitation);

                    /**
                     * If an invitation exists for this user, see if it's pending and if it was generated
                     * originally by the user whose link was clicked.
                     */
                    } else {
                        if (($invitation->getUserId() != $tokenId) || ($invitation->getUserInvitationStatus() != UserInvitation::STATUS_PENDING)) {
                            $invitation = false;
                        }
                    }

                /**
                 * If the token was via an invitation id, get the invitation, update it, and give the referring user
                 * a credit.
                 */
                } else if ($registrationForm->get('tokenType')->getData() == 'invitationId') {
                    $invitation = $em->getRepository('NiftyThriftyShopBundle:UserInvitation')
                                     ->find($tokenId);
                    if ($invitation->getUserInvitationStatus() != UserInvitation::STATUS_PENDING) {
                        $invitation = false;
                    }
                }

                /**
                 * If the rules above gave us an invitation record, update it and give the inviting user
                 * a $1 credit.
                 */
                if ($invitation) {
                    $invitation->setUserInvitationFirstName($user->getUserFirstName())
                               ->setUserInvitationLastName($user->getUserLastName())
                               ->setUserInvitationUserId($user->getUserId())
                               ->setUserInvitationStatus(UserInvitation::STATUS_ACCEPTED);
                    $userCredit = new UserCredits();
                    $nowTime = new \DateTime();
                    $expireTime = new \DateTime();
                    $expireTime->modify("+6 months");
                    $userCredit->setUserCreditsDate($nowTime)
                               ->setUserCreditsDateEnd($expireTime)
                               ->setUserCreditsValue(1)
                               ->setUserId($invitation->getInvitingUser()->getUserId());
                    $em->persist($userCredit);
                    $em->flush();
                    $mailer->send('transa_invitejoins',
                                  $invitation->getInvitingUser()->getUserEmail(),
                                  array('inviter_first_name'    => $invitation->getInvitingUser()->getUserFirstName(),
                                        'inviter_list_name'     => $invitation->getInvitingUser()->getUserLastName(),
                                        'inviter_totalcredits'  => 1,
                                        'invitee_first_name'    => $invitation->getUserInvitationFirstName(),
                                        'invitee_last_name'     => $invitation->getUserInvitationLastName()));
                }
            }

            //send new user reg email
			$sendnow = $mailer->send('transa_welcome',
                                     $user->getUserEmail(),
                                     array('first_name' => $user->getUserFirstName(), 'last_name' => $user->getUserLastName()));
			//add user to email list
			$mailer->setEmail($user->getUserEmail(),
                              array("name"=> $user->getUserFirstName()." ".$user->getUserLastName(),
                                    "Firstname" => $user->getUserFirstName(),
                                    "Lastname" => $user->getUserLastName()),
                              "Nifty Plug");

            // Presuming there was no error thrown in persist or flush, log the user in.
            $token = new UsernamePasswordToken($user, null, 'all_nifty', array('ROLE_USER'));
            $this->get('security.context')->setToken($token);
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return $this->redirect($this->generateUrl('nifty_thrifty_content', array('slug' => 'registerbonus')) . '#registerbonus');
        } else {
            return $this->render('NiftyThriftyShopBundle:User:registerUser.html.twig',
                                 array('editForm' => $registrationForm->createView()));
        }
    }

    /**
     * Display form to recover user's password. This form should be available to anonymous users
     *
     * @Route("/recover_password", name="recover_password_form")
     */
    public function recoverPasswordForm()
    {
        if ($this->getUser() instanceof \NiftyThrifty\ShopBundle\Entity\User) {
            $userEmail = $this->getUser()->getUserEmail();
        } else {
            $userEmail = null;
        }

        $recoverForm = $this->createFormBuilder(array('userEmail' => $userEmail))
                            ->add('userEmail',  'text',     array('label' => "Enter the account's e-mail address.",
                                                                  'attr'  => array('size' => 50)))
                            ->add('Recover',    'submit')
                            ->setMethod('POST')
                            ->setAction($this->generateUrl('recover_password'))
                            ->getForm();
        return $this->render('NiftyThriftyShopBundle:User:recoverPassword.html.twig',
                                array('recoverForm' => $recoverForm->createView()));
    }

    /**
     * Reset the user's password, send them an e-mail.
     *
     * @Route("/recover", name="recover_password")
     * @Method({"POST"})
     */
    public function recoverPassword(Request $request)
    {
        $formData     = $request->request->get('form');
        $emailAddress = trim($formData['userEmail']);

        $user = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail($emailAddress);

        if ($user) {
            // Reset the password to 8 random(ish) alphanumeric characters
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
            $pass = array();
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $newPasswordPlaintext = implode($pass);

            // encode the new password.
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            $newPasswordEncoded = $encoder->encodePassword($newPasswordPlaintext, $user->getSalt());
            $user->setUserPassword($newPasswordEncoded);
            $this->getDoctrine()->getManager()->flush();

            $mailer = new \Sailthru_Client($this->container->getParameter('sailthru_api_key'),
                                          $this->container->getParameter('sailthru_api_secret'));
            $mailerVars = array('first_name'    => $user->getUserFirstName(),
                                'new_password'  => $newPasswordPlaintext,
                                'link'          => 'http://'
                                                    . $this->getRequest()->getHost()
                                                    . $this->generateUrl('edit_password_form'));
            $mailer->send('transa_resetpassword',
                          $user->getUserEmail(),
                          $mailerVars);
            return $this->render('NiftyThriftyShopBundle:User:passwordSent.html.twig');

        /**
         * If no user was found, regenerate the password form and show it with an error
         */
        } else {
            $recoverForm = $this->createFormBuilder(array('userEmail' => $emailAddress))
                                 ->add('userEmail', 'text', array('label' => "Enter the account's e-mail address.",
                                                                  'attr'  => array('size' => 50)))
                                 ->add('Recover', 'submit')
                                 ->setMethod('POST')
                                 ->setAction($this->generateUrl('recover_password'))
                                 ->getForm();
            $recoverForm->addError(new FormError('No account is registered for this e-mail address.'));
            return $this->render('NiftyThriftyShopBundle:User:recoverPassword.html.twig',
                                    array('recoverForm' => $recoverForm->createView()));
        }
    }

    /**
     * Show the user a form for displaying either their shipping or billing address.
     *
     * @Route("/change_address/{slug}", name="user_update_address_form")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     */
    public function editAddressFormAction($slug)
    {
        // Anything other than shipping or billing should be a 404.
        $valid = array(\NiftyThrifty\ShopBundle\Entity\Address::TYPE_SHIPPING,
                       \NiftyThrifty\ShopBundle\Entity\Address::TYPE_BILLING);
        if (!in_array($slug, $valid)) throw $this->createNotFoundException('Page not found.');

        // Create the form to update a user's address
        if ($slug == \NiftyThrifty\ShopBundle\Entity\Address::TYPE_SHIPPING) {
            $address = $this->getUser()->getAddressIdShipping()
                        ? $this->getUser()->getAddressShipping()
                        : new \NiftyThrifty\ShopBundle\Entity\Address();
        } else if ($slug == \NiftyThrifty\ShopBundle\Entity\Address::TYPE_BILLING) {
            $address = $this->getUser()->getAddressIdBilling()
                        ? $this->getUser()->getAddressBilling()
                        : new \NiftyThrifty\ShopBundle\Entity\Address();
        }

        $editForm = $this->createForm(new AddressType(),
                                      $address,
                                      array('method' => 'POST',
                                            'action' => $this->generateUrl('user_update_address_action')));

        // Add which address we're aditing so the right one is updated.
        $editForm->add('userId',
                       'hidden',
                       array('attr' => array('value' => $this->getUser()->getUserId())));
        $editForm->add('addressType',
                       'hidden',
                       array('mapped' => false,
                             'attr'   => array('value' => $slug)));

        return $this->render('NiftyThriftyShopBundle:User:editAddress.html.twig',
                             array('user'        => $this->getUser(),
                                   'addressType' => $slug,
                                   'editForm'    => $editForm->createView()));
    }

    /**
     * Function updates or adds a default address for the user.
     *
     * @Route("/update_address", name="user_update_address_action")
     * @Method({"POST"})
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     */
    public function updateAddress(Request $request)
    {
        $newAddress = new \NiftyThrifty\ShopBundle\Entity\Address();
        $em         = $this->getDoctrine()->getManager();
        $editForm   = $this->createForm(new AddressType(),
                                        $newAddress,
                                        array('method' => 'POST',
                                              'action' => $this->generateUrl('user_update_address_action')));
        $editForm->add('userId',        'hidden');
        $editForm->add('addressType',   'hidden', array('mapped' => false));
        $editForm->handleRequest($request);
        $addressType = $editForm->get('addressType')->getData();

        /**
         * We will update or create based on whether or not there is currently a default setting
         * for this user.  A default shipping or billing address will be updated.  This will not
         * create a new record in the database.  Nor will the previous address be saved.
         */
        if ($editForm->isValid()) {
            $newAddress->setUser($this->getUser());
            $newAddress->setState($editForm->get('state')->getData());

            if ($addressType == \NiftyThrifty\ShopBundle\Entity\Address::TYPE_SHIPPING) {
                if ($this->getUser()->getAddressIdShipping()) {
                    $this->getUser()->getAddressShipping()->setAddressFirstName($newAddress->getAddressFirstName());
                    $this->getUser()->getAddressShipping()->setAddressLastName($newAddress->getAddressLastName());
                    $this->getUser()->getAddressShipping()->setAddressStreet($newAddress->getAddressStreet());
                    $this->getUser()->getAddressShipping()->setAddressCity($newAddress->getAddressCity());
                    $this->getUser()->getAddressShipping()->setAddressZipcode($newAddress->getAddressZipcode());
                    $this->getUser()->getAddressShipping()->setState($newAddress->getState());
                } else {
                    $this->getUser()->setAddressShipping($newAddress);
                }
            } else if ($addressType == \NiftyThrifty\ShopBundle\Entity\Address::TYPE_BILLING) {
                if ($this->getUser()->getAddressIdBilling()) {
                    $this->getUser()->getAddressBilling()->setAddressFirstName($newAddress->getAddressFirstName());
                    $this->getUser()->getAddressBilling()->setAddressLastName($newAddress->getAddressLastName());
                    $this->getUser()->getAddressBilling()->setAddressStreet($newAddress->getAddressStreet());
                    $this->getUser()->getAddressBilling()->setAddressCity($newAddress->getAddressCity());
                    $this->getUser()->getAddressBilling()->setAddressZipcode($newAddress->getAddressZipcode());
                    $this->getUser()->getAddressBilling()->setState($newAddress->getState());
                } else {
                    $this->getUser()->setAddressBilling($newAddress);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('user_account'));

        // Invalid form, re-render with errors.
        } else {
            return $this->render('NiftyThriftyShopBundle:User:editAddress.html.twig',
                                 array('user'        => $this->getUser(),
                                       'addressType' => $addressType,
                                       'editForm'    => $editForm->createView()));
        }
    }

    /**
     * Update a user's general account info.  IE: name/e-mail address
     *
     * @Route("/edit_account_info", name="edit_account_info_form")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     */
    public function editAccountInfoFormAction()
    {
        $editForm = $this->createForm(new UserInfoType(),
                                      $this->getUser(),
                                      array('method' => 'POST',
                                            'action' => $this->generateUrl('update_user_info')));
        return $this->render('NiftyThriftyShopBundle:User:editUserInfo.html.twig',
                             array('user'        => $this->getUser(),
                                   'editForm'    => $editForm->createView()));
    }

    /**
     * Update the user's information
     *
     * @Route("/update_account_info", name="update_user_info")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     * @Method({"POST"})
     */
    public function updateAccountInfoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(new UserInfoType(),
                                      $this->getUser(),
                                      array('method' => 'POST',
                                            'action' => $this->generateUrl('update_user_info')));
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('user_account'));
        } else {
            return $this->render('NiftyThriftyShopBundle:User:editUserInfo.html.twig',
                                 array('user'        => $this->getUser(),
                                       'editForm'    => $editForm->createView()));
        }
    }

    /**
     * User's change password form
     *
     * @Route("/edit_password", name="edit_password_form")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     */
    public function editPasswordFormAction()
    {
        $editForm = $this->createForm(new ChangePasswordType(),
                                      $this->getUser(),
                                      array('method' => 'POST',
                                            'action' => $this->generateUrl('update_password')));
        return $this->render('NiftyThriftyShopBundle:User:editPassword.html.twig',
                             array('user'        => $this->getUser(),
                                   'editForm'    => $editForm->createView()));
    }

    /**
     * Update the user's password.  More of this is handled in the controller than i would like
     * but I couldn't quite figure out how to get the UserPassword() validation to work, so all
     * the encoding is handled here.  A future project, if I wanted to get in to modifying the
     * Symfony framework, would be to generate a "change password" form automatically with all
     * the authentication and such handled rather than this current mess.  -Tom
     *
     * @Route("/update_password", name="update_password")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     * @Method({"POST"})
     */
    public function updatePasswordFormAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $factory = $this->get('security.encoder_factory');
        $editForm = $this->createForm(new ChangePasswordType(),
                                      $this->getUser(),
                                      array('method' => 'POST',
                                            'action' => $this->generateUrl('update_password')));
        $encoder = $factory->getEncoder($this->getUser());

        // Get the existing password so you can compare it later.
        $currentPassword = $this->getUser()->getPassword();
        $editForm->handleRequest($request);

        // Encode the entered current password so you can compare it to the user's current password.
        $encodedCurrentFormPassword = $encoder->encodePassword($editForm->get('currentPassword')->getData(), $this->getUser()->getSalt());

        // There is theoretically a validation method that handles this, but damned if I could get it to work.
        if ($encodedCurrentFormPassword != $currentPassword) {
            $editForm->addError(new FormError('Current password is incorrect.'));
        }

        // If the password passes validation, encode it before you save it.
        if ($editForm->isValid()) {
            $this->getUser()->setUserPassword($encoder->encodePassword($this->getUser()->getPassword(), $this->getUser()->getSalt()));
            $em->flush();
            return $this->redirect($this->generateUrl('user_account'));
        } else {
            return $this->render('NiftyThriftyShopBundle:User:editPassword.html.twig',
                                 array('user'        => $this->getUser(),
                                       'editForm'    => $editForm->createView()));
        }
    }

    /**
     * Display the history of user's orders.  Displays order number, date, and status.
     *
     * @Route("/my_orders", name="view_order_history")
     */
    public function viewOrderHistoryAction()
    {
        $invoices = $this->getUser()->getInvoices();
        return $this->render('NiftyThriftyShopBundle:User:orderHistory.html.twig',
                                array('user'    => $this->getUser(),
                                      'invoices'=> $invoices));
    }

    /**
     * This generates the "invite friends" form.  Anonymous users should be barred from
     * using this form.
     *
     * @Route("/invite_friend", name="user_invite_friend")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     */
    public function inviteFriendAction()
    {
        $inviteFormData = array('inviteLink'    => 'http://'
                                                    . $this->getRequest()->getHost()
                                                    . $this->generateUrl('user_invitation_link',
                                                                         array('inviteToken' => $this->getUser()->getInviteToken()
                                                                                                    . '-'
                                                                                                    . $this->getUser()->getUserId())),
                                'emailAddresses'=> null,
                                'inviteText'    => UserInvitation::DEFAULT_INVITE_TEXT);
        $inviteForm     = $this->createFormBuilder($inviteFormData)
                               ->add('inviteLink',      'text',     array('label'       => false,
                                                                          'read_only'   => true,
                                                                          'attr'        => array('size' => 100)))
                               ->add('emailAddresses',  'text',     array('label' => false,
                                                                          'attr'  => array('size' => 100)))
                               ->add('inviteText',      'textarea', array('label' => false,
                                                                          'attr'  => array('cols' => 100,
                                                                                           'rows' => 10)))
                               ->add('Invite', 'submit')
                               ->setMethod('POST')
                               ->setAction($this->generateUrl('process_invite_friends'))
                               ->getForm();
        $usercredits = $this->getDoctrine()
		 				    ->getManager()
						    ->getRepository('NiftyThriftyShopBundle:UserCredits')
						    ->getUserCreditTotal($this->getUser()->getUserId());

        return $this->render('NiftyThriftyShopBundle:User:inviteFriends.html.twig',
                                array('user'        => $this->getUser(),
                                      'totalcredits'  => $usercredits,
                                      'inviteForm'  => $inviteForm->createView(),
                                      'invitations' => $this->getUser()->getUserInvitations()));
    }

    /**
     * This processes the form inviting users.  It adds the invitation records and handles
     * e-mailing the users.
     *
     * @Route("/process_invites", name="process_invite_friends")
     * @Secure(roles="IS_AUTHENTICATED_FULLY")
     * @Method({"POST"})
     */
    public function inviteFriends(Request $request)
    {
        $inviteFormData = array();
        $inviteForm = $this->createFormBuilder($inviteFormData)
                           ->add('inviteLink',      'text',     array('label' => false, 'read_only' => true,
                                                                      'attr'  => array('size' => 100)))
                           ->add('emailAddresses',  'text',     array('label' => false,
                                                                      'attr'  => array('size' => 100)))
                           ->add('inviteText',      'textarea', array('label' => false,
                                                                      'attr'  => array('cols' => 100, 'rows' => 10)))
                           ->setMethod('POST')
                           ->setAction($this->generateUrl('process_invite_friends'))
                           ->getForm();
        $inviteForm->handleRequest($request);
        $inviteFormData = $inviteForm->getData();
        $em     = $this->getDoctrine()->getManager();
        $errors = array();
        $invalidEmails = array();
        $validInvites  = array();
        $errors        = array();

        // Validate the e-mails.
        if (!$inviteFormData['emailAddresses']) {
            $errors['emailAddresses'] = 'At least one e-mail address must be provided.';
        } else if (!$inviteFormData['inviteText']) {
            $errors['inviteText'] = 'There must be some invite text defined.';
        } else {
            $addresses = explode(',', str_replace(' ', '', $inviteFormData['emailAddresses']));
            $constraints = new All(array(new Email()));
            $errorList = $this->get('validator')->validateValue($addresses, $constraints);
            if (sizeof($errorList)) $errors['emailAddresses'] = 'Invitations could not be sent because not all e-mail addresses are vaild.';
        }

        if (!sizeof($errors)) {
            $addresses      = explode(',', str_replace(' ', '', $inviteFormData['emailAddresses']));
            $nowTime        = new \DateTime();
            $validInvites   = array();
            $inviteValidator= $this->get('validator');

            foreach ($addresses as $address) {
                $invite = new UserInvitation();
                $invite->setUserInvitationStatus(UserInvitation::STATUS_PENDING)
                       ->setUserInvitationDate($nowTime)
                       ->setUserInvitationType(UserInvitation::TYPE_MAIL)
                       ->setUserInvitationContent($inviteFormData['inviteText'])
                       ->setUserInvitationEmail($address)
                       ->setUserId($this->getUser()->getUserId())
                       ->setInvitingUser($this->getUser());

                // Validate here.  The only error this validation should pick up is unique e-mail problems
                $validationErrors = $inviteValidator->validate($invite);

                if (sizeof($validationErrors)) {
                    $invalidEmails[] = $address;
                } else {
                    $validInvites[] = $invite;
                    $em->persist($invite);
                }
            }

            // If there were any valid invitations, save them.
            if (sizeof($validInvites)) {
                $em->flush();
            }
        }

        // If there are valid invitations, send the e-mails.
        if (sizeof($validInvites)) {
            $invitationCount = 0;
            $mailer = new \Sailthru_Client($this->container->getParameter('sailthru_api_key'),
                                           $this->container->getParameter('sailthru_api_secret'));
            $mailerVars = array('inviter_first_name'=> $this->getUser()->getUserFirstName(),
                                'inviter_last_name' => $this->getUser()->getUserLastName(),
                                'invitation_link'   => null,
                                'invitation_message'=> null);
            foreach ($validInvites as $invitation) {
                $invitationCount++;
                $mailerVars['invitation_link'] = 'http://'
                                                    . $this->getRequest()->getHost()
                                                    . $this->generateUrl('user_email_invitation',
                                                                         array('inviteToken' => $this->getUser()->getInviteToken()
                                                                                                . '-'
                                                                                                . $invitation->getUserInvitationId()));
                $mailerVars['invitation_message'] = $invitation->getUserInvitationContent();
                $result = $mailer->send('transa_inviteintro',
                                        $invitation->getUserInvitationEmail(),
                                        $mailerVars);
            }

            return $this->render('NiftyThriftyShopBundle:User:invitationsSent.html.twig',
                                    array('invalidEmails'   => $invalidEmails,
                                          'inviteCount'     => $invitationCount));
        }

        /**
         * If we didn't return in the above block, there were errors we couldn't recover from or there
         * were no valid e-mails, so just display the invite form again with errors.
         */
        foreach ($errors as $errorKey => $errorVal) {
            $inviteForm->get($errorKey)->addError(new FormError($errorVal));
        }
        if (sizeof($invalidEmails)) {
            $inviteForm->get('emailAddresses')->addError(new FormError('The following users were already invited: ' .
                                                                        implode(';', $invalidEmails)));
        }

        $credits = $em->getRepository('NiftyThriftyShopBundle:UserCredits')
                      ->getUserCreditTotal($this->getUser()->getUserId());
        return $this->render('NiftyThriftyShopBundle:User:inviteFriends.html.twig',
                                array('user'        => $this->getUser(),
                                      'inviteForm'  => $inviteForm->createView(),
                                      'totalcredits'=> $credits,
                                      'invitations' => array()));
    }

    /**
     * This processes a user invitation link to give credit to the original user.
     * This generates the "invite friends" form.  Anonymous users should be barred from
     * using this form.
     *
     * @Route("/share/{inviteToken}", name="user_invitation_link")
     */
    public function userRegisterFriendForm($inviteToken)
    {
        $user = new \NiftyThrifty\ShopBundle\Entity\User();
        $registrationForm = $this->createForm(new RegistrationType(),
                                                $user,
                                                array('method' => 'POST',
                                                      'action' => $this->generateUrl('register_user')));
        $registrationForm->get('inviteToken')->setData($inviteToken);
        $registrationForm->get('tokenType')->setData('userId');
        $registrationForm->get('referSite')->setData(UserInvitation::getReferer($this->get('request')->headers->get('referer')));

        return $this->render('NiftyThriftyShopBundle:User:registerUser.html.twig',
                             array('editForm' => $registrationForm->createView()));
    }

    /**
     * Same principal as above function, but the token uses an invitation id instead of the user's id.
     *
     * @Route("/invitation/{inviteToken}", name="user_email_invitation"))
     */
    public function userRegisterFriendInvitationFormAction($inviteToken)
    {
        $user = new \NiftyThrifty\ShopBundle\Entity\User();
        $registrationForm = $this->createForm(new RegistrationType(),
                                                $user,
                                                array('method' => 'POST',
                                                      'action' => $this->generateUrl('register_user')));
        $registrationForm->get('inviteToken')->setData($inviteToken);
        $registrationForm->get('tokenType')->setData('invitationId');

        return $this->render('NiftyThriftyShopBundle:User:registerUser.html.twig',
                             array('editForm' => $registrationForm->createView()));
    }

    /**
     * Load in custom js tags
     */
    public function customTagsAction()
    {
        $currentUser = $this->getUser();
        if ($currentUser instanceof \NiftyThrifty\ShopBundle\Entity\User) {
			return $this->render('NiftyThriftyShopBundle:User:customTagsLoggedIn.html.twig',
								 array('user' => $currentUser));
        } else {
			return $this->render('NiftyThriftyShopBundle:User:customTagsLoggedOut.html.twig',
								 array('user' => $currentUser));
        }
    }

    /**
     * There are a couple ways to love an item.
     *  - Love an already loved item (nothing happens).
     *  - Love a deleted item (update the delete flag)
     *  - Love an unloved item (insert a new record)
     *
     * @Route("/love_item/{productId}", name="love_item", requirements={"productId" = "\d+"})
     * @Method({"GET"})
     */
    public function loveItem($productId)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $lovedItem = $em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                            ->findByUserAndProduct($this->getUser()->getUserId(), $productId);
            $deletedVal = $lovedItem->getIsDeleted() ? 0 : 1;
            $lovedItem->setIsDeleted($deletedVal);
            $responsevalue = $deletedVal ? 'unloved' : 'loved';

        } catch (NoResultException $e) {
            $nowTime = new \DateTime();
            $product = $em->getRepository('NiftyThriftyShopBundle:Product')->find($productId);
            if ($product) {
                $lovedItem = new \NiftyThrifty\ShopBundle\Entity\UserLovedProduct();
                $lovedItem->setProductId($productId)
                          ->setUserId($this->getUser()->getUserId())
                          ->setProduct($product)
                          ->setUser($this->getUser())
                          ->setLoveType('link')
                          ->setDateLoved($nowTime)
                          ->setIsDeleted(0);
                $em->persist($lovedItem);
            $responsevalue = "loved";
            } else {
                throw $this->createNotFoundException('Item was not found.');
            }
        }
        $em->flush();
		$response = new Response(json_encode(array('action' => $responsevalue)));
		$response->headers->set('Content-Type', 'application/json');

		return $response;

        //return new Response('loved');
    }

    /**
     * Remove an item from a user's loved items list.
     *
     * @Route("/unlove_item/{productId}", name="unlove_item", requirements={"productId" = "\d+"})
     * @Method({"GET"})
     */
    public function unloveItem($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('NiftyThriftyShopBundle:Product')->find($productId);
        if (!$product) {
            throw $this->createNotFoundException('Product was not found.');
        }

        try {
            $lovedItem = $em->getRepository('NiftyThriftyShopBundle:UserLovedProduct')
                            ->findByUserAndProduct($this->getUser()->getUserId(), $productId);
            $lovedItem->setIsDeleted(1);
            $em->flush();

        /**
         * We can swallow this exception, because if we're deleting something that doesn't
         * exist, who cares?
         */
        }
        catch (NoResultException $e) {}

        return new Response('unloved');
    }

    /**
     * Display the current user's loved items
     *
     * @Route("/things_i_love", name="current_user_loves")
     * @Method({"GET"})
     */
    public function myLoves()
    {
        $repository = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product');

        $items      = $repository->findByLove($this->getUser()->getUserId(),
                                              array('orderBy' => 'productId',
                                                    'orderDirection' => 'DESC'
                                              ));
        $productscount = $repository->findCountByLove($this->getUser()->getUserId());

        /**
        $items      = $this->getDoctrine()
                           ->getManager()
                           ->getRepository('NiftyThriftyShopBundle:Product')
                           ->findLovedItems($this->getUser()->getUserId());
        $loves      = $this->getUser()->getUserLovedProducts();
        $items      = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($loves as $love) $items->add($love->getProduct());
		$productscount      = sizeof($items);
        **/

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
                             array('products'               => $items,
							       'productscount'          => $productscount,
                                   'categories'             => $categories,
 							       'activeCategoryId'       => '',
                                   'sizes'                  => $sizes,
                                   'collectionsForFilter'   => $collectionsForFilter,
                                   'description'            => "My Loves"));
    }

    /**
     * Display another user's loved items
     *
     * @Route("/things_loved_by/{user}", name="other_user_loves")
     * @Method({"GET"})
     */
    public function userLoves($user)
    {
        $em     = $this->getDoctrine()->getManager();
        $user   = $em->getRepository('NiftyThriftyShopBundle:User')
                     ->findOneByUserEmail($user);
        if (!($user instanceof \NiftyThrifty\ShopBundle\Entity\User)) {
            throw $this->createNotFoundException('User was not found.');
        }
        $loves   = $user->getUserLovedProducts();

        return $this->render('NiftyThriftyShopBundle:User:lovedProducts.html.twig',
                             array('loves'  => $loves,
                                   'user'   => $user));
    }
}
