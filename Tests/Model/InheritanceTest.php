<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class MiddleClass extends SimpleOverridingDefaultView {  }

/**
 * @Form\Form(
 *  full = { "title", "author", "another" }
 * )
 */
class InheritanceTest extends MiddleClass
{
    /**
     * @Form\Field(type="text")
     */
    public $another;
}
