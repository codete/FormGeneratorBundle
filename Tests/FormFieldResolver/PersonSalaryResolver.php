<?php

namespace Codete\FormGeneratorBundle\Tests\FormFieldResolver;

use Codete\FormGeneratorBundle\FormFieldResolver;
use Codete\FormGeneratorBundle\Tests\Model\Person;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

class PersonSalaryResolver implements FormFieldResolver
{
    public function getFormField(FormBuilderInterface $fb, $field, $type, $options, $context) 
    {
        $transformer = new DummyDataTransformer();
        return $fb->create($field, $type, $options)
                ->addViewTransformer($transformer);
    }

    public function supports($model, $field, $type, $options, $context) 
    {
        return $model instanceof Person && $field === 'salary';
    }
}

class DummyDataTransformer implements DataTransformerInterface
{
    public function reverseTransform($value) {
        
    }

    public function transform($value) {
        
    }

}
