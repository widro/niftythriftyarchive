<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class CouponFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('couponId', 'filter_number_range')
            ->add('couponCode', 'filter_text')
            ->add('couponDateStart', 'filter_date_range')
            ->add('couponDateEnd', 'filter_date_range')
            ->add('couponPercent', 'filter_number_range')
            ->add('couponAmount', 'filter_number_range')
            ->add('couponQuantityLimited', 'filter_text')
            ->add('couponQuantity', 'filter_number_range')
            ->add('couponUnique', 'filter_text')
            ->add('couponDateAdd', 'filter_date_range')
            ->add('couponFreeShipping', 'filter_text')
            ->add('userId', 'filter_number_range')
        ;

        $listener = function(FormEvent $event)
        {
            // Is data empty?
            foreach ($event->getData() as $data) {
                if(is_array($data)) {
                    foreach ($data as $subData) {
                        if(!empty($subData)) return;
                    }
                }
                else {
                    if(!empty($data)) return;
                }
            }

            $event->getForm()->addError(new FormError('Filter empty'));
        };
        $builder->addEventListener(FormEvents::POST_BIND, $listener);
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_couponfiltertype';
    }
}
