<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DeprecatedDisplay
{
    public $id;

    /**
     * @Form\Display(type=TextType::class)
     */
    public $title;
}
