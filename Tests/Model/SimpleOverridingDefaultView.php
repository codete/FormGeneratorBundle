<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\Form(
 *   default = {
 *     "title" = { "attr" = { "class" = "foo" } },
 *     "author" = { "type" = "choice", "choices" = { "foo" = "foo", "bar" = "bar" }, "choices_as_values" = true }
 *   },
 *   only_title = {
 *     "title"
 *   }
 * )
 */
class SimpleOverridingDefaultView extends Simple
{
    /**
     * @Form\Field(type="text")
     */
    public $author;
}
