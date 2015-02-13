<?php

namespace Codete\FormGeneratorBundle;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * FormGenerator creates populated FormBuilder for any given
 * class. Say goodbye to writing boring FormType classes!
 * 
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FormGenerator
{
    /** @var Reader */
    private $annotationReader;
    
    /** @var FormFactoryInterface */
    private $formFactory;
    
    /** @var FormConfigurationModifierInterface[] */
    private $formConfigurationModifiers = array();
    
    /** @var FormFieldResolverInterface[] */
    private $formFieldResolvers = array();
    
    /** @var FormViewProviderInterface[] */
    private $formViewProviders = array();
    
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->annotationReader = new AnnotationReader();
        $this->formFactory = $formFactory;
    }
    
    /**
     * Adds modifier for form's configuration
     * 
     * @param FormConfigurationModifierInterface $modifier
     */
    public function addFormConfigurationModifier(FormConfigurationModifierInterface $modifier)
    {
        $this->formConfigurationModifiers[] = $modifier;
    }
    
    /**
     * Adds resolver for form's fields
     * 
     * @param FormFieldResolverInterface $resolver
     */
    public function addFormFieldResolver(FormFieldResolverInterface $resolver)
    {
        $this->formFieldResolvers[] = $resolver;
    }
    
    /**
     * Adds provider for defining default fields for form
     * 
     * @param FormViewProviderInterface $provider
     */
    public function addFormViewProvider(FormViewProviderInterface $provider)
    {
        $this->formViewProviders[] = $provider;
    }
    
    /**
     * Creates FormBuilder and populates it
     * 
     * @param object $model data object
     * @param string $form view to generate
     * @param array $context
     * @return FormBuilderInterface
     */
    public function createFormBuilder($model, $form = 'default', $context = array())
    {
        $fb = $this->formFactory->createBuilder('form', $model);
        $this->populateFormBuilder($fb, $model, $form, $context);
        return $fb;
    }
    
    /**
     * Creates named FormBuilder and populates it
     * 
     * @param string $name
     * @param object $model data object
     * @param string $form view to generate
     * @param array $context
     * @return FormBuilderInterface
     */
    public function createNamedFormBuilder($name, $model, $form = 'default', $context = array())
    {
        $fb = $this->formFactory->createNamedBuilder($name, 'form', $model);
        $this->populateFormBuilder($fb, $model, $form, $context);
        return $fb;
    }
    
    /**
     * Populates FormBuilder
     * 
     * @param FormBuilderInterface $fb
     * @param object $model
     * @param string $form view to generate
     */
    public function populateFormBuilder(FormBuilderInterface $fb, $model, $form = 'default', $context = array())
    {
        $fields = null;
        foreach ($this->formViewProviders as $provider) {
            if ($provider->supports($form, $model, $context)) {
                $fields = $provider->getFields($model, $context);
                break;
            }
        }
        if ($fields === null) {
            $fields = $this->getFields($model, $form);
        }
        $fields = $this->normalizeFields($fields);
        $configuration = $this->getFieldsConfiguration($model, $fields);
        foreach ($this->formConfigurationModifiers as $modifier) {
            if ($modifier->supports($model, $configuration, $context)) {
                $configuration = $modifier->modify($model, $configuration, $context);
            }
        }
        foreach ($configuration as $field => $options) {
            $type = null;
            if (isset($options['type'])) {
                $type = $options['type'];
                unset($options['type']);
            }
            foreach ($this->formFieldResolvers as $resolver) {
                if ($resolver->supports($model, $field, $type, $options, $context)) {
                    $fb->add($resolver->getFormField($fb, $field, $type, $options, $context));
                    continue 2;
                }
            }
            $fb->add($field, $type, $options);
        }
    }
    
    /**
     * Creates form configuration for $model for given $fields
     * 
     * @param object $model
     * @param array $fields
     * @return array
     */
    private function getFieldsConfiguration($model, $fields = array())
    {
        $configuration = $properties = array();
        $ro = new \ReflectionObject($model);
        if (empty($fields)) {
            $properties = $ro->getProperties();
        } else {
            foreach (array_keys($fields) as $field) {
                $properties[] = $ro->getProperty($field);
            }
        }
        foreach ($properties as $property) {
            $propertyIsListed = array_key_exists($property->getName(), $fields);
            if (!empty($fields) && !$propertyIsListed) {
                continue;
            }
            $fieldConfiguration = $this->annotationReader->getPropertyAnnotation($property, 'Codete\FormGeneratorBundle\Annotations\Display');
            if ($fieldConfiguration === null && !$propertyIsListed) {
                continue;
            }
            $configuration[$property->getName()] = (array)$fieldConfiguration;
            if (isset($fields[$property->getName()])) {
                $configuration[$property->getName()] = array_replace_recursive($configuration[$property->getName()], $fields[$property->getName()]);
            }
            // this variable comes from Doctrine\Common\Annotations\Annotation 
            unset($configuration[$property->getName()]['value']);
        }
        return $configuration;
    }
    
    /**
     * Gets field list from $model basing on its Form annotation.
     * 
     * @param object $model
     * @param string $form view
     * @return array list of fields (or empty for all fields)
     */
    private function getFields($model, $form)
    {
        $ro = new \ReflectionObject($model);
        $formAnnotation = $this->annotationReader->getClassAnnotation($ro, 'Codete\FormGeneratorBundle\Annotations\Form');
        if (($formAnnotation === null || !$formAnnotation->hasForm($form)) && $ro->getParentClass()) {
            while ($ro = $ro->getParentClass()) {
                $formAnnotation = $this->annotationReader->getClassAnnotation($ro, 'Codete\FormGeneratorBundle\Annotations\Form');
                if ($formAnnotation !== null && $formAnnotation->hasForm($form)) {
                    break;
                }
            }
        }
        if ($formAnnotation === null) {
            $formAnnotation = new Annotations\Form(array());
        }
        return $formAnnotation->getForm($form);
    }
    
    /**
     * Normalizes $fields array
     * 
     * @param array $_fields
     */
    private function normalizeFields($_fields)
    {
        $fields = array();
        foreach ($_fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $value;
            } else {
                $fields[$value] = array();
            }
        }
        return $fields;
    }
}
