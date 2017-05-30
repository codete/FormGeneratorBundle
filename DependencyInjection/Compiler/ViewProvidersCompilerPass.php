<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class ViewProvidersCompilerPass extends AbstractCompilerPass
{
    const TAG = 'form_generator.view_provider';

    /**
     * @inheritdoc
     */
    protected function getMethodToCall()
    {
        return 'addFormViewProvider';
    }

    /**
     * @inheritdoc
     */
    protected function getTagName()
    {
        return self::TAG;
    }
}
