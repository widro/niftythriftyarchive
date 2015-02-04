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

use NiftyThrifty\ShopBundle\Entity\Product;
use NiftyThrifty\ShopBundle\Form\ProductType;
use NiftyThrifty\ShopBundle\Form\ProductFilterType;

/**
 * Product controller.
 *
 * @Route("/product_admin")
 */
class ProductController extends Controller
{
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="product_admin")
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
        $filterForm = $this->createForm(new ProductFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('NiftyThriftyShopBundle:Product')->createQueryBuilder('e')->orderBy('e.productId', 'DESC');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ProductControllerFilter');
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
                $session->set('ProductControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('ProductControllerFilter')) {
                $filterData = $session->get('ProductControllerFilter');
                $filterForm = $this->createForm(new ProductFilterType(), $filterData);
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
            return $me->generateUrl('product_admin', array('page' => $page));
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
     * Creates a new Product entity.
     *
     * @Route("/", name="product_admin_create")
     * @Method("POST")
     * @Template("NiftyThriftyShopBundle:Admin/Product:new.html.twig")
     */
    public function createAction(Request $request)
    {
        //print_r($request);
        $entity  = new Product();
            $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ProductType(), $entity, array('em' => $em));
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('product_admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="product_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Product();
            $em = $this->getDoctrine()->getManager();
        $entity->setProductOverallCondition('Vintage')
               ->setProductDetailedConditionValue('4')
               ->setProductDetailedConditionDescription('Good condition')
               ->setProductHeavy('no')
               ->setProductTaxes('8.875')
               ->setProductTaxesActive('yes');
        $form   = $this->createForm(new ProductType(), $entity, array('em' => $em));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", name="product_admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="product_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->createForm(new ProductType(), $entity, array('em' => $em));
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}", name="product_admin_update")
     * @Method("PUT")
     * @Template("NiftyThriftyShopBundle:Admin/Product:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductType(), $entity, array('em' => $em));
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
        $existingImageData = $request->request->get('existing_niftythrifty_shopbundle_producttype');
        $uploadedFiles     = $request->files->get('niftythrifty_shopbundle_producttype');
        if (!$existingImageData) $existingImageData = array();

        // If a file has not been uploaded, set the path to its original path string which we stored in a hidden variable
        if (!($uploadedFiles['productVisual1Large'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('productVisual1Large', $existingImageData)) $existingImageData['productVisual1Large'] = null;
            $entity->setProductVisual1Large($existingImageData['productVisual1Large']);
        }
        if (!($uploadedFiles['productVisual2Large'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('productVisual2Large', $existingImageData)) $existingImageData['productVisual2Large'] = null;
            $entity->setProductVisual2Large($existingImageData['productVisual2Large']);
        }
        if (!($uploadedFiles['productVisual3Large'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('productVisual3Large', $existingImageData)) $existingImageData['productVisual3Large'] = null;
            $entity->setProductVisual3Large($existingImageData['productVisual3Large']);
        }

        // Next handle deletes - if you add a new image and check the box, you will get no image.
        $deletedImageData  = $request->request->get('delete_niftythrifty_shopbundle_producttype');
        if (!$deletedImageData) $deletedImageData = array();
        $deletedVisual1Large        = array_key_exists('productVisual1Large',   $deletedImageData) ? 1 : 0;
        $deletedVisual2Large        = array_key_exists('productVisual2Large',   $deletedImageData) ? 1 : 0;
        $deletedVisual3Large        = array_key_exists('productVisual3Large',   $deletedImageData) ? 1 : 0;
        if ($deletedVisual1Large)   $entity->setProductVisual1Large(null);
        if ($deletedVisual2Large)   $entity->setProductVisual2Large(null);
        if ($deletedVisual3Large)   $entity->setProductVisual3Large(null);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('product_admin_edit', array('id' => $id)));
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
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="product_admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NiftyThriftyShopBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('product_admin'));
    }

    /**
     * Creates a form to delete a Product entity by id.
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
