<?php

namespace Codete\FormGeneratorBundle\Tests\FormViewProvider;

use Codete\FormGeneratorBundle\FormViewProvider;
use Codete\FormGeneratorBundle\Tests\Model\Person;

class PersonAddFormView implements FormViewProvider
{
    public function getFields($model, $context) 
    {
        return array('surname');
    }

    public function supports($form, $model, $context) 
    {
        return $form === 'add' && $model instanceof Person;
    }
}
