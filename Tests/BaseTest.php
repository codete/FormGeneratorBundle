<?php

namespace Codete\FormGeneratorBundle\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Codete\FormGeneratorBundle\FormGenerator;
use Symfony\Component\Form\Forms;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Codete\FormGeneratorBundle\FormGenerator */
    protected $formGenerator;
    
    public function setUp()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/../Annotations/Annotations.php');
        AnnotationRegistry::registerAutoloadNamespace('Symfony\Component\Validator\Constraints', __DIR__ . '/../vendor/symfony/symfony/src');
        $this->formGenerator = new FormGenerator(
            Forms::createFormFactoryBuilder()->getFormFactory()
        );
    }
}
