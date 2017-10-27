<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Form\Form(
 *   default = {
 *     "title" = { "attr" = { "class" = "foo" } },
 *     "author" = { "type" = ChoiceType::class, "choices" = { "foo" = "foo", "bar" = "bar" } }
 *   },
 *   only_title = {
 *     "title"
 *   }
 * )
 */
class SimpleOverridingDefaultView extends Simple
{
    /**
     * @Form\Field(type=TextType::class)
     */
    public $author;
}
