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
     * @Form\Display(type="choice", choices = { "mr" = "Mr.", "ms" = "Ms." })
     */
    public $title;
    
    /**
     * @Form\Display(type="text")
     */
    public $name;
    
    /**
     * @Form\Display(type="text")
     */
    public $surname;
    
    /**
     * @Form\Display(type="file")
     */
    public $photo;
    
    /**
     * @Form\Display(type="checkbox")
     */
    public $active;
    
    /**
     * @Form\Display(type="money")
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
