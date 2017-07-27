<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class DisplayOptions
{
    /**
     * @Form\Field(type="text", attr = { "class" = "foo" })
     */
    public $normal;

    /**
     * @Form\Field(type="text", options = { "attr" = { "class" = "foo" } })
     */
    public $options;

    /**
     * @Form\Field(type="text", options = { "attr" = { "class" = "foo" } }, attr = { "class" = "bar" })
     */
    public $optionsIgnoreInlinedFields;
}
