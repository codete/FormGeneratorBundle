<?php

namespace Codete\FormGeneratorBundle\Tests\DependencyInjection\Compiler;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FieldResolversCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $fg = new Definition();
        $modifier = new Definition();
        $modifier->addTag('form_generator.field_resolver');

        $container = new ContainerBuilder;
        $container->setDefinition('form_generator', $fg);
        $container->setDefinition('some.field.resolver', $modifier);

        $pass = new FieldResolversCompilerPass();

        $this->assertCount(0, $fg->getMethodCalls());
        $pass->process($container);
        $this->assertCount(1, $fg->getMethodCalls());
    }
}
 