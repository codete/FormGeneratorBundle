<?php

namespace Codete\FormGeneratorBundle\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Codete\FormGeneratorBundle\FormGenerator;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\Forms;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Codete\FormGeneratorBundle\FormGenerator */
    protected $formGenerator;

    /**
     * @var FormFactoryBuilderInterface
     */
    protected $formFactoryBuilder;

    public function setUp()
    {
        $loader = require __DIR__.'/../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        $this->formFactoryBuilder = Forms::createFormFactoryBuilder();

        $this->formGenerator = new FormGenerator(
            $this->formFactoryBuilder->getFormFactory()
        );
    }
}
