<?php

namespace Codete\FormGeneratorBundle;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
interface FormConfigurationModifierInterface
{
    /**
     * Check if this FormConfigurationModifier can alter form's configuration
     * 
     * @param object $model
     * @param array $configuration
     * @param array $context
     * @return bool
     */
    public function supports($model, $configuration, $context);
    
    /**
     * Modifies form's configuration 
     * 
     * @param object $model
     * @param array $configuration
     * @param array $context
     * @return array
     */
    public function modify($model, $configuration, $context);
}
