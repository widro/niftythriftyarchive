<?php

namespace NiftyThrifty\ShopBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use NiftyThrifty\ShopBundle\Entity\State;

/**
 * This form is displayed from a user's basket.  It gives the user the ability to select
 * their shipping type.
 */
class OrderFormStep1Type extends AbstractType
{
    private $user;

    /**
     * Add the shipping manager so we can manage the shipping choices and the user so we can get the
     * user's payment profiles.
     *
     * @param   ShopBundle:Service:ShippingCostService
     * @param   ShopBundle:Entity:User
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orderAmount',            'hidden')
                ->add('orderAmountCoupon',      'hidden')
                ->add('orderAmountVat',         'hidden')
                ->add('orderAmountShipping',    'hidden')
                ->add('orderAmountCredits',     'hidden')
                ->add('orderAmountTotal',       'hidden')

                // Products and shipping
                ->add('orderShippingAddressFirstName',  'text',         array('label' => 'First name'))
                ->add('orderShippingAddressLastName',   'text',         array('label' => 'Last name'))
                ->add('orderShippingAddressStreet',     'textarea',     array('label' => 'Street'))
                ->add('orderShippingAddressCity',       'text',         array('label' => 'City'))
                ->add('orderShippingAddressState',
                      'choice',
                      array('empty_value' => 'Select state...',
                            'choices'     => State::getStateChoices(),
                            'label'       => 'State'))
                ->add('orderShippingAddressZipcode',    'text', array('label' => 'Zip'))
                ->add('orderShippingAddressCountry',    'text', array('label'       => 'Country',
                                                                      'read_only'   => true,
                                                                      'data'        => 'USA'))
                ->add('orderDuplicateBillingAndShipping','choice', array('expanded' => true,
                                                                         'multiple' => false,
                                                                         'mapped'   => false,
                                                                         'label'    => 'My billing and shipping address is the same.',
                                                                         'choices'  => array('yes' => 'Yes',
                                                                         					 'no' => 'No')))

                // Billing
                ->add('orderBillingAddressFirstName',   'text',     array('label' => 'First name'))
                ->add('orderBillingAddressLastName',    'text',     array('label' => 'Last name'))
                ->add('orderBillingAddressStreet',      'textarea', array('label' => 'Street'))
                ->add('orderBillingAddressCity',        'text',     array('label' => 'City'))
                ->add('orderBillingAddressState',
                      'choice',
                      array('empty_value' => 'Select state...',
                            'choices'     => State::getStateChoices(),
                            'label'       => 'State'))
                ->add('orderBillingAddressZipcode', 'text', array('label' => 'Zip'))
                ->add('orderBillingAddressCountry', 'text', array('label'       => 'Country',
                                                                  'read_only'   => true,
                                                                  'data'        => 'USA'))
                ->add('Continue',       'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NiftyThrifty\ShopBundle\Entity\Order'
        ));
    }

    /**
     * Get the years array for expiration dates.  Start with the current year and go out 10 years.
     *
     * return array
     */
    public function getYears()
    {
        $nowDate = new \DateTime();
        $thisYear = $nowDate->format('Y');
        $years = array();
        for ($i=0; $i<15; $i++) {
            $yearVal = $thisYear + $i;
            $years["$yearVal"] = $yearVal;
        }
        return $years;
    }

    public function getMonths()
    {
        $months = array();
        for ($i=1; $i<=12; $i++) {
            $months[$i] = $i;
        }
        return $months;
    }

    public function getName()
    {
        return 'orderFormStep1';
    }
}
