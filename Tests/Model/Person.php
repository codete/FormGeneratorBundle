<?php

namespace Codete\FormGeneratorBundle\Tests\Model;

use Codete\FormGeneratorBundle\Annotations as Form;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Form\Form(
 *  personal = { "title", "name", "surname", "photo", "active" },
 *  work = { "salary" },
 *  admin = { "id" = { "type" = NumberType::class }, "surname" }
 * )
 */
class Person
{
    public $id;
    
    /**
     * @Form\Field(type=ChoiceType::class, choices = { "Mr." = "mr", "Ms." = "ms" })
     */
    public $title;
    
    /**
     * @Form\Field(type=TextType::class)
     */
    public $name;
    
    /**
     * @Form\Field(type=TextType::class)
     */
    public $surname;
    
    /**
     * @Form\Field(type=FileType::class)
     */
    public $photo;
    
    /**
     * @Form\Field(type=CheckboxType::class)
     */
    public $active;
    
    /**
     * @Form\Field(type=MoneyType::class)
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
