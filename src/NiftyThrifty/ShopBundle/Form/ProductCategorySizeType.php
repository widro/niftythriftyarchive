<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductCategorySizeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productCategorySizeName')
            ->add('productCategorySizeValue')
            ->add('productCategorySizeOrder')
            ->add('productCategory', 'entity',  array('class'       => 'NiftyThriftyShopBundle:ProductCategory',
                                                      'empty_value' => 'Select category...'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\ProductCategorySize'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_productcategorysizetype';
    }
}
