<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class ConfigurationModifiersCompilerPass extends AbstractCompilerPass
{
    const TAG = 'form_generator.configuration_modifier';

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
        return self::TAG;
    }
}
