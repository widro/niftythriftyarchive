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

use NiftyThrifty\ShopBundle\Entity\ProductTag;
use NiftyThrifty\ShopBundle\Form\ProductTagType;
use NiftyThrifty\ShopBundle\Form\ProductTagFilterType;

/**
 * ProductTag controller.
 *
 * @Route("/product_tag_admin")
 */
class ProductTagController extends Controller
{
    /**
     * Lists all ProductTag entities.
     *
     * @Route("/", name="product_tag_admin")
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
        $filterForm = $this->createForm(new ProductTagFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('NiftyThriftyShopBundle:ProductTag')->createQueryBuilder('e');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ProductTagControllerFilter');
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
                $session->set('ProductTagControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('ProductTagControllerFilter')) {
                $filterData = $session->get('ProductTagControllerFilter');
                $filterForm = $this->createForm(new ProductTagFilterType(), $filterData);
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
            return $me->generateUrl('product_tag_admin', array('page' => $page));
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
     * Creates a new ProductTag entity.
     *
     * @Route("/", name="product_tag_admin_create")
     * @Method("POST")
     * @Template("NiftyThriftyShopBundle:Admin/ProductTag:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new ProductTag();
        $form = $this->createForm(new ProductTagType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('product_tag_admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new ProductTag entity.
     *
     * @Route("/new", name="product_tag_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProductTag();
        $form   = $this->createForm(new ProductTagType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProductTag entity.
     *
     * @Route("/{id}", name="product_tag_admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:ProductTag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductTag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProductTag entity.
     *
     * @Route("/{id}/edit", name="product_tag_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:ProductTag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductTag entity.');
        }

        $editForm = $this->createForm(new ProductTagType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ProductTag entity.
     *
     * @Route("/{id}", name="product_tag_admin_update")
     * @Method("PUT")
     * @Template("NiftyThriftyShopBundle:Admin/ProductTag:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:ProductTag')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductTag entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductTagType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('product_tag_admin_edit', array('id' => $id)));
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
     * Deletes a ProductTag entity.
     *
     * @Route("/{id}", name="product_tag_admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NiftyThriftyShopBundle:ProductTag')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductTag entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('product_tag_admin'));
    }

    /**
     * Creates a form to delete a ProductTag entity by id.
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
