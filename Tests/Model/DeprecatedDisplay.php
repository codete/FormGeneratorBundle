<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class DeprecatedDisplay
{
    public $id;

    /**
     * @Form\Display(type="text")
     */
    public $title;
}
