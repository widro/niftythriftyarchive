<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class CollectionFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('collectionId', 'filter_number_range')
            ->add('collectionCode', 'filter_text')
            ->add('isShop', 'filter_choice',array('choices' => array('no' => 'No',
                                                                     'yes'=> 'Yes')))
            ->add('collectionName', 'filter_text')
            ->add('collectionDescription', 'filter_text')
            ->add('collectionType', 'filter_choice',array('choices' => array('Women' => 'Women',
                                                                             'Men'=> 'Men',
                                                                             'Home'=> 'Home')))
            ->add('collectionArchetype', 'filter_choice',array('choices' => array('None' => 'None',
                                                                                  'Boho'=> 'Boho',
                                                                                  'Classic'=> 'Classic',
                                                                                  'Rocker'=> 'Rocker')))
            ->add('collectionDateStart', 'filter_date_range')
            ->add('collectionDateEnd', 'filter_date_range')
            ->add('collectionActive', 'filter_choice',array('choices' => array('no' => 'No',
                                                                     'yes'=> 'Yes')))
            ->add('collectionVisualHomeHero', 'filter_text')
            ->add('collectionVisualMainPanel', 'filter_text')
            ->add('collectionVisualSaleHero', 'filter_text')
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
        return 'niftythrifty_shopbundle_collectionfiltertype';
    }
}
