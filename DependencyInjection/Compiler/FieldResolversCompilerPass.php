<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FieldResolversCompilerPass extends AbstractCompilerPass
{
    const TAG = 'form_generator.field_resolver';

    /**
     * @inheritdoc
     */
    protected function getMethodToCall()
    {
        return 'addFormFieldResolver';
    }

    /**
     * @inheritdoc
     */
    protected function getTagName()
    {
        return self::TAG;
    }
}
