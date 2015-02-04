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

use NiftyThrifty\ShopBundle\Entity\Banner;
use NiftyThrifty\ShopBundle\Form\BannerType;
use NiftyThrifty\ShopBundle\Form\BannerFilterType;

/**
 * Banner controller.
 *
 * @Route("/banner_admin")
 */
class BannerController extends Controller
{
    /**
     * Lists all Banner entities.
     *
     * @Route("/", name="banner_admin")
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
        $filterForm = $this->createForm(new BannerFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('NiftyThriftyShopBundle:Banner')->createQueryBuilder('e');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('BannerControllerFilter');
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
                $session->set('BannerControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('BannerControllerFilter')) {
                $filterData = $session->get('BannerControllerFilter');
                $filterForm = $this->createForm(new BannerFilterType(), $filterData);
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
            return $me->generateUrl('banner_admin', array('page' => $page));
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
     * Creates a new Banner entity.
     *
     * @Route("/", name="banner_admin_create")
     * @Method("POST")
     * @Template("NiftyThriftyShopBundle:Admin/Banner:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Banner();
        $form = $this->createForm(new BannerType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('banner_admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Banner entity.
     *
     * @Route("/new", name="banner_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $bannerType = $em->getRepository('NiftyThriftyShopBundle:BannerType')->find('home_upper_right');
        $entity     = new Banner();
        $entity->setRotationStartTime(new \DateTime())
               ->setRotationEndTime(new \DateTime())
               ->setBannerTypeEntity($bannerType)
               ->setIsDefault('no');
        $form   = $this->createForm(new BannerType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Banner entity.
     *
     * @Route("/{id}", name="banner_admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Banner entity.
     *
     * @Route("/{id}/edit", name="banner_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $editForm = $this->createForm(new BannerType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Banner entity.
     *
     * @Route("/{id}", name="banner_admin_update")
     * @Method("PUT")
     * @Template("NiftyThriftyShopBundle:Admin/Banner:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new BannerType(), $entity);
        $editForm->bind($request);

        // See NewsletterController for what we are doing here.
        $existingImageData  = $request->request->get('existing_niftythrifty_shopbundle_bannertype');
        $uploadedFiles      = $request->files->get('niftythrifty_shopbundle_bannertype');
        if (!$existingImageData) $existingImageData = array();

        if (!($uploadedFiles['bannerImage'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('bannerImage', $existingImageData)) $existingImageData['bannerImage'] = null;
            $entity->setBannerImage($existingImageData['bannerImage']);
        }

        $deletedImageData = $request->request->get('deleted_niftythrifty_shopbundle_bannertype');
        if (!$deletedImageData) $deletedImageData = array();
        $deletedBannerImage = array_key_exists('bannerImage', $deletedImageData) ? 1 : 0;
        if ($deletedBannerImage) $entity->setBannerImage(null);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('banner_admin_edit', array('id' => $id)));
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
     * Deletes a Banner entity.
     *
     * @Route("/{id}", name="banner_admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NiftyThriftyShopBundle:Banner')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Banner entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('banner_admin'));
    }

    /**
     * Creates a form to delete a Banner entity by id.
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
}
