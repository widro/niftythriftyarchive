<?php

namespace NiftyThrifty\ShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileImageUploadTypeExtension extends AbstractTypeExtension
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array('image_path', 'form_name'));
    }

    /**
     * Pass the image url to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('image_path', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData, $options['image_path']);
            } else {
                 $imageUrl = null;
            }

            // set an "image_url" variable that will be available when rendering this field
            $view->vars['image_url']  = $imageUrl;
            $view->vars['field_name'] = $options['form_name'] . '[' . $options['image_path'] . ']';
            $view->vars['id_name']    = $options['form_name'] . '_' . $options['image_path'];
        }
    }
    
    /**
     * Abstract class function.
     */
    public function getExtendedType()
    {
        return 'file';
    }
}