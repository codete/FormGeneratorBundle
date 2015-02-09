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
        return array(
            array(new Model\Simple(), array('title')),
            array(new Model\SimpleNotOverridingDefaultView(), array('author', 'title')),
            array(new Model\SimpleOverridingDefaultView(), array('title', 'author'), function($form) {
                $titleOptions = $form->get('title')->getConfig()->getOptions();
                $this->assertEquals('foo', $titleOptions['attr']['class']);
                $authorConfig = $form->get('author')->getConfig();
                $this->assertInstanceOf('Symfony\Component\Form\Extension\Core\Type\ChoiceType', $authorConfig->getType()->getInnerType());
                $authorOptions = $authorConfig->getOptions();
                $this->assertSame(array('foo' => 'foo', 'bar' => 'bar'), $authorOptions['choices']);
            }),
        );
    }
    
    public function testFormViewDefinedInAnnotation()
    {
        $this->checkForm(new Model\SimpleOverridingDefaultView(), array('title'), null, 'only_title');
    }
    
    public function testFieldProvidedButNotAnnotated()
    {
        $this->checkForm(new Model\Person(), array('id', 'surname'), null, 'admin');
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
        $this->checkForm(new Model\Person(), array('surname'), null, 'add');
    }
    
    public function testFormViewProviderOrderMatters()
    {
        $this->formGenerator->addFormViewProvider(new FormViewProvider\PersonAddFormView());
        $notCalled = $this->getMockBuilder('Codete\FormGeneratorBundle\FormViewProviderInterface')
            ->getMock();
        $notCalled->expects($this->never())->method('supports');
        $this->formGenerator->addFormViewProvider($notCalled);
        $this->checkForm(new Model\Person(), array('surname'), null, 'add');
    }
    
    public function testFormConfigurationModifier()
    {
        $this->formGenerator->addFormConfigurationModifier(new FormConfigurationModifier\InactivePersonModifier());
        $model = new Model\Person();
        $model->active = false;
        $this->checkForm($model, array('title', 'name', 'surname', 'photo', 'active'));
    }
    
    public function testFormFieldResolver()
    {
        $this->formGenerator->addFormFieldResolver(new FormFieldResolver\PersonSalaryResolver());
        $this->checkForm(new Model\Person(), array('title', 'name', 'surname', 'photo', 'active', 'salary'), function($form) {
            $config = $form->get('salary')->getConfig();
            foreach ($config->getViewTransformers() as $t) {
                if ($t instanceof FormFieldResolver\DummyDataTransformer) {
                    return true;
                }
            }
            throw new Exception('DummyDataTransformer has not been found');
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
        return array(
            array(
                array('foo', 'bar'),
                array('foo' => array(), 'bar' => array())
            ),
            array(
                array('foo' => array('bar' => 'baz')),
                array('foo' => array('bar' => 'baz'))
            ),
            array(
                array('foo', 'bar' => array()),
                array('foo' => array(), 'bar' => array())
            ),
        );
    }
    
    protected function checkForm($model, $expectedFields, $additionalCheck = null, $form = 'default', $context = array())
    {
        $form = $this->formGenerator->createFormBuilder($model, $form, $context)->getForm();
        $this->assertEquals(count($expectedFields), count($form));
        $cnt = 0;
        foreach ($form as $field) {
            $this->assertEquals($field->getName(), $expectedFields[$cnt++]);
        }
        if ($additionalCheck !== null) {
            $additionalCheck($form);
        }
    }
}
