<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

class DisplayOptions
{
    /**
     * @Form\Display(type="text", attr = { "class" = "foo" })
     */
    public $normal;

    /**
     * @Form\Display(type="text", options = { "attr" = { "class" = "foo" } })
     */
    public $options;

    /**
     * @Form\Display(type="text", options = { "attr" = { "class" = "foo" } }, attr = { "class" = "bar" })
     */
    public $optionsIgnoreInlinedFields;
}
