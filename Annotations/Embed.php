<?php

namespace Codete\FormGeneratorBundle\Annotations;

/**
 * @Annotation
 */
class Embed extends Display
{
    public $view = 'default';

    public $type = 'embed';
}
