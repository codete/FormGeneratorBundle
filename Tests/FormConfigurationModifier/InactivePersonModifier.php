<?php

namespace Codete\FormGeneratorBundle\Tests\FormConfigurationModifier;

use Codete\FormGeneratorBundle\FormConfigurationModifier;
use Codete\FormGeneratorBundle\Tests\Model\Person;

class InactivePersonModifier implements FormConfigurationModifier
{
    public function modify($model, $configuration, $context) 
    {
        unset($configuration['salary']);
        return $configuration;
    }

    public function supports($model, $configuration, $context) 
    {
        return $model instanceof Person && $model->active === false;
    }
}
