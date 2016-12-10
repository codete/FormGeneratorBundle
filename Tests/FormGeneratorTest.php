<?php

namespace Codete\FormGeneratorBundle\Tests;

class FormGeneratorTest extends BaseTest
{
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
        ];
    }
    
    public function testNamedForm()
    {
        $form = $this->formGenerator->createNamedFormBuilder('my_form', new Model\Simple());
        $this->assertSame('my_form', $form->getName());
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
    
    public function testFormAnnotationViewCanBeOverriden()
    {
        $this->checkForm(new Model\Director(), ['salary', 'department'], null, 'work');
    }
    
    public function testAllParentsAreCheckedForDefaultFormView()
    {
        $this->checkForm(new Model\InheritanceTest(), ['title', 'author']);
    }
    
    /**
     * @dataProvider provideFieldsNormalization
     */
    public function testFieldsNormalization($toNormalize, $expected)
    {
        $r = new \ReflectionObject($this->formGenerator);
        $m = $r->getMethod('normalizeFields');
        $m->setAccessible(true);
        $this->assertSame($expected, $m->invoke($this->formGenerator, $toNormalize));
    }
    
    public function provideFieldsNormalization()
    {
        return [
            [
                ['foo', 'bar'],
                ['foo' => [], 'bar' => []],
            ],
            [
                ['foo' => ['bar' => 'baz']],
                ['foo' => ['bar' => 'baz']],
            ],
            [
                ['foo', 'bar' => []],
                ['foo' => [], 'bar' => []],
            ],
        ];
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
}
