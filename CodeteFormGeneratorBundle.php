<?php

namespace Codete\FormGeneratorBundle;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ViewProvidersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class CodeteFormGeneratorBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurationModifiersCompilerPass());
        $container->addCompilerPass(new FieldResolversCompilerPass());
        $container->addCompilerPass(new ViewProvidersCompilerPass());
    }
}
