<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class Simple
{
    public $id;
    
    /**
     * @Form\Display(type="text")
     */
    public $title;
}
