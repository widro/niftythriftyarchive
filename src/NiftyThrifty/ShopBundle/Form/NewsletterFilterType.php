<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class NewsletterFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newsletterId', 'filter_number_range')
            ->add('newsletterName', 'filter_text')
            ->add('newsletterTitle', 'filter_text')
            ->add('newsletterCollectionImg', 'filter_text')
            ->add('newsletterProduct1Img', 'filter_text')
            ->add('newsletterProduct1Link', 'filter_number_range')
            ->add('newsletterProduct2Img', 'filter_text')
            ->add('newsletterProduct2Link', 'filter_number_range')
            ->add('newsletterBlastId', 'filter_number_range')
            ->add('newsletterBlastScheduleTime', 'filter_text')
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
        return 'niftythrifty_shopbundle_newsletterfiltertype';
    }
}
