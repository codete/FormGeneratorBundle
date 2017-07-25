<?php

namespace Codete\FormGeneratorBundle\FormField;

use Codete\FormGeneratorBundle\Annotations\Display;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Describes a field that is mapped to a property.
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class PropertyBasedField implements FormFieldInterface
{
    /**
     * @var \ReflectionProperty
     */
    private $property;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(\ReflectionProperty $property, AnnotationReader $annotationReader)
    {
        $this->property = $property;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration()
    {
        $configuration = $this->annotationReader->getPropertyAnnotation($this->property, Display::class);
        return $configuration ? (array) $configuration : null;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->property->getName();
    }

    /**
     * @inheritdoc
     */
    public function getValue($model)
    {
        return $this->property->getValue($model);
    }
}
