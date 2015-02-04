<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use NiftyThrifty\ShopBundle\Form\DataTransformer\UploadFieldToFileTransformer;
use NiftyThrifty\ShopBundle\Form\DataTransformer\CollectionTransformer;
use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UploadFieldToFileTransformer;
        $builder
            ->add('productCode', 'text', array('required' => false, 'label' => 'Item #'))
            ->add('collection',   'entity', array('class'         => 'NiftyThriftyShopBundle:Collection',
                                                    'property'      => 'collectionName',
                                                    'query_builder' => function(EntityRepository $er) {
                                                                         return $er->createQueryBuilder('c')
                                                                                   ->orderBy('c.collectionName', 'ASC');
                                                                       },
                                                    'empty_value'   => 'Select Collection...'))
            ->add('productName', 'text', array('label' => 'Name of Item'))
            ->add('designer',   'entity', array('class'         => 'NiftyThriftyShopBundle:Designer',
                                                  'property'      => 'designerName',
                                                  'label'         => 'Designer',
                                                  'required'      => false,
                                                  'query_builder' => function(EntityRepository $er) {
                                                                       return $er->createQueryBuilder('d')
                                                                                 ->orderBy('d.designerName', 'ASC');
                                                                     },
                                                  'empty_value'   => 'Select Designer...'))

            ->add('productDescription')
            ->add('productTagsize', 'text', array('required' => false))

            ->add('productCategorySize',   'entity', array('class'         => 'NiftyThriftyShopBundle:ProductCategorySize',
                                                             'property'      => 'productCategorySizeName',
                                                             'query_builder' => function(EntityRepository $er) {
                                                                                    return $er->createQueryBuilder('pcs')
                                                                                              ->orderBy('pcs.productCategorySizeName', 'ASC');
                                                                                },
                                                             'empty_value'   => 'Select Size...'))

            ->add('productMeasurements')
            ->add('productPrice')
            ->add('productOldPrice', 'text', array('required' => false))
            ->add('productOverallCondition', 'text')
            ->add('productDiscount', 'text', array('required' => false))
            ->add('productDetailedConditionValue',       'integer')
            ->add('productDetailedConditionDescription', 'text')
            ->add('productFabric')
            ->add('productAvailability',    'choice',   array('choices' => array('sale'     => 'Sale',
                                                                                 'sold'     => 'Sold',
                                                                                 'reserved' => 'Reserved')))
            ->add('productHeavy',           'choice',   array('choices' => array('yes' => 'Yes',
                                                                                 'no'  => 'No'),
                                                              'empty_value' => 'Select one...'))
            ->add($builder->create('productVisual1Large',   'file', array('image_path'  => 'productVisual1Large',
                                                                          'form_name'   => $this->getName(),
                                                                          'required'    => false))
                          ->addModelTransformer($transformer))
            ->add($builder->create('productVisual2Large',   'file', array('image_path'  => 'productVisual2Large',
                                                                          'form_name'   => $this->getName(),
                                                                          'required'    => false))
                          ->addModelTransformer($transformer))
            ->add($builder->create('productVisual3Large',   'file', array('image_path'  => 'productVisual3Large',
                                                                          'form_name'   => $this->getName(),
                                                                          'required'    => false))
                          ->addModelTransformer($transformer))
            ->add('productTaxes',           'text',     array('read_only' => true))
            ->add('productTaxesActive',     'choice',   array('choices' => array('yes' => 'Yes',
                                                                                 'no'  => 'No'),
                                                              'empty_value' => 'Select one...'))
            ->add('productTags',            'entity',   array('class'           => 'NiftyThriftyShopBundle:ProductTag',
                                                              'property'        => 'productTagName',
                                                              'query_builder'   => function(EntityRepository $er) {
                                                                                    return $er->createQueryBuilder('pt')
                                                                                              ->orderBy('pt.productTagtypeId, pt.productTagName', 'ASC');
                                                                                   },
                                                              'expanded'        => true, 
                                                              'multiple'        => true,
                                                              'required'        => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\Product'
        ));
        $resolver->setRequired(array(
            'em',
        ));
        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_producttype';
    }
}
