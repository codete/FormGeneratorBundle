<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\AdditionalFields(
 *     submit = { "label" = "Crash Override" }
 * )
 */
class WithAdditionalFieldsChildOverriding extends WithAdditionalFields
{

}
