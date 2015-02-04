<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CouponType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('couponCode')
            ->add('couponDateStart')
            ->add('couponDateEnd')
            ->add('couponPercent', 'text', array('required' => false))
            ->add('couponAmount',  'text', array('required' => false))
            ->add('couponQuantityLimited',
                  'choice',
                  array('choices' => array('true'  => 'true', 
                                           'false' => 'false')))
            ->add('couponQuantity')
            ->add('couponUnique',
                  'choice',
                  array('choices' => array('true'  => 'true', 
                                           'false' => 'false')))
            ->add('couponFreeShipping',
                  'choice',
                  array('choices' => array('true'  => 'true', 
                                           'false' => 'false')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\Coupon'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_coupontype';
    }
}
