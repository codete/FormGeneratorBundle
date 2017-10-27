<?php

namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\Form\Type\EmbedType;
use Codete\FormGeneratorBundle\Tests\FormConfigurationModifier\InactivePersonModifier;
use Codete\FormGeneratorBundle\Tests\FormConfigurationModifier\NoPhotoPersonModifier;
use Codete\FormGeneratorBundle\Tests\Model\Person;
use Codete\FormGeneratorBundle\Tests\Model\SimpleParent;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\PreloadedExtension;

class FormGeneratorTest extends BaseTest
{
    /**
     * @var PreloadedExtension
     */
    protected $embedTypeExtension;

    public function setUp()
    {
        parent::setUp();

        $embedType = new EmbedType($this->formGenerator);

        $this->embedTypeExtension = new PreloadedExtension([
            $embedType->getBlockPrefix() => $embedType,
        ], []);
    }

    /**
     * @dataProvider provideDefaultForm
     */
    public function testDefaultForm($model, $expectedFields, $additionalCheck = null)
    {
        $this->checkForm($model, $expectedFields, $additionalCheck, 'default');
    }
    
    public function provideDefaultForm()
    {
        return [
            [new Model\DeprecatedDisplay(), ['title']],
            [new Model\Simple(), ['title']],
            [new Model\SimpleNotOverridingDefaultView(), ['author', 'title']],
            [new Model\SimpleOverridingDefaultView(), ['title', 'author'], function($phpunit, $form) {
                $titleOptions = $form->get('title')->getConfig()->getOptions();
                $phpunit->assertEquals('foo', $titleOptions['attr']['class']);
                $authorConfig = $form->get('author')->getConfig();
                $phpunit->assertInstanceOf('Symfony\Component\Form\Extension\Core\Type\ChoiceType', $authorConfig->getType()->getInnerType());
                $authorOptions = $authorConfig->getOptions();
                $phpunit->assertSame(['foo' => 'foo', 'bar' => 'bar'], $authorOptions['choices']);
            }],
            [new Model\DisplayOptions(), ['normal', 'options', 'optionsIgnoreInlinedFields'], function($phpunit, $form) {
                $normal = $form->get('normal')->getConfig()->getOptions();
                $phpunit->assertEquals('foo', $normal['attr']['class']);
                $options = $form->get('options')->getConfig()->getOptions();
                $phpunit->assertEquals('foo', $options['attr']['class']);
                $optionsIgnoreInlinedFields = $form->get('optionsIgnoreInlinedFields')->getConfig()->getOptions();
                $phpunit->assertEquals('foo', $optionsIgnoreInlinedFields['attr']['class']);
            }],
            [new Model\ClassLevelFields(), ['title', 'reset', 'submit']],
        ];
    }
    
    public function testNamedForm()
    {
        $form = $this->formGenerator->createNamedFormBuilder('my_form', new Model\Simple());
        $this->assertSame('my_form', $form->getName());
    }

    public function testNamedFormWithOptionsMethodPut()
    {
        $form = $this->formGenerator->createNamedFormBuilder('my_form', new Model\Simple(), 'default', [],
            ['method' => 'PUT']);
        $this->assertSame('PUT', $form->getFormConfig()->getMethod());
    }

    public function testFormWithOptionsMethodPut()
    {
        $form = $this->formGenerator->createFormBuilder(new Model\Person(), 'work', [], ['method' => 'PUT']);
        $this->assertSame('PUT', $form->getFormConfig()->getMethod());
    }

    public function testFormViewDefinedInAnnotation()
    {
        $this->checkForm(new Model\SimpleOverridingDefaultView(), ['title'], null, 'only_title');
    }
    
    public function testFieldProvidedButNotAnnotated()
    {
        $this->checkForm(new Model\Person(), ['id', 'surname'], null, 'admin');
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown form 'foo'
     */
    public function testUnknownFormViewThrowsException()
    {
        $this->formGenerator->createFormBuilder(new Model\Simple(), 'foo');
    }
    
    public function testFormViewProvider()
    {
        $this->formGenerator->addFormViewProvider(new FormViewProvider\PersonAddFormView());
        $this->checkForm(new Model\Person(), ['surname'], null, 'add');
    }
    
    public function testFormViewProviderOrderMatters()
    {
        $this->formGenerator->addFormViewProvider(new FormViewProvider\PersonAddFormView());
        $notCalled = $this->getMockBuilder('Codete\FormGeneratorBundle\FormViewProviderInterface')
            ->getMock();
        $notCalled->expects($this->never())->method('supports');
        $this->formGenerator->addFormViewProvider($notCalled);
        $this->checkForm(new Model\Person(), ['surname'], null, 'add');
    }

    public function testFormViewProviderPriorityMatters()
    {
        $notCalled = $this->getMockBuilder('Codete\FormGeneratorBundle\FormViewProviderInterface')
            ->getMock();
        $notCalled->expects($this->never())->method('supports');
        $this->formGenerator->addFormViewProvider($notCalled);
        $this->formGenerator->addFormViewProvider(new FormViewProvider\PersonAddFormView(), 1);
        $this->checkForm(new Model\Person(), ['surname'], null, 'add');
    }

    /**
     * @depends testFormViewProviderPriorityMatters
     */
    public function testFormViewProviderPriorityAfterAnotherAdd()
    {
        $called = $this->getMockBuilder('Codete\FormGeneratorBundle\FormViewProviderInterface')
            ->getMock();
        $called->expects($this->once())->method('supports');
        $this->formGenerator->addFormViewProvider($called, 5);
        $this->formGenerator->createFormBuilder(new Model\Person(), 'work');
    }
    
    public function testFormConfigurationModifier()
    {
        $this->formGenerator->addFormConfigurationModifier(new FormConfigurationModifier\InactivePersonModifier());
        $model = new Model\Person();
        $model->active = false;
        $this->checkForm($model, ['title', 'name', 'surname', 'photo', 'active']);
    }
    
    public function testFormFieldResolver()
    {
        $this->formGenerator->addFormFieldResolver(new FormFieldResolver\PersonSalaryResolver());
        $this->checkForm(new Model\Person(), ['title', 'name', 'surname', 'photo', 'active', 'salary'], function($phpunit, $form) {
            $config = $form->get('salary')->getConfig();
            foreach ($config->getViewTransformers() as $t) {
                if ($t instanceof FormFieldResolver\DummyDataTransformer) {
                    return true;
                }
            }
            throw new \Exception('DummyDataTransformer has not been found');
        });
    }
    
    public function testFormFieldResolverOrderMatters()
    {
        $this->formGenerator->addFormFieldResolver(new FormFieldResolver\PersonSalaryResolver());
        $notCalled = $this->getMockBuilder('Codete\FormGeneratorBundle\FormFieldResolverInterface')
            ->getMock();
        $notCalled->expects($this->never())->method('supports');
        $this->formGenerator->addFormFieldResolver($notCalled);
        $this->formGenerator->createFormBuilder(new Model\Person(), 'work');
    }

    public function testFormFieldResolverPriorityMatters()
    {
        $notCalled = $this->getMockBuilder('Codete\FormGeneratorBundle\FormFieldResolverInterface')
            ->getMock();
        $notCalled->expects($this->never())->method('supports');
        $this->formGenerator->addFormFieldResolver($notCalled);
        $this->formGenerator->addFormFieldResolver(new FormFieldResolver\PersonSalaryResolver(), 1);
        $this->formGenerator->createFormBuilder(new Model\Person(), 'work');
    }

    /**
     * @depends testFormFieldResolverPriorityMatters
     */
    public function testFormFieldResolverPriorityAfterAnotherAdd()
    {
        $called = $this->getMockBuilder('Codete\FormGeneratorBundle\FormFieldResolverInterface')
            ->getMock();
        $called->expects($this->once())->method('supports');
        $this->formGenerator->addFormFieldResolver($called, 5);
        $this->formGenerator->createFormBuilder(new Model\Person(), 'work');
    }
    
    public function testFormAnnotationViewIsInherited()
    {
        $this->checkForm(new Model\Director(), ['title', 'name', 'surname', 'photo', 'active'], null, 'personal');
    }
    
    public function testFormAnnotationViewCanBeOverridden()
    {
        $this->checkForm(new Model\Director(), ['salary', 'department'], null, 'work');
    }
    
    public function testAllParentsAreCheckedForDefaultFormView()
    {
        $this->checkForm(new Model\InheritanceTest(), ['title', 'author']);
    }

    public function testFormViewCanAffectClassLevelFields()
    {
        $this->checkForm(new Model\ClassLevelFields(), ['submit', 'title'], function($phpunit, $form) {
            $normal = $form->get('submit')->getConfig()->getOptions();
            $phpunit->assertEquals('Click me', $normal['label']);
        }, 'tweaked');
    }
    
    protected function checkForm($model, $expectedFields, callable $additionalCheck = null, $form = 'default', $context = [])
    {
        $form = $this->formGenerator->createFormBuilder($model, $form, $context)->getForm();
        $this->assertEquals(count($expectedFields), count($form));
        $cnt = 0;
        foreach ($form as $field) {
            $this->assertEquals($field->getName(), $expectedFields[$cnt++]);
        }
        if ($additionalCheck !== null) {
            $additionalCheck($this, $form);
        }
    }

    public function testEmbedForms()
    {
        $sp = new SimpleParent();

        $sp->employee = new Person();
        $sp->named = new Person();

        $sp->employee->salary = 1390.86;
        $sp->named->active = false;

        $sp->person = new Person('Bar', 'Baz');

        $fb = $this->formFactoryBuilder
            ->addExtension($this->embedTypeExtension)
            ->getFormFactory()
            ->createBuilder(FormType::class, $sp);

        $this->formGenerator->addFormConfigurationModifier(new NoPhotoPersonModifier());
        $this->formGenerator->addFormConfigurationModifier(new InactivePersonModifier());

        $this->formGenerator->populateFormBuilder($fb, $sp);
        $form = $fb->getForm();
        $this->assertEquals(count(['person', 'noName', 'anonymous', 'employee']), count($form));
        $this->assertEquals(count(['title', 'name', 'surname', 'photo', 'active', 'salary']), $form->get('person')->count());
        $this->assertEquals(count(['title', 'name', 'surname', 'photo', 'active']), $form->get('named')->count());
        $this->assertEquals(count(['title', 'name', 'surname', 'active', 'salary']), $form->get('anonymous')->count());
        $this->assertEquals(count(['salary']), $form->get('employee')->count());
        $this->assertEquals(1390.86, $form->get('employee')->get('salary')->getData());
        $this->assertNull($form->get('anonymous')->get('name')->getData());
        $this->assertEquals('Foo', $form->get('named')->get('name')->getData());
        $this->assertEquals('Bar', $form->get('person')->get('name')->getData());
        $this->assertEquals('Baz', $form->get('person')->get('surname')->getData());
    }
}
