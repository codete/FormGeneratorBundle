<?php

namespace Codete\FormGeneratorBundle;

use Codete\FormGeneratorBundle\Annotations\Display;
use Codete\FormGeneratorBundle\Annotations\Field;
use Codete\FormGeneratorBundle\Annotations\Form;
use Codete\FormGeneratorBundle\Form\Type\EmbedType;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Instantiator\Instantiator;

/**
 * FormConfigurationFactory creates initial form configuration that is adjusted later.
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FormConfigurationFactory
{
    /**
     * @var AdjusterRegistry
     */
    private $adjusterRegistry;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var Instantiator
     */
    private $instantiator;

    /**
     * @param AdjusterRegistry $adjusterRegistry
     */
    public function __construct(AdjusterRegistry $adjusterRegistry)
    {
        $this->adjusterRegistry = $adjusterRegistry;
        $this->annotationReader = new AnnotationReader();
        $this->instantiator = new Instantiator();
    }

    /**
     * Generates initial form configuration.
     *
     * @param string $form
     * @param object $model
     * @param array $context
     * @return array
     */
    public function getConfiguration($form, $model, $context)
    {
        $fields = null;
        foreach ($this->adjusterRegistry->getFormViewProviders() as $provider) {
            if ($provider->supports($form, $model, $context)) {
                $fields = $provider->getFields($model, $context);
                break;
            }
        }
        if ($fields === null) {
            $fields = $this->getFields($model, $form);
        }
        $fields = $this->normalizeFields($fields);
        return $this->getFieldsConfiguration($model, $fields);
    }

    /**
     * Creates form configuration for $model for given $fields.
     *
     * @param object $model
     * @param array $fields
     * @return array
     */
    private function getFieldsConfiguration($model, $fields = [])
    {
        $configuration = $properties = [];
        $ro = new \ReflectionObject($model);
        if (empty($fields)) {
            $properties = $ro->getProperties();
        } else {
            foreach (array_keys($fields) as $field) {
                if (! $ro->hasProperty($field)) {
                    continue; // most prob a class-level field
                }
                $properties[] = $ro->getProperty($field);
            }
        }
        $fieldConfigurations = [];
        // first are coming properties
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyIsListed = array_key_exists($propertyName, $fields);
            if (!empty($fields) && !$propertyIsListed) {
                continue; // list of fields was specified and current one is not there
            }
            $fieldConfiguration = $this->annotationReader->getPropertyAnnotation($property, Display::class);
            if ($fieldConfiguration === null && !$propertyIsListed) {
                continue;
            }
            $fieldConfigurations[$propertyName] = $fieldConfiguration;
        }
        // later are coming class-level fields. We need to iterate through all annotations as there's no method
        // to get *all* occurrences of chosen annotation.
        foreach ($this->annotationReader->getClassAnnotations($ro) as $annotation) {
            if (! $annotation instanceof Display) {
                continue;
            }
            $propertyName = $annotation->value;
            if (!empty($fields) && !array_key_exists($propertyName, $fields)) {
                continue; // list of fields was specified and current one is not there
            }
            // @todo this was a mistake originally, need to drop default required at all in 2.0
            unset($annotation->required);
            $fieldConfigurations[$propertyName] = $annotation;
        }
        foreach ($fieldConfigurations as $propertyName => $fieldConfiguration) {
            if ($fieldConfiguration instanceof Display && ! $fieldConfiguration instanceof Field) {
                @trigger_error("Display annotation has been deprecated in 1.3 and will be removed in 2.0 - please use Field instead.", E_USER_DEPRECATED);
            }
            $configuration[$propertyName] = (array)$fieldConfiguration;
            if (isset($fields[$propertyName])) {
                $configuration[$propertyName] = array_replace_recursive($configuration[$propertyName], $fields[$propertyName]);
            }
            if ($configuration[$propertyName]['type'] === EmbedType::TYPE) {
                if (! $ro->hasProperty($propertyName) || ($value = $ro->getProperty($propertyName)->getValue($model)) === null) {
                    $value = $this->instantiator->instantiate($configuration[$propertyName]['class']);
                }
                $configuration[$propertyName]['data_class'] = $configuration[$propertyName]['class'];
                $configuration[$propertyName]['model'] = $value;
            }
            // this variable comes from Doctrine\Common\Annotations\Annotation
            unset($configuration[$propertyName]['value']);
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
        $formAnnotation = $this->annotationReader->getClassAnnotation($ro, Form::class);
        if (($formAnnotation === null || !$formAnnotation->hasForm($form)) && $ro->getParentClass()) {
            while ($ro = $ro->getParentClass()) {
                $formAnnotation = $this->annotationReader->getClassAnnotation($ro, Form::class);
                if ($formAnnotation !== null && $formAnnotation->hasForm($form)) {
                    break;
                }
            }
        }
        if ($formAnnotation === null) {
            $formAnnotation = new Annotations\Form([]);
        }
        return $formAnnotation->getForm($form);
    }

    /**
     * Normalizes $fields array.
     *
     * @param array $_fields
     * @return array
     */
    private function normalizeFields($_fields)
    {
        $fields = [];
        foreach ($_fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $value;
            } else {
                $fields[$value] = [];
            }
        }
        return $fields;
    }
}
