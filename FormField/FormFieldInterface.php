<?php

namespace Codete\FormGeneratorBundle\FormField;

/**
 * Represents a field at a time of preparing a form's configuration.
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
interface FormFieldInterface
{
    /**
     * Gets field's configuration.
     *
     * @return array|null
     */
    public function getConfiguration();

    /**
     * Gets name of field.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets current value of the field.
     *
     * @param object $model
     * @return mixed
     */
    public function getValue($model);
}
