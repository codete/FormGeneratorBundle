<?php

namespace Codete\FormGeneratorBundle\Tests\DependencyInjection\Compiler;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigurationModifiersCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $fg = new Definition();
        $modifier = new Definition();
        $modifier->addTag('form_generator.configuration_modifier');

        $container = new ContainerBuilder;
        $container->setDefinition('form_generator', $fg);
        $container->setDefinition('some.form.modifier', $modifier);

        $pass = new ConfigurationModifiersCompilerPass();

        $this->assertCount(0, $fg->getMethodCalls());
        $pass->process($container);
        $this->assertCount(1, $fg->getMethodCalls());
    }
}
 