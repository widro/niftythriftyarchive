<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductTagtypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productTagtypeName')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\ProductTagtype'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_producttagtypetype';
    }
}
