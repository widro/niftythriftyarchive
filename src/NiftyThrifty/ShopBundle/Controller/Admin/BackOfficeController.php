<?php

namespace NiftyThrifty\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormError;

use NiftyThrifty\ShopBundle\Entity\Designer;
use NiftyThrifty\ShopBundle\Form\Type\DesignerType;
/**
 * The Back Office Controller handles adding new products to the website.  This is the main
 * interface for adding new merchandice.
 */
class BackOfficeController extends Controller
{
    /**
     * This is the index page of the back office.  It displays links to the forms that are used
     * to enter products and such in to the system.
     *
     * Current forms:
     *  - Add product
     *  - Add collection
     *  - Add category (with sizes)
     *  - Add designer
     *  - Add tags
     *  - Add newsletter
     *  - Product/Collection
     *  - Coupon/Credit
     *
     * @Route("/", name="back_office_home")
     */
    public function indexAction()
    {
        return $this->render('NiftyThriftyShopBundle:Admin/BackOffice:index.html.twig');
    }

    public function showNewProductForm()
    {

    }

    public function showEditProductForm()
    {

    }

    public function saveProduct()
    {

    }

    public function showNewCollectionForm()
    {

    }

    public function showEditCollectionForm()
    {

    }

    public function saveCollection()
    {

    }

    /**
     * Add a new designer.
     *
     * @Route("/add_designer", name="add_designer")
     */
    public function showNewDesignerForm()
    {
        $designer = new Designer();

        $designerForm = $this->createForm(new DesignerType(),
                                          new Designer(),
                                          array('method' => 'post',
                                                'action' => $this->generateUrl('save_designer')));

        return $this->render('NiftyThriftyShopBundle:Admin/BackOffice:backOfficeForm.html.twig',
                             array('title' => 'Add designer',
                                   'form'  => $designerForm->createView()));
    }

    /**
     * Select a designer to edit, or actually edit a designer.
     *
     * @Route("/edit_designer", name="edit_designer")
     */
    public function showEditDesignerForm(Request $request)
    {
        $formData   = $request->request->get('form');
        $designerId = $formData['designerId'];

        // If designer id has been defined, display the edit form.
        if ($designerId) {
            $designer = $this->getDoctrine()
                             ->getRepository('NiftyThriftyShopBundle:Designer')
                             ->find($designerId);
            $designerForm = $this->createForm(new DesignerType(),
                                              $designer,
                                              array('method' => 'post',
                                                    'action' => $this->generateUrl('save_designer')));

        // Otherwise, display the "select a designer form.
        } else {
            $designerForm = $this->createFormBuilder()
                                 ->add('designerId', 'entity', array('class'        => 'NiftyThriftyShopBundle:Designer',
                                                                     'property'     => 'designerName',
                                                                     'query_builder'=> function(EntityRepository $er) {
                                                                                           return $er->createQueryBuilder('d')
                                                                                                     ->orderBy('d.designerName','ASC');
                                                                                       }
                                                                    ))
                                 ->add('Edit', 'submit')
                                 ->setMethod('POST')
                                 ->setAction($this->generateUrl('edit_designer'))
                                 ->getForm();
        }

        return $this->render('NiftyThriftyShopBundle:Admin/BackOffice:backOfficeForm.html.twig',
                             array('title' => 'Edit designer',
                                   'form'  => $designerForm->createView()));
    }

    /**
     * Save designer.  Should handle both insert and update based on the presense of a designer id
     *
     * @Route("/save_designer", name="save_designer")
     */
    public function saveDesigner(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $designerForm = $this->createForm(new DesignerType(),
                                          new Designer(),
                                          array('method' => 'POST',
                                                'action' => $this->generateUrl('save_designer')));
        $designerForm->handleRequest($request);
        if ($designerForm->isValid()) {
            $designerId = $designerForm->getData('designerId');
            if (!$designerId) {
                $em->persist($designer);
            } else {
                $designerEdit = $this->getDoctrine()
                                     ->getRepository('NiftyThriftyShopBundle:Designer')
                                     ->find($designerId);
                $designerEdit->setDesignerName($designer->getDesignerName());
            }
            $em->flush();
            return $this->redirect($this->generateUrl('back_office_home'));

        } else {
            $editType = $designerForm->getData('designerId') ? 'Edit' : 'Add';
            return $this->render('NiftyThriftyShopBundle:Admin/BackOffice:backOfficeForm.html.twig',
                                 array('title' => "$editType designer",
                                       'form'  => $designerForm->createView()));
        }
    }

    public function showAddCategoryForm()
    {

    }

    public function showEditCategoryForm()
    {

    }

    public function saveCategory()
    {

    }
}
