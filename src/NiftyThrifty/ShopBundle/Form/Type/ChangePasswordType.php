<?php

namespace NiftyThrifty\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ChangePasswordType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class'           => 'NiftyThrifty\ShopBundle\Entity\User',
                                     'validation_groups'    => array('passwordCheck')));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', 'password', array('max_length' => 16,
                                                           'mapped'     => false,
                                                           'label'      => 'Current password'));
        $builder->add('userPassword', 'repeated', array('type'             => 'password',
                                                        'invalid_message'  => 'The password fields must match.',
                                                        'required'         => true,
                                                        'first_options'    => array('label' => 'New password'),
                                                        'second_options'   => array('label' => 'Confirm new password')));
        $builder->add('Save', 'submit');
    }

    public function getName()
    {
        return 'changePassword';
    }
}
