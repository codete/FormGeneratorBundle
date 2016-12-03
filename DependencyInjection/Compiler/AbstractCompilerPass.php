<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
abstract class AbstractCompilerPass implements CompilerPassInterface
{
    /**
     * Gets name of method that should be called.
     *
     * @return string
     */
    abstract protected function getMethodToCall();

    /**
     * Gets tag name.
     *
     * @return string
     */
    abstract protected function getTagName();

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('form_generator')) {
            return;
        }
        $formGenerator = $container->getDefinition('form_generator');
        foreach ($container->findTaggedServiceIds($this->getTagName()) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (! isset($attributes['priority'])) {
                    $attributes['priority'] = 0;
                }
                $formGenerator->addMethodCall(
                    $this->getMethodToCall(),
                    [new Reference($id), (int) $attributes['priority']]
                );
            }
        }
    }
}
