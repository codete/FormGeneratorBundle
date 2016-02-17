<?php

namespace Codete\FormGeneratorBundle\Tests\FormConfigurationModifier;

use Codete\FormGeneratorBundle\FormConfigurationModifierInterface;
use Codete\FormGeneratorBundle\Tests\Model\Person;

class NoPhotoPersonModifier implements FormConfigurationModifierInterface
{
    public function modify($model, $configuration, $context) 
    {
        unset($configuration['photo']);
        return $configuration;
    }

    public function supports($model, $configuration, $context) 
    {
        return $model instanceof Person && isset($context['no_photo']) && $context['no_photo'] === true;
    }
}
