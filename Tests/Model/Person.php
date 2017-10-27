<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;

/**
 * @Form\Form(
 *  personal = { "title", "name", "surname", "photo", "active" },
 *  work = { "salary" },
 *  admin = { "id" = { "type" = "number" }, "surname" }
 * )
 */
class Person
{
    public $id;
    
    /**
     * @Form\Field(type="choice", choices = { "Mr." = "mr", "Ms." = "ms" })
     */
    public $title;
    
    /**
     * @Form\Field(type="text")
     */
    public $name;
    
    /**
     * @Form\Field(type="text")
     */
    public $surname;
    
    /**
     * @Form\Field(type="file")
     */
    public $photo;
    
    /**
     * @Form\Field(type="checkbox")
     */
    public $active;
    
    /**
     * @Form\Field(type="money")
     */
    public $salary;

    /**
     * Person constructor.
     * @param $name
     * @param $surname
     */
    public function __construct($name = 'Foo', $surname = null)
    {
        $this->name = $name;
        $this->surname = $surname;
    }


}
