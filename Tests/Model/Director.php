<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Form\Form(
 *  work = { "salary", "department" },
 * )
 */
class Director extends Person
{
    /**
     * @Form\Field(type=TextType::class)
     */
    public $department;
}
