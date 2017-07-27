<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\Form(
 *  work = { "salary", "department" },
 * )
 */
class Director extends Person
{
    /**
     * @Form\Field(type="text")
     */
    public $department;
}
