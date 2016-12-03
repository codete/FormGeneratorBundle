<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FieldResolversCompilerPass extends AbstractCompilerPass
{
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
        return 'form_generator.field_resolver';
    }
}
