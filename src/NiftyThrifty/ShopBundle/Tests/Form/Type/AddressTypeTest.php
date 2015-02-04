<?php

namespace NiftyThrifty\ShopBundle\Tests\Form\Type;

use NiftyThrifty\ShopBundle\Form\Type\AddressType;
use NiftyThrifty\ShopBundle\Entity\Address;
use Symfony\Component\Form\Test\TypeTestCase;

class AddressTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $this->markTestIncomplete('Fails due to entity field type.');

        $formData = array(
            'addressFirstName'  => 'New',
            'addressLastName'   => 'User',
            'addressStreet'     => '123 Test Street',
            'addressCity'       => 'Testville',
            'state'             => '1',
            'addressZipcode'    => '12345',
            'addressCountry'    => 'USA',
        );

        $type = new AddressType();
        $form = $this->factory->create($type);

        $address = new Address();
        $address->setAddressFirstName('New')
                ->setAddressLastName('User')
                ->setAddressStreet('123 Test Street')
                ->setAddressCity('Testville')
                ->setState('1')
                ->setAddressZipCode('12345')
                ->setAddressCountry('USA');

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($address, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
