<?php

namespace Codete\FormGeneratorBundle;

use Codete\FormGeneratorBundle\Annotations\AdditionalFields;
use Codete\FormGeneratorBundle\Annotations\Display;
use Codete\FormGeneratorBundle\Annotations\Form;
use Codete\FormGeneratorBundle\Form\Type\EmbedType;
use Codete\FormGeneratorBundle\FormField\AdditionalField;
use Codete\FormGeneratorBundle\FormField\FormFieldInterface;
use Codete\FormGeneratorBundle\FormField\PropertyBasedField;
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
        $additionalFields = $this->getAdditionalFields($model);
        if (empty($fields)) {
            $properties = array_map(function(\ReflectionProperty $property) {
                return new PropertyBasedField($property, $this->annotationReader);
            }, $ro->getProperties());
            foreach ($additionalFields as $field => $fieldConfiguration) {
                $properties[] = new AdditionalField($field, $fieldConfiguration);
            }
        } else {
            foreach (array_keys($fields) as $field) {
                $properties[] = $ro->hasProperty($field)
                    ? new PropertyBasedField($ro->getProperty($field), $this->annotationReader)
                    : new AdditionalField($field, $additionalFields[$field]);
            }
        }
        foreach ($properties as $property) {
            $propertyIsListed = array_key_exists($property->getName(), $fields);
            if (!empty($fields) && !$propertyIsListed) {
                continue;
            }
            $fieldConfiguration = $property->getConfiguration();
            if ($fieldConfiguration === null && !$propertyIsListed) {
                continue;
            }
            $configuration[$property->getName()] = (array) $fieldConfiguration;
            if (isset($fields[$property->getName()])) {
                $configuration[$property->getName()] = array_replace_recursive($configuration[$property->getName()], $fields[$property->getName()]);
            }
            if ($configuration[$property->getName()]['type'] === EmbedType::TYPE) {
                if (($value = $property->getValue($model)) === null) {
                    $value = $this->instantiator->instantiate($configuration[$property->getName()]['class']);
                }
                $configuration[$property->getName()]['data_class'] = $configuration[$property->getName()]['class'];
                $configuration[$property->getName()]['model'] = $value;
            }
            // this variable comes from Doctrine\Common\Annotations\Annotation
            unset($configuration[$property->getName()]['value']);
        }
        return $configuration;
    }

    /**
     * Gets all additional properties defined for given model and its ancestors.
     *
     * @param object $model
     * @return array
     */
    private function getAdditionalFields($model)
    {
        $inReverseOrder = [];
        $ro = new \ReflectionObject($model);
        do {
            $annotation = $this->annotationReader->getClassAnnotation($ro, AdditionalFields::class);
            if ($annotation instanceof AdditionalFields) {
                $inReverseOrder[] = $annotation->getFields();
            }
        } while ($ro = $ro->getParentClass());

        switch (count($inReverseOrder)) {
            case 0:
                return [];
            case 1:
                return $inReverseOrder[0];
            default:
                return array_replace_recursive(...array_reverse($inReverseOrder));
        }
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
