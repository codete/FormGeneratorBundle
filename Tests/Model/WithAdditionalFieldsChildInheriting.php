<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\AdditionalFields(
 *     reset = { "type" = "reset", "label" = "Reset" }
 * )
 */
class WithAdditionalFieldsChildInheriting extends WithAdditionalFields
{

}
