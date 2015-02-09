<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class SimpleNotOverridingDefaultView extends Simple
{
    /**
     * @Form\Display(type="text")
     */
    public $author;
}
