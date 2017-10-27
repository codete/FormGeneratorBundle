<?php

namespace Codete\FormGeneratorBundle\Tests\DependencyInjection\Compiler;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass;
use Codete\FormGeneratorBundle\Tests\BaseTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FieldResolversCompilerPassTest extends BaseTest
{
    public function testProcess()
    {
        $fg = new Definition();
        $modifier = new Definition();
        $modifier->addTag('form_generator.field_resolver');
        $importantResolver = new Definition();
        $importantResolver->addTag('form_generator.field_resolver', ['priority' => 255]);

        $container = new ContainerBuilder;
        $container->setDefinition('form_generator', $fg);
        $container->setDefinition('some.field_resolver', $modifier);
        $container->setDefinition('important.field_resolver', $importantResolver);

        $pass = new FieldResolversCompilerPass();

        $this->assertCount(0, $fg->getMethodCalls());
        $pass->process($container);
        $methodCalls = $fg->getMethodCalls();
        $this->assertCount(2, $methodCalls);
        // check if priorities are passed correctly
        $this->assertSame(0, $methodCalls[0][1][1]);
        $this->assertSame(255, $methodCalls[1][1][1]);
    }
}
 