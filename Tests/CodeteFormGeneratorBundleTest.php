<?php

namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\CodeteFormGeneratorBundle;

class CodeteFormGeneratorBundleTest extends BaseTest
{
    public function testIfCompilerPassesAreLoaded()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $container->expects($this->exactly(3))
                ->method('addCompilerPass')
                ->withConsecutive(
                        [$this->isInstanceOf('Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass')],
                        [$this->isInstanceOf('Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass')],
                        [$this->isInstanceOf('Codete\FormGeneratorBundle\DependencyInjection\Compiler\ViewProvidersCompilerPass')]
                );
        $bundle = new CodeteFormGeneratorBundle();
        $bundle->build($container);
    }
}
