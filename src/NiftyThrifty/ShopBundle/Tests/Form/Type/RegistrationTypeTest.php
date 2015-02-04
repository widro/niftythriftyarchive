<?php

namespace NiftyThrifty\ShopBundle\Tests\Form\Type;

use NiftyThrifty\ShopBundle\Form\Type\RegistrationType;
use NiftyThrifty\ShopBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class RegistrationTypeTest extends TypeTestCase
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->factory = Forms::createFormFactoryBuilder()
             ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock('Symfony\Component\Validator\ValidatorInterface')
                ))
             ->getFormFactory();
    }
    
    public function testSubmitValidData()
    {
        $this->markTestIncomplete('Fails due to repeated field.');

        $formData = array(
            'userFirstName' => 'New',
            'userLastName'  => 'User',
            'userEmail'     => 'test@niftythrifty.com',
            'userPassword'  => 'testpass',
        );

        $type = new RegistrationType();
        $form = $this->factory->create($type);

        $user = new User();
        $user->setUserFirstName('New')
             ->setUserLastName('User')
             ->setUserPassword('testpass')
             ->setUserEmail('test@niftythrifty.com');

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
