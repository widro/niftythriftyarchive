<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use NiftyThrifty\ShopBundle\Entity\Order;

/**
 * This form is displayed from a user's basket.  It gives the user the ability to select
 * their shipping type.  
 *
 * We removed all the options from this form.  We didn't want the users setting any options in the
 * the basket, so instead we just provide them a button to start an order.
 *  - Tom: 9/26/2013
 */
class StartOrderFromBasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Proceed to checkout','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    public function getName()
    {
        return 'basketToOrderForm';
    }
}
