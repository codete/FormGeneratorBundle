<?php

namespace Codete\FormGeneratorBundle;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * FormGenerator creates populated FormBuilder for any given class. Say goodbye to writing boring FormType classes!
 * 
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FormGenerator
{
    /** @var Reader */
    private $annotationReader;
    
    /** @var FormFactoryInterface */
    private $formFactory;
    
    /** @var FormConfigurationModifierInterface[][] */
    private $formConfigurationModifiers = array();
    
    /** @var FormFieldResolverInterface[][] */
    private $formFieldResolvers = array();
    
    /** @var FormViewProviderInterface[][] */
    private $formViewProviders = array();

    /** @var bool */
    private $needsSorting = false;

    /** @var FormConfigurationModifierInterface[] */
    private $sortedFormConfigurationModifiers = array();

    /** @var FormFieldResolverInterface[] */
    private $sortedFormFieldResolvers = array();

    /** @var FormViewProviderInterface[] */
    private $sortedFormViewProviders = array();
    
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->annotationReader = new AnnotationReader();
        $this->formFactory = $formFactory;
    }
    
    /**
     * Adds modifier for form's configuration
     * 
     * @param FormConfigurationModifierInterface $modifier
     * @param int $priority
     */
    public function addFormConfigurationModifier(FormConfigurationModifierInterface $modifier, $priority = 0)
    {
        $this->formConfigurationModifiers[$priority][] = $modifier;
        $this->needsSorting = true;
    }
    
    /**
     * Adds resolver for form's fields
     * 
     * @param FormFieldResolverInterface $resolver
     * @param int $priority
     */
    public function addFormFieldResolver(FormFieldResolverInterface $resolver, $priority = 0)
    {
        $this->formFieldResolvers[$priority][] = $resolver;
        $this->needsSorting = true;
    }
    
    /**
     * Adds provider for defining default fields for form
     * 
     * @param FormViewProviderInterface $provider
     * @param int $priority
     */
    public function addFormViewProvider(FormViewProviderInterface $provider, $priority = 0)
    {
        $this->formViewProviders[$priority][] = $provider;
        $this->needsSorting = true;
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
     * @param array $context
     */
    public function populateFormBuilder(FormBuilderInterface $fb, $model, $form = 'default', $context = array())
    {
        if ($this->needsSorting) {
            $this->sortRegisteredServices();
        }
        $fields = null;
        foreach ($this->sortedFormViewProviders as $provider) {
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
        foreach ($this->sortedFormConfigurationModifiers as $modifier) {
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
            foreach ($this->sortedFormFieldResolvers as $resolver) {
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
     * @return array
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

    private function sortRegisteredServices()
    {
        krsort($this->formConfigurationModifiers);
        if ( ! empty($this->formConfigurationModifiers)) {
            $this->sortedFormConfigurationModifiers = call_user_func_array('array_merge', $this->formConfigurationModifiers);
        }
        krsort($this->formFieldResolvers);
        if ( ! empty($this->formFieldResolvers)) {
            $this->sortedFormFieldResolvers = call_user_func_array('array_merge', $this->formFieldResolvers);
        }
        krsort($this->formViewProviders);
        if ( ! empty($this->formViewProviders)) {
            $this->sortedFormViewProviders = call_user_func_array('array_merge', $this->formViewProviders);
        }
    }
}
