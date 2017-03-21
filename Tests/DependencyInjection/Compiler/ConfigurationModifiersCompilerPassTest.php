<?php

namespace Codete\FormGeneratorBundle\Tests\DependencyInjection\Compiler;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ConfigurationModifiersCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcess()
    {
        $fg = new Definition();
        $modifier = new Definition();
        $modifier->addTag('form_generator.configuration_modifier');
        $importantModifier = new Definition();
        $importantModifier->addTag('form_generator.configuration_modifier', ['priority' => 255]);

        $container = new ContainerBuilder;
        $container->setDefinition('form_generator', $fg);
        $container->setDefinition('some.form.modifier', $modifier);
        $container->setDefinition('important.form_modifier', $importantModifier);

        $pass = new ConfigurationModifiersCompilerPass();

        $this->assertCount(0, $fg->getMethodCalls());
        $pass->process($container);
        $methodCalls = $fg->getMethodCalls();
        $this->assertCount(2, $methodCalls);
        // check if priorities are passed correctly
        $this->assertSame(0, $methodCalls[0][1][1]);
        $this->assertSame(255, $methodCalls[1][1][1]);
    }
}
 