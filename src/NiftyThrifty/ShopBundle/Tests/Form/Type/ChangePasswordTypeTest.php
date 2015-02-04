<?php

namespace NiftyThrifty\ShopBundle\Tests\Form\Type;

use NiftyThrifty\ShopBundle\Form\Type\ChangePasswordType;
use NiftyThrifty\ShopBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class ChangePasswordTypeTest extends TypeTestCase
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
            'currentPassword'   => 'current',
            'userPassword'      => 'testpass',
        );

        $type = new ChangePasswordType();
        $form = $this->factory->create($type);

        $user = new User();
        $user->setUserPassword('testpass');

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
