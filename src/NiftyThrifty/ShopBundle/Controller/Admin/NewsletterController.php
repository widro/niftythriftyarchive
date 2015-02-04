<?php

namespace NiftyThrifty\ShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;

use NiftyThrifty\ShopBundle\Entity\Newsletter;
use NiftyThrifty\ShopBundle\Form\NewsletterType;
use NiftyThrifty\ShopBundle\Form\NewsletterFilterType;

/**
 * Newsletter controller.
 *
 * @Route("/newsletter_admin")
 */
class NewsletterController extends Controller
{
    /**
     * Lists all Newsletter entities.
     *
     * @Route("/", name="newsletter_admin")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return array(
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        );
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $filterForm = $this->createForm(new NewsletterFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->createQueryBuilder('e')->orderBy('e.newsletterId', 'DESC');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('NewsletterControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->bind($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('NewsletterControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('NewsletterControllerFilter')) {
                $filterData = $session->get('NewsletterControllerFilter');
                $filterForm = $this->createForm(new NewsletterFilterType(), $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder)
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $this->getRequest()->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me)
        {
            return $me->generateUrl('newsletter_admin', array('page' => $page));
        };

        // Paginator - view
        $translator = $this->get('translator');
        $view = new TwitterBootstrapView();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => $translator->trans('views.index.pagprev', array(), 'JordiLlonchCrudGeneratorBundle'),
            'next_message' => $translator->trans('views.index.pagnext', array(), 'JordiLlonchCrudGeneratorBundle'),
        ));

        return array($entities, $pagerHtml);
    }

    /**
     * Creates a new Newsletter entity.
     *
     * @Route("/", name="newsletter_admin_create")
     * @Method("POST")
     * @Template("NiftyThriftyShopBundle:Admin/Newsletter:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Newsletter();
        $form = $this->createForm(new NewsletterType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('newsletter_admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Newsletter entity.
     *
     * @Route("/new", name="newsletter_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Newsletter();
        $entity->setNewsletterLink('https://www.niftythrifty.com');
        $form   = $this->createForm(new NewsletterType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Newsletter entity.
     *
     * @Route("/{id}", name="newsletter_admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Newsletter entity.
     *
     * @Route("/{id}/edit", name="newsletter_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $editForm = $this->createForm(new NewsletterType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Displays a form to edit an existing Newsletter entity.
     *
     * @Route("/{id}/schedule", name="newsletter_admin_schedule")
     * @Method("GET")
     * @Template()
     */
    public function scheduleAction($id)
    {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Newsletter entity.');
		}


		return array(
			'entity'      => $entity
		);
    }
    /**
     * Displays a form to edit an existing Newsletter entity.
     *
     * @Route("/{id}/schedulesubmit", name="newsletter_admin_schedule_submit")
     * @Method("POST")
     * @Template()
     */
    public function schedulesubmitAction($id)
    {
    	$viewtype = "showform";

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find Newsletter entity.');
		}

		$newsletterHtml = $this->getNewsletterHtml($id);

		$mailer = new \Sailthru_Client($this->container->getParameter('sailthru_api_key'),
									   $this->container->getParameter('sailthru_api_secret'));

		$schedule_time = "2013-11-01 10:10:10";
		$schedule_time = $_POST['schedule_time'];


		$blast = $mailer->scheduleBlast($entity->getNewsletterName(),
										'Nifty Plug',
										$schedule_time,
										'NiftyThrifty',
										'sales@niftythrifty.com',
										$entity->getNewsletterTitle(),
										$newsletterHtml,
										'',
										array(
											'is_link_tracking' => 1,
											'is_google_analytics' => 1
										)
		);

		return array(
			'entity'      => $entity,
			'newsletterHtml'      => $newsletterHtml
		);

    }
    /**
     * Edits an existing Newsletter entity.
     *
     * @Route("/{id}", name="newsletter_admin_update")
     * @Method("PUT")
     * @Template("NiftyThriftyShopBundle:Admin/Newsletter:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new NewsletterType(), $entity);
        $editForm->bind($request);

        /**
         * There are three cases we have to handle here.
         *  1) The delete file checkbox is checked - Remove this file.
         *  2) A new file has been uploaded.  Delete the existing file and upload
         *      the new file.  The entity should handle this correctly.
         *  3) Neither of the above cases. Add the saved filepath in to the entity so the
         *      database does not delete it.
         */

        // Handle updates first.
        $existingImageData = $request->request->get('existing_niftythrifty_shopbundle_newslettertype');
        $uploadedFiles     = $request->files->get('niftythrifty_shopbundle_newslettertype');
        if (!$existingImageData) $existingImageData = array();

        // If a file has not been uploaded, set the path to its original path string which we stored in a hidden variable
        if (!($uploadedFiles['newsletterCollectionImg'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('newsletterCollectionImg', $existingImageData)) $existingImageData['newsletterCollectionImg'] = null;
            $entity->setNewsletterCollectionImg($existingImageData['newsletterCollectionImg']);
        }
        if (!($uploadedFiles['newsletterProduct1Img'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('newsletterProduct1Img', $existingImageData)) $existingImageData['newsletterProduct1Img'] = null;
            $entity->setNewsletterProduct1Img($existingImageData['newsletterProduct1Img']);
        }
        if (!($uploadedFiles['newsletterProduct2Img'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('newsletterProduct2Img', $existingImageData)) $existingImageData['newsletterProduct2Img'] = null;
            $entity->setNewsletterProduct2Img($existingImageData['newsletterProduct2Img']);
        }

        // Next handle deletes - if you add a new image and check the box, you will get no image.
        $deletedImageData  = $request->request->get('delete_niftythrifty_shopbundle_newslettertype');
        if (!$deletedImageData) $deletedImageData = array();
        $deletedNewsletterCollectionImg     = array_key_exists('newsletterCollectionImg',   $deletedImageData) ? 1 : 0;
        $deletedNewsletterProduct1Img       = array_key_exists('newsletterProduct1Img',     $deletedImageData) ? 1 : 0;
        $deletedNewsletterProduct2Img       = array_key_exists('newsletterProduct2Img',     $deletedImageData) ? 1 : 0;
        if ($deletedNewsletterCollectionImg) $entity->setNewsletterCollectionImg(null);
        if ($deletedNewsletterProduct1Img)   $entity->setNewsletterProduct1Img(null);
        if ($deletedNewsletterProduct2Img)   $entity->setNewsletterProduct2Img(null);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('newsletter_admin_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Newsletter entity.
     *
     * @Route("/{id}", name="newsletter_admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Newsletter entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('newsletter_admin'));
    }

    /**
     * Creates a form to delete a Newsletter entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }






    /**
     * Function to create newsletter html, maybe changed soon
     *
     * @param int $id The newsletter id
     *
     */

	private function getNewsletterHtml($id){
        $em = $this->getDoctrine()->getManager();

        $newsletter = $em->getRepository('NiftyThriftyShopBundle:Newsletter')->find($id);

        if (!$newsletter) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }










		$html ='


<html>
<head>
<title>'.$newsletter->getNewsletterTitle().'</title>
</head>
<body>
<div style="width:560px; padding:20px;padding-top:15px; height:auto; margin:0 auto;">

	<div style="width:560px;margin-bottom:15px;">
		<div style="float:right;">
			<a href="https://www.facebook.com/Niftythriftyvintage"><img src="https://www.niftythrifty.com/images/images/email_topicon_facebook.png"></a>
		</div>
	</div>
	<div style="width:560px; border-bottom:1px solid #cccccc;margin-bottom:15px;">
		<table width="560" height="36" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td><a href="https://www.niftythrifty.com/Collections/Home.sls"><img src="https://www.niftythrifty.com/images/images/emailnav_01.png" width="122" height="36" alt="" border="0"></a></td>
				<td><a href="https://www.niftythrifty.com/Collections/Home.sls"><img src="https://www.niftythrifty.com/images/images/emailnav_02.png" width="132" height="36" alt="" border="0"></a></td>
				<td><a href="https://www.niftythrifty.com/Collections/Archive.sls"><img src="https://www.niftythrifty.com/images/images/emailnav_03.png" width="84" height="36" alt="" border="0"></a></td>
				<td><a href="https://www.niftythrifty.com/Collections/LookBook.sls"><img src="https://www.niftythrifty.com/images/images/emailnav_04.png" width="123" height="36" alt="" border="0"></a></td>
				<td><a href="https://www.niftythrifty.com/Collections/Archive/Search/under35.sls"><img src="https://www.niftythrifty.com/images/images/emailnav_052.png" width="99" height="36" alt="" border="0"></a></td>
			</tr>
		</table>
	</div>
	<div style="width:560px; height:auto;margin-bottom:20px;">
		<a href="'.$newsletter->getNewsletterLink().'"><img src="https://www.niftythrifty.com/'.$newsletter->getNewsletterCollectionImg().'" border="0" width="560"></a>
		<div style="clear:both;"></div>
	</div>
	';

	if($newsletter->getNewsletterProduct1Img()&&$newsletter->getNewsletterProduct2Img()){
		$html .='
	<div style="width:560px; height:auto;margin-bottom:20px;">
		<div style="width:270px; height:auto;overflow:hidden; float:left;">
			<a href="'.$newsletter->getNewsletterProduct1Link().'"><img src="https://www.niftythrifty.com/'.$newsletter->getNewsletterProduct1Img().'"></a>
		</div>
		<div style="width:270px; height:auto;overflow:hidden; margin-left:20px;float:left;">
			<a href="'.$newsletter->getNewsletterProduct2Link().'"><img src="https://www.niftythrifty.com/'.$newsletter->getNewsletterProduct2Img().'"></a>
		</div>
		<div style="clear:both;"></div>
	</div>
	';
	}
	elseif($newsletter->getNewsletterProduct1Img()){
		$html .='
	<div style="width:560px; height:auto;margin-bottom:20px;">
		<div style="width:560px; height:auto;overflow:hidden; float:left;">
			<a href="'.$newsletter->getNewsletterProduct1Link().'"><img src="https://www.niftythrifty.com/'.$newsletter->getNewsletterProduct1Img().'"></a>
		</div>
		<div style="clear:both;"></div>
	</div>
	';
	}




		$html .='
	<div style="width:560px; height:1px;margin-bottom:20px;">
		<img src="https://www.niftythrifty.com/images/images/emaildashedline.png" style="display:block;">
	</div>
	<div style="width:560px; height:auto;margin-bottom:15px;">
		<div style="border-right:1px solid #cccccc;float:left;">
			<a href="https://www.niftythrifty.com/User/Invite.sls"><img src="https://www.niftythrifty.com/images/images/email_3row_a2.png" border="0"></a>
		</div>
		<div style="border-right:1px solid #cccccc;float:left;">
			<table width="190" height="48" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="4">
						<img src="https://www.niftythrifty.com/images/images/email_3row_b_01.png" width="190" height="22" alt="" border="0"></td>
				</tr>
				<tr>
					<td><a href="https://www.facebook.com/Niftythriftyvintage"><img src="https://www.niftythrifty.com/images/images/email_3row_b_02.png" width="56" height="26" alt="" border="0"></a></td>
					<td><a href="https://twitter.com/niftythrifty"><img src="https://www.niftythrifty.com/images/images/email_3row_b_03.png" width="38" height="26" alt="" border="0"></a></td>
					<td><a href="http://instagram.com/niftythrifty"><img src="https://www.niftythrifty.com/images/images/email_3row_b_04.png" width="38" height="26" alt="" border="0"></a></td>
					<td><a href="http://www.pinterest.com/niftythriftynyc/"><img src="https://www.niftythrifty.com/images/images/email_3row_b_05.png" width="58" height="26" alt="" border="0"></a></td>
				</tr>
			</table>
	</div>
		<div style="float:left;">
			<a href="http://blog.niftythrifty.com/"><img src="https://www.niftythrifty.com/images/images/email_3row_c.png" border="0"></a>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div style="width:560px; display:block; height:1px;padding-bottom:40px;">
		<img src="https://www.niftythrifty.com/images/images/emaildashedline.png" style="display:block;">
	</div>
	<div style="width:560px; height:auto; text-align:center;font-size: 12px; font-family: arial; color: #adadad; font-style: italic;">
		If you are unable to see this message, click <a style="text-decoration: none;color: #757575;" href="https://www.niftythrifty.com/Home/Newsletter/Id/328__cosmic_breeze-330.sls"><strong>here</strong></a> to view.
		<br/>
		To ensure delivery to your inbox, please add <a style="text-decoration: none;color: #757575;" href="mailto:sales@niftythrifty.com" title=""><strong>sales@niftythrifty.com</strong></a> to your address book.
		<br/>
		If you wish to unsubscribe, click <a style="font-weight:bold;text-decoration: none;color: #757575;" href="{optout_confirm_url}">here</a>.
		<br/>
		<br/>
		<a style="color: #ffffff;background-color: #000000;height: 16px;line-height: 16px;font-family: arial;text-align: center;text-decoration: none;font-size: 10px;padding: 0 7px;font-weight: bold;" href="http://www.niftythrifty.com" >www.niftythrifty.com</a>
		<br /><br />
		NiftyThrifty, Inc.<br />
		37 Greenpoint Avenue - Suite A3A<br />
		Brooklyn (Greenpoint), NY 11222<br />
		email - {email}
		{beacon}
	</div>
</div>
</body>
</html>


		';
		return $html;
	}


























}
