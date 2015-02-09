<?php

namespace Codete\FormGeneratorBundle;

/**
 * Objects implementing this interface can define what fields
 * will be generated in Forms for cases they support. First
 * registered Provider that supports case wins, support of
 * other Providers is not tested.
 * 
 * @author Maciej Malarz <malarzm@gmail.com>
 */
interface FormViewProviderInterface
{
    /**
     * Check if this FormViewProvider can provide form view
     * 
     * @param string $form
     * @param object $model
     * @param array $context
     * @return bool
     */
    public function supports($form, $model, $context);
    
    /**
     * Gets list of fields (with configuration eventually) that
     * should be included in Form. Configuration defined here
     * will override field's configuration for same attributes,
     * rest will be inherited (effectively array_replace_recursive
     * will be called)
     * 
     * @param object $model
     * @param array $context
     * @return array array('foo', 'bar' => array(...), ...)
     */
    public function getFields($model, $context);
}
