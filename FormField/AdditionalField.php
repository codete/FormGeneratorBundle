<?php

namespace Codete\FormGeneratorBundle\FormField;

/**
 * Describes a field that is not mapped to a property.
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class AdditionalField implements FormFieldInterface
{
    /**
     * @var
     */
    private $name;

    /**
     * @var array
     */
    private $configuration;

    public function __construct($name, array $configuration)
    {
        $this->name = $name;
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * By definition the field is not mapped to any property thus it can't have any value.
     */
    public function getValue($model)
    {
        return null;
    }
}
