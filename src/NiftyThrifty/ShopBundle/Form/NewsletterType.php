<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use NiftyThrifty\ShopBundle\Form\DataTransformer\UploadFieldToFileTransformer;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UploadFieldToFileTransformer;
        $builder
            ->add('newsletterName')
            ->add('newsletterTitle')
            ->add('newsletterLink')
            ->add($builder->create('newsletterCollectionImg',   'file', array('image_path'  => 'newsletterCollectionImg',
                                                                              'form_name'   => $this->getName(),
                                                                              'required'    => false))
                          ->addModelTransformer($transformer))
            ->add($builder->create('newsletterProduct1Img',     'file', array('image_path'  => 'newsletterProduct1Img',
                                                                              'form_name'   => $this->getName(),
                                                                              'required'    => false))
                          ->addModelTransformer($transformer))
            ->add('newsletterProduct1Link')
            ->add($builder->create('newsletterProduct2Img',     'file', array('image_path'  => 'newsletterProduct2Img',
                                                                              'form_name'   => $this->getName(),
                                                                              'required'    => false))
                          ->addModelTransformer($transformer))
            ->add('newsletterProduct2Link')
            ->add('newsletterBlastId')
            ->add('newsletterBlastScheduleTime', 'datetime', array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\Newsletter'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_newslettertype';
    }
}
