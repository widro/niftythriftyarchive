<?php

namespace NiftyThrifty\ShopBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use NiftyThrifty\ShopBundle\Form\DataTransformer\UploadFieldToFileTransformer;
use Doctrine\ORM\EntityRepository;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new UploadFieldToFileTransformer;
        $builder
            ->add('description')
            ->add('url')
            ->add('isDefault',
                  'choice',
                  array('choices' => array('no' => 'No', 'yes' => 'Yes')))
            ->add($builder->create('bannerImage',   'file', array('image_path' => 'bannerImage',
                                                                  'form_name'  => $this->getName(),
                                                                  'required'    => false))
                          ->addModelTransformer($transformer))
            ->add('rotationStartTime',  'datetime')
            ->add('rotationEndTime',    'datetime')
            ->add('bannerTypeEntity',   'entity',   array('class' => 'NiftyThriftyShopBundle:BannerType',
                                                          'query_builder' => function(EntityRepository $er) {
                                                                         return $er->createQueryBuilder('bt')
                                                                                   ->orderBy('bt.name', 'ASC');
                                                                       },
                                                          'empty_value' => 'Select type...'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\Banner'
        ));
    }

    public function getName()
    {
        return 'niftythrifty_shopbundle_bannertype';
    }
}
