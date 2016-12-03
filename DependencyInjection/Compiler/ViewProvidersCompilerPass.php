<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class ViewProvidersCompilerPass extends AbstractCompilerPass
{
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
        return 'form_generator.view_provider';
    }
}
