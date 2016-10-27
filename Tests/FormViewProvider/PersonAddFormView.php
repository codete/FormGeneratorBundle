<?php

namespace Codete\FormGeneratorBundle\Tests\FormViewProvider;

use Codete\FormGeneratorBundle\FormViewProviderInterface;
use Codete\FormGeneratorBundle\Tests\Model\Person;

class PersonAddFormView implements FormViewProviderInterface
{
    public function getFields($model, $context) 
    {
        return ['surname'];
    }

    public function supports($form, $model, $context) 
    {
        return $form === 'add' && $model instanceof Person;
    }
}
