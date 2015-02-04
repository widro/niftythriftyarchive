<?php

namespace NiftyThrifty\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class DesignerType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'NiftyThrifty\ShopBundle\Entity\Designer'));
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('designerId',   'hidden', array('mapped' => false));
        $builder->add('designerName', 'text', array('label'     => 'Designer Name',
                                                    'max_length'=> 50));
        $builder->add('Save', 'submit');    
    }

    public function getName()
    {
        return 'designerForm';
    }
}
