<?php

namespace Codete\FormGeneratorBundle\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class AdditionalFields extends \Doctrine\Common\Annotations\Annotation
{
    private $fields = [];

    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
