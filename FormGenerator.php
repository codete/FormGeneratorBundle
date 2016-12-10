<?php

namespace Codete\FormGeneratorBundle;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * FormGenerator creates populated FormBuilder for any given class. Say goodbye to writing boring FormType classes!
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class FormGenerator
{
    /** @var AdjusterRegistry */
    private $adjusterRegistry;

    /** @var FormConfigurationFactory */
    private $formConfigurationFactory;

    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->adjusterRegistry = new AdjusterRegistry();
        $this->formConfigurationFactory = new FormConfigurationFactory($this->adjusterRegistry);
        $this->formFactory = $formFactory;
    }

    /**
     * Adds modifier for form's configuration.
     *
     * @param FormConfigurationModifierInterface $modifier
     * @param int $priority
     */
    public function addFormConfigurationModifier(FormConfigurationModifierInterface $modifier, $priority = 0)
    {
        $this->adjusterRegistry->addFormConfigurationModifier($modifier, $priority);
    }

    /**
     * Adds resolver for form's fields.
     *
     * @param FormFieldResolverInterface $resolver
     * @param int $priority
     */
    public function addFormFieldResolver(FormFieldResolverInterface $resolver, $priority = 0)
    {
        $this->adjusterRegistry->addFormFieldResolver($resolver, $priority);
    }

    /**
     * Adds provider for defining default fields for form.
     *
     * @param FormViewProviderInterface $provider
     * @param int $priority
     */
    public function addFormViewProvider(FormViewProviderInterface $provider, $priority = 0)
    {
        $this->adjusterRegistry->addFormViewProvider($provider, $priority);
    }

    /**
     * Creates FormBuilder and populates it.
     *
     * @param object $model data object
     * @param string $form view to generate
     * @param array $context
     * @return FormBuilderInterface
     */
    public function createFormBuilder($model, $form = 'default', $context = [])
    {
        $fb = $this->formFactory->createBuilder(FieldTypeMapper::map('form'), $model);

        $this->populateFormBuilder($fb, $model, $form, $context);
        return $fb;
    }

    /**
     * Creates named FormBuilder and populates it.
     *
     * @param string $name
     * @param object $model data object
     * @param string $form view to generate
     * @param array $context
     * @return FormBuilderInterface
     */
    public function createNamedFormBuilder($name, $model, $form = 'default', $context = [])
    {
        $fb = $this->formFactory->createNamedBuilder($name, FieldTypeMapper::map('form'), $model);

        $this->populateFormBuilder($fb, $model, $form, $context);
        return $fb;
    }

    /**
     * Populates FormBuilder.
     *
     * @param FormBuilderInterface $fb
     * @param object $model
     * @param string $form view to generate
     * @param array $context
     */
    public function populateFormBuilder(FormBuilderInterface $fb, $model, $form = 'default', $context = [])
    {
        $configuration = $this->formConfigurationFactory->getConfiguration($form, $model, $context);
        foreach ($this->adjusterRegistry->getFormConfigurationModifiers() as $modifier) {
            if ($modifier->supports($model, $configuration, $context)) {
                $configuration = $modifier->modify($model, $configuration, $context);
            }
        }
        foreach ($configuration as $field => $options) {
            $type = null;
            if (isset($options['type'])) {
                $type = FieldTypeMapper::map($options['type']);
                unset($options['type']);
            }
            foreach ($this->adjusterRegistry->getFormFieldResolvers() as $resolver) {
                if ($resolver->supports($model, $field, $type, $options, $context)) {
                    $fb->add($resolver->getFormField($fb, $field, $type, $options, $context));
                    continue 2;
                }
            }
            $fb->add($field, $type, $options);
        }
    }
}
