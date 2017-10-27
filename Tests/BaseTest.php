<?php

namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\FormGenerator;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\Forms;

abstract class BaseTest extends TestCase
{
    /**
     * @var \Codete\FormGeneratorBundle\FormGenerator
     */
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
