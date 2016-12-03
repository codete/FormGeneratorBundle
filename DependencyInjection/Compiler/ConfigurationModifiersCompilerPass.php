<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class ConfigurationModifiersCompilerPass extends AbstractCompilerPass
{
    /**
     * @inheritdoc
     */
    protected function getMethodToCall()
    {
        return 'addFormConfigurationModifier';
    }

    /**
     * @inheritdoc
     */
    protected function getTagName()
    {
        return 'form_generator.configuration_modifier';
    }
}
