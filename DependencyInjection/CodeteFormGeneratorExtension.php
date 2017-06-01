<?php

namespace Codete\FormGeneratorBundle\DependencyInjection;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ViewProvidersCompilerPass;
use Codete\FormGeneratorBundle\FormConfigurationModifierInterface;
use Codete\FormGeneratorBundle\FormFieldResolverInterface;
use Codete\FormGeneratorBundle\FormViewProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class CodeteFormGeneratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form_generator.xml');

        if (method_exists($container, 'registerForAutoconfiguration')) {
            $container->registerForAutoconfiguration(FormConfigurationModifierInterface::class)
                ->addTag(ConfigurationModifiersCompilerPass::TAG);
            $container->registerForAutoconfiguration(FormViewProviderInterface::class)
                ->addTag(ViewProvidersCompilerPass::TAG);
            $container->registerForAutoconfiguration(FormFieldResolverInterface::class)
                ->addTag(FieldResolversCompilerPass::TAG);
        }
    }
}
