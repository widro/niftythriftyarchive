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

use NiftyThrifty\ShopBundle\Entity\Coupon;
use NiftyThrifty\ShopBundle\Form\CouponType;
use NiftyThrifty\ShopBundle\Form\CouponFilterType;

/**
 * Coupon controller.
 *
 * @Route("/coupon_admin")
 */
class CouponController extends Controller
{
    /**
     * Lists all Coupon entities.
     *
     * @Route("/", name="coupon_admin")
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
        $filterForm = $this->createForm(new CouponFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('NiftyThriftyShopBundle:Coupon')->createQueryBuilder('e');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('CouponControllerFilter');
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
                $session->set('CouponControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('CouponControllerFilter')) {
                $filterData = $session->get('CouponControllerFilter');
                $filterForm = $this->createForm(new CouponFilterType(), $filterData);
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
            return $me->generateUrl('coupon_admin', array('page' => $page));
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
     * Creates a new Coupon entity.
     *
     * @Route("/", name="coupon_admin_create")
     * @Method("POST")
     * @Template("NiftyThriftyShopBundle:Admin/Coupon:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Coupon();
        $entity->setCouponDateAdd(new \DateTime());
        $form = $this->createForm(new CouponType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('coupon_admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Coupon entity.
     *
     * @Route("/new", name="coupon_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Coupon();
        $entity->setCouponQuantityLimited('false')
               ->setCouponUnique('false')
               ->setCouponFreeShipping('false')
               ->setCouponDateStart(new \DateTime())
               ->setCouponDateEnd(new \DateTime());
        $form   = $this->createForm(new CouponType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Coupon entity.
     *
     * @Route("/{id}", name="coupon_admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Coupon entity.
     *
     * @Route("/{id}/edit", name="coupon_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $editForm = $this->createForm(new CouponType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Coupon entity.
     *
     * @Route("/{id}", name="coupon_admin_update")
     * @Method("PUT")
     * @Template("NiftyThriftyShopBundle:Admin/Coupon:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Coupon')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Coupon entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CouponType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('coupon_admin_edit', array('id' => $id)));
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
     * Deletes a Coupon entity.
     *
     * @Route("/{id}", name="coupon_admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NiftyThriftyShopBundle:Coupon')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Coupon entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('coupon_admin'));
    }

    /**
     * Creates a form to delete a Coupon entity by id.
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
