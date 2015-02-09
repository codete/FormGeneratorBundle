<?php

namespace Codete\FormGeneratorBundle;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Objects implementing this interface can generate entire form
 * fields tailored to your needs (for instance add Transformers).
 * First registered Resolver that supports case wins, support of
 * other Resolvers is not tested.
 * 
 * @author Maciej Malarz <malarzm@gmail.com>
 */
interface FormFieldResolverInterface
{
    /**
     * Check if this FormFieldResolver can provide form field
     * 
     * @param object $model
     * @param string $field
     * @param string $type
     * @param array $options
     * @param array $context
     * @return bool
     */
    public function supports($model, $field, $type, $options, $context);
    
    /**
     * Creates FormBuilderInterface representing a field to be added to parent form
     * 
     * @param FormBuilderInterface $fb
     * @param string $field
     * @param string $type
     * @param array $options
     * @param array $context
     * @return FormBuilderInterface
     */
    public function getFormField(FormBuilderInterface $fb, $field, $type, $options, $context);
}
