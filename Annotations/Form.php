<?php

namespace Codete\FormGeneratorBundle\Annotations;

/** 
 * @Annotation 
 * @Target("CLASS")
 * 
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class Form extends \Doctrine\Common\Annotations\Annotation
{
    private $forms = [];
    
    public function __set($name, $value) 
    {
        $this->forms[$name] = $value;
    }
    
    public function getForm($form)
    {
        if (!isset($this->forms[$form])) {
            if ($form === 'default') {
                return [];
            }
            throw new \InvalidArgumentException("Unknown form '$form'");
        }
        return $this->forms[$form];
    }
    
    public function hasForm($form)
    {
        return isset($this->forms[$form]);
    }
}
