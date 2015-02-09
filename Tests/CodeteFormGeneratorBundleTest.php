<?php

namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\CodeteFormGeneratorBundle;

class CodeteFormGeneratorBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testIfCompilerPassesAreLoaded()
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
                ->disableOriginalConstructor()
                ->getMock();
        $container->expects($this->exactly(3))
                ->method('addCompilerPass')
                ->withConsecutive(
                        array($this->isInstanceOf('Codete\FormGeneratorBundle\DependencyInjection\Compiler\ConfigurationModifiersCompilerPass')),
                        array($this->isInstanceOf('Codete\FormGeneratorBundle\DependencyInjection\Compiler\FieldResolversCompilerPass')),
                        array($this->isInstanceOf('Codete\FormGeneratorBundle\DependencyInjection\Compiler\ViewProvidersCompilerPass'))
                );
        $bundle = new CodeteFormGeneratorBundle();
        $bundle->build($container);
    }
}
