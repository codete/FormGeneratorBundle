<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DisplayOptions
{
    /**
     * @Form\Field(type=TextType::class, attr = { "class" = "foo" })
     */
    public $normal;

    /**
     * @Form\Field(type=TextType::class, options = { "attr" = { "class" = "foo" } })
     */
    public $options;

    /**
     * @Form\Field(type=TextType::class, options = { "attr" = { "class" = "foo" } }, attr = { "class" = "bar" })
     */
    public $optionsIgnoreInlinedFields;
}
