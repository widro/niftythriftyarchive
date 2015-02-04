<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class ProductFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('productId', 'filter_text', array('label' => 'Inventory #'))
            ->add('productName', 'hidden')
            ->add('productDescription', 'hidden')
            ->add('productCategorySizeId', 'hidden')
            ->add('productTypeId', 'hidden')
            ->add('productOverallCondition', 'hidden')
            ->add('productPrice', 'hidden')
            ->add('productOldPrice', 'hidden')
            ->add('productDiscount', 'hidden')
            ->add('productDetailedConditionValue', 'hidden')
            ->add('productDetailedConditionDescription', 'hidden')
            ->add('productFabric', 'hidden')
            ->add('productMeasurements', 'hidden')
            ->add('productAvailability', 'hidden')
            ->add('productHeavy', 'hidden')
            ->add('productVisual1', 'hidden')
            ->add('productVisual1Large', 'hidden')
            ->add('productVisual2', 'hidden')
            ->add('productVisual2Large', 'hidden')
            ->add('productVisual3', 'hidden')
            ->add('productVisual3Large', 'hidden')
            ->add('collectionId', 'filter_text')
            ->add('designerId', 'hidden')
            ->add('productHashtag', 'hidden')
            ->add('productInstagramMediaIdNifty', 'hidden')
            ->add('productInstagramMediaIdCustomer', 'hidden')
            ->add('productTaxes', 'hidden')
            ->add('productTaxesActive', 'hidden')
            ->add('productCode', 'filter_text', array('label' => 'Item #'))
            ->add('productTagsize', 'hidden')
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
        return 'niftythrifty_shopbundle_productfiltertype';
    }
}
