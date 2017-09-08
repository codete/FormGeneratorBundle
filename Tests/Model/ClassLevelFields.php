<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\Form(
 *   tweaked = { "submit" = { "label" = "Click me" }, "title" }
 * )
 * @Form\Field("reset", type="reset")
 * @Form\Field("submit", type="submit")
 */
class ClassLevelFields
{
    /**
     * @Form\Field(type="text")
     */
    public $title;
}
