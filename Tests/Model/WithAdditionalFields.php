<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\Form(
 *     changeLabelAndOrder = { "submit" = { "label" = "Changed" }, "field" }
 * )
 * @Form\AdditionalFields(
 *     submit = { "type" = "submit", "label" = "Go" }
 * )
 */
class WithAdditionalFields
{
    /**
     * @Form\Display(type="text")
     */
    public $field;
}
