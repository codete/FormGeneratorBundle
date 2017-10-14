<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class SimpleNotOverridingDefaultView extends Simple
{
    /**
     * @Form\Field(type="text")
     */
    public $author;
}
