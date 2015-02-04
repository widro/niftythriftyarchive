<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use NiftyThrifty\ShopBundle\Form\DataTransformer\UploadFieldToFileTransformer;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UploadFieldToFileTransformer;
        $builder
            ->add('collectionName')
            ->add('collectionCode')
            ->add('isShop',
                   'choice',
                   array('choices' => array('no' => 'No',
                                            'yes'=> 'Yes')))
            ->add('collectionDescription')
            ->add('collectionType',
                  'choice',
                  array('choices' => array('Women' => 'Women',
                                           'Men'   => 'Men',
                                           'Home'  => 'Home')))
            ->add('collectionDateStart')
            ->add('collectionDateEnd')
            ->add('collectionActive',
                  'choice',
                  array('choices' => array('no' => 'No',
                                           'yes'=> 'Yes')));


        $builder->add($builder->create('collectionVisualHomeHero', 'file', array('required'     => false,
                                                                                  'label'  => 'Collection Homepage Hero',
                                                                                 'image_path'   => 'collectionVisualHomeHero',
                                                                                 'form_name'    => $this->getName()))
                              ->addModelTransformer($transformer));
        $builder->add($builder->create('collectionVisualSaleHero', 'file', array('required'    => false,
                                                                                  'label'  => 'Collection Page Hero',
                                                                                  'image_path'  => 'collectionVisualSaleHero',
                                                                                  'form_name'   => $this->getName()))
                              ->addModelTransformer($transformer));
        $builder->add($builder->create('collectionVisualMainPanel', 'file', array('required'    => false,
                                                                                  'label'  => 'Collection Panel (color)',
                                                                                  'image_path'  => 'collectionVisualMainPanel',
                                                                                  'form_name'   => $this->getName()))
                              ->addModelTransformer($transformer));
        $builder->add($builder->create('collectionVisualMainPanelBw', 'file', array('required'    => false,
                                                                                  'label'  => 'Collection Panel (black & white)',
                                                                                  'image_path'  => 'collectionVisualMainPanelBw',
                                                                                  'form_name'   => $this->getName()))
                              ->addModelTransformer($transformer));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\Collection'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_collectiontype';
    }
}
