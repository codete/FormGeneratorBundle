<?php

namespace Codete\FormGeneratorBundle\Tests\DependencyInjection\Compiler;

use Codete\FormGeneratorBundle\DependencyInjection\Compiler\ViewProvidersCompilerPass;
use Codete\FormGeneratorBundle\Tests\BaseTest;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ViewProvidersCompilerPassTest extends BaseTest
{
    public function testProcess()
    {
        $fg = new Definition();
        $provider = new Definition();
        $provider->addTag('form_generator.view_provider');
        $importantProvider = new Definition();
        $importantProvider->addTag('form_generator.view_provider', ['priority' => 255]);

        $container = new ContainerBuilder;
        $container->setDefinition('form_generator', $fg);
        $container->setDefinition('some.form_provider', $provider);
        $container->setDefinition('important.form_provider', $importantProvider);

        $pass = new ViewProvidersCompilerPass();

        $this->assertCount(0, $fg->getMethodCalls());
        $pass->process($container);
        $methodCalls = $fg->getMethodCalls();
        $this->assertCount(2, $methodCalls);
        // check if priorities are passed correctly
        $this->assertSame(0, $methodCalls[0][1][1]);
        $this->assertSame(255, $methodCalls[1][1][1]);
    }
}
 