<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductTagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productTagName')
            ->add('productTagSlug')
            ->add('productTagtype')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\ProductTag'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_producttagtype';
    }
}
