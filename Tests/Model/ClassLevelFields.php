<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Form\Form(
 *   tweaked = { "submit" = { "label" = "Click me" }, "title" }
 * )
 * @Form\Field("reset", type=ResetType::class)
 * @Form\Field("submit", type=SubmitType::class)
 */
class ClassLevelFields
{
    /**
     * @Form\Field(type=TextType::class)
     */
    public $title;
}
