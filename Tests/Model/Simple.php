<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Simple
{
    public $id;
    
    /**
     * @Form\Field(type=TextType::class)
     */
    public $title;
}
