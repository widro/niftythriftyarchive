<?php

namespace NiftyThrifty\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class RegistrationType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'           => 'NiftyThrifty\ShopBundle\Entity\User',
                                     'validation_groups'    => array('accountInfo', 'passwordCheck')));
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userFirstName', 'text', array('label'      => 'First name',
                                                     'max_length' => 50));
        $builder->add('userLastName',  'text', array('label'      => 'Last name',
                                                     'max_length' => 50));
        $builder->add('userEmail',     'email',array('label'      => 'E-mail address',
                                                     'max_length' => 100));
        $builder->add('userPassword', 'repeated', array('type'             => 'password',
                                                        'invalid_message'  => 'The password fields must match.',
                                                        'required'         => true,
                                                        'first_options'    => array('label' => 'New password'),
                                                        'second_options'   => array('label' => 'Confirm new password')));
        $builder->add('inviteToken',    'hidden', array('mapped' => false));
        $builder->add('tokenType',      'hidden', array('mapped' => false));
        $builder->add('referSite',      'hidden', array('mapped' => false));
        $builder->add('userFbId',      'hidden', array('mapped' => true));
        $builder->add('Submit', 'submit');
    }

    public function getName()
    {
        return 'registration';
    }
}