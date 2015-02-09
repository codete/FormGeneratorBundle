<?php

namespace Codete\FormGeneratorBundle\Annotations;

/**
 * @Annotation
 * 
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class Display extends \Doctrine\Common\Annotations\Annotation
{
    public $required = false;
    
    /**
     * Unlike original Annotation we accept all variables.
     * If one of them is wrong FormBuilderInterface will let
     * us know about it.
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}