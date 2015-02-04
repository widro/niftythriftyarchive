<?php

namespace NiftyThrifty\ShopBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AddressType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'NiftyThrifty\ShopBundle\Entity\Address'));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('addressFirstName', 'text', array('label'      => 'First name',
                                                        'max_length' => 50));
        $builder->add('addressLastName',  'text', array('label'      => 'Last name',
                                                        'max_length' => 50));

        $builder->add('addressStreet',    'text', array('label'      => 'Street',
                                                        'max_length' => 50));
        $builder->add('addressCity',      'text', array('label'      => 'City',
                                                        'max_length' => 50));

        $builder->add('state',   'entity', array('class'         => 'NiftyThriftyShopBundle:State',
                                                 'property'      => 'stateCode',
                                                 'label'         => 'State',
                                                 'query_builder' => function(EntityRepository $er) {
                                                                      return $er->createQueryBuilder('s')
                                                                                ->orderBy('s.stateCode', 'ASC');
                                                                    },
                                                 'empty_value'   => 'Select State'));

        $builder->add('addressZipcode',   'text', array('label'      => 'Zip',
                                                        'max_length' => 5));
        $builder->add('addressCountry', 'hidden', array('attr' => array('value' => 'USA')));
        $builder->add('Save', 'submit');
    }

    public function getName()
    {
        return 'address';
    }
}
