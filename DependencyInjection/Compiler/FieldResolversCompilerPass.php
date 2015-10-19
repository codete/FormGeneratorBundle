<?php

namespace Codete\FormGeneratorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FieldResolversCompilerPass implements CompilerPassInterface {

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container) 
    {
        $tags = $container->findTaggedServiceIds('form_generator.field_resolver');
        if (count($tags) > 0 && $container->hasDefinition('form_generator')) {
            $formGenerator = $container->getDefinition('form_generator');
            foreach ($tags as $id => $tags) {
                foreach ($tags as $attributes) {
                    if ( ! isset($attributes['priority'])) {
                        $attributes['priority'] = 0;
                    }
                    $formGenerator->addMethodCall(
                        'addFormFieldResolver',
                        array(new Reference($id), (int) $attributes['priority'])
                    );
                }
            }
        }
    }
}
