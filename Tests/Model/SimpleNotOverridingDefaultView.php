<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SimpleNotOverridingDefaultView extends Simple
{
    /**
     * @Form\Field(type=TextType::class)
     */
    public $author;
}
