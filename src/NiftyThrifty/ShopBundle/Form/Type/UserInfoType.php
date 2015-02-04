<?php

namespace NiftyThrifty\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserInfoType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'           => 'NiftyThrifty\ShopBundle\Entity\User',
                                     'validation_groups'    => array('accountInfo')));
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userFirstName', 'text', array('label'      => 'First name',
                                                     'max_length' => 50));
        $builder->add('userLastName',  'text', array('label'      => 'Last name',
                                                     'max_length' => 50));
        $builder->add('userEmail',     'email',array('label'      => 'E-mail address',
                                                     'max_length' => 100));
        $builder->add('Save',           'submit');
    }

    public function getName()
    {
        return 'userInfo';
    }
}
