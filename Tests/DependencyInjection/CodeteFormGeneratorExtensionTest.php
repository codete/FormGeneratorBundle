<?php

namespace DependencyInjection;

use Codete\FormGeneratorBundle\DependencyInjection\CodeteFormGeneratorExtension;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass;
use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ViewProvidersCompilerPass;
use Codete\FormGeneratorBundle\Form\Type\EmbedType;
use Codete\FormGeneratorBundle\FormConfigurationModifierInterface;
use Codete\FormGeneratorBundle\FormFieldResolverInterface;
use Codete\FormGeneratorBundle\FormGenerator;
use Codete\FormGeneratorBundle\FormViewProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CodeteFormGeneratorExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testCoreServicesAreLoaded()
    {
        $container = new ContainerBuilder();
        $extension = new CodeteFormGeneratorExtension();
        $extension->load([], $container);

        $this->assertTrue($container->has('form_generator'));
        $this->assertTrue($container->has('form_generator.type.embed'));
        $embedType = $container->getDefinition('form_generator.type.embed');
        $this->assertTrue($embedType->hasTag('form.type'));

        $this->assertTrue($container->has(FormGenerator::class));
        $this->assertTrue($container->has(EmbedType::class));
    }

    public function testAutoconfigure()
    {
        if (! method_exists(ContainerBuilder::class, 'registerForAutoconfiguration')) {
            $this->markTestSkipped('This test requires Symfony 3.3 or above');
        }

        $container = new ContainerBuilder();
        $extension = new CodeteFormGeneratorExtension();
        $extension->load([], $container);
        $autoconfigure = $container->getAutoconfiguredInstanceof();

        $this->assertArrayHasKey(FormConfigurationModifierInterface::class, $autoconfigure);
        $this->assertTrue($autoconfigure[FormConfigurationModifierInterface::class]->hasTag(ConfigurationModifiersCompilerPass::TAG));

        $this->assertArrayHasKey(FormViewProviderInterface::class, $autoconfigure);
        $this->assertTrue($autoconfigure[FormViewProviderInterface::class]->hasTag(ViewProvidersCompilerPass::TAG));

        $this->assertArrayHasKey(FormFieldResolverInterface::class, $autoconfigure);
        $this->assertTrue($autoconfigure[FormFieldResolverInterface::class]->hasTag(FieldResolversCompilerPass::TAG));
    }
}
