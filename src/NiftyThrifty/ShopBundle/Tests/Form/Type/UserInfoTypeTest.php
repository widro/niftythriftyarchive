<?php

namespace NiftyThrifty\ShopBundle\Tests\Form\Type;

use NiftyThrifty\ShopBundle\Form\Type\UserInfoType;
use NiftyThrifty\ShopBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class UserInfoTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'userFirstName' => 'New',
            'userLastName'  => 'User',
            'userEmail'     => 'test@niftythrifty.com',
        );

        $type = new UserInfoType();
        $form = $this->factory->create($type);

        $user = new User();
        $user->setUserFirstName('New')
             ->setUserLastName('User')
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