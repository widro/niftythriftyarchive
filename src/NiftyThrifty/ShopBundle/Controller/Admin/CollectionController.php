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

use NiftyThrifty\ShopBundle\Entity\Collection;
use NiftyThrifty\ShopBundle\Form\CollectionType;
use NiftyThrifty\ShopBundle\Form\CollectionFilterType;

/**
 * Collection controller.
 *
 * @Route("/collection_admin")
 */
class CollectionController extends Controller
{
    /**
     * Lists all Collection entities.
     *
     * @Route("/", name="collection_admin")
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
        $filterForm = $this->createForm(new CollectionFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('NiftyThriftyShopBundle:Collection')->createQueryBuilder('e')->orderBy('e.collectionId', 'DESC');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('CollectionControllerFilter');
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
                $session->set('CollectionControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('CollectionControllerFilter')) {
                $filterData = $session->get('CollectionControllerFilter');
                $filterForm = $this->createForm(new CollectionFilterType(), $filterData);
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
            return $me->generateUrl('collection_admin', array('page' => $page));
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
     * Creates a new Collection entity.
     *
     * @Route("/", name="collection_admin_create")
     * @Method("POST")
     * @Template("NiftyThriftyShopBundle:Admin/Collection:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Collection();
        $form = $this->createForm(new CollectionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('collection_admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Collection entity.
     *
     * @Route("/new", name="collection_admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Collection();
        $entity->setCollectionType('Women')
               ->setCollectionActive('no')
               ->setIsShop('no')
               ->setCollectionDateStart(new \DateTime())
               ->setCollectionDateEnd(new \DateTime());
        $form   = $this->createForm(new CollectionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Collection entity.
     *
     * @Route("/{id}", name="collection_admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Collection')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Collection entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Collection entity.
     *
     * @Route("/{id}/edit", name="collection_admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Collection')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Collection entity.');
        }

        $editForm = $this->createForm(new CollectionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Collection entity.
     *
     * @Route("/{id}", name="collection_admin_update")
     * @Method("PUT")
     * @Template("NiftyThriftyShopBundle:Admin/Collection:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NiftyThriftyShopBundle:Collection')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Collection entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CollectionType(), $entity);
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
        $existingImageData = $request->request->get('existing_niftythrifty_shopbundle_collectiontype');
        $uploadedFiles     = $request->files->get('niftythrifty_shopbundle_collectiontype');
        if (!$existingImageData) $existingImageData = array();

        // If a file has not been uploaded, set the path to its original path string which we stored in a hidden variable
        if (!($uploadedFiles['collectionVisualHomeHero'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('collectionVisualHomeHero', $existingImageData)) $existingImageData['collectionVisualHomeHero'] = null;
            $entity->setCollectionVisualHomeHero($existingImageData['collectionVisualHomeHero']);
        }
        if (!($uploadedFiles['collectionVisualSaleHero'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('collectionVisualSaleHero', $existingImageData)) $existingImageData['collectionVisualSaleHero'] = null;
            $entity->setCollectionVisualSaleHero($existingImageData['collectionVisualSaleHero']);
        }
        if (!($uploadedFiles['collectionVisualMainPanel'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('collectionVisualMainPanel', $existingImageData)) $existingImageData['collectionVisualMainPanel'] = null;
            $entity->setCollectionVisualMainPanel($existingImageData['collectionVisualMainPanel']);
        }
        if (!($uploadedFiles['collectionVisualMainPanelBw'] instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
            if (!array_key_exists('collectionVisualMainPanelBw', $existingImageData)) $existingImageData['collectionVisualMainPanelBw'] = null;
            $entity->setCollectionVisualMainPanelBw($existingImageData['collectionVisualMainPanelBw']);
        }

        // Next handle deletes - if you add a new image and check the box, you will get no image.
        $deletedImageData  = $request->request->get('delete_niftythrifty_shopbundle_collectiontype');
        if (!$deletedImageData) $deletedImageData = array();
        $deletedVisualHomeHero      = array_key_exists('collectionVisualHomeHero',      $deletedImageData) ? 1 : 0;
        $deletedVisualSaleHero      = array_key_exists('collectionVisualSaleHero',      $deletedImageData) ? 1 : 0;
        $deletedVisualMainPanel     = array_key_exists('collectionVisualMainPanel',     $deletedImageData) ? 1 : 0;
        $deletedVisualMainPanelBw   = array_key_exists('collectionVisualMainPanelBw',   $deletedImageData) ? 1 : 0;
        if ($deletedVisualHomeHero)     $entity->setCollectionVisualHomeHero(null);
        if ($deletedVisualSaleHero)     $entity->setCollectionVisualSaleHero(null);
        if ($deletedVisualMainPanel)    $entity->setCollectionVisualMainPanel(null);
        if ($deletedVisualMainPanelBw)  $entity->setCollectionVisualMainPanelBw(null);
        
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('collection_admin_edit', array('id' => $id)));
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
     * Deletes a Collection entity.
     *
     * @Route("/{id}", name="collection_admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NiftyThriftyShopBundle:Collection')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Collection entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('collection_admin'));
    }

    /**
     * Creates a form to delete a Collection entity by id.
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
