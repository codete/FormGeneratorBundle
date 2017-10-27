<?php

namespace Codete\FormGeneratorBundle\Annotations;

use Codete\FormGeneratorBundle\Form\Type\EmbedType;

/**
 * @Annotation
 */
class Embed extends Field
{
    public $view = 'default';

    public $type = EmbedType::class;
}
