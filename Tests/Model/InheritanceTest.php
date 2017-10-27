<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MiddleClass extends SimpleOverridingDefaultView {  }

/**
 * @Form\Form(
 *  full = { "title", "author", "another" }
 * )
 */
class InheritanceTest extends MiddleClass
{
    /**
     * @Form\Field(type=TextType::class)
     */
    public $another;
}
