<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productCategoryName')
            ->add('inNavigation',
                  'choice',
                  array('choices' => array('no' => 'No',
                                           'yes'=> 'Yes')))
            ->add('navigationOrder')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\ProductCategory'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_productcategorytype';
    }
}
