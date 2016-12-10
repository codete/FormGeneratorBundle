<?php

namespace Codete\FormGeneratorBundle;

/**
 * AdjusterRegistry stores and manages order of ConfigurationModifiers, FieldResolvers and ViewProviders.
 *
 * @author Maciej Malarz <malarzm@gmail.com>
 */
class AdjusterRegistry
{
    /** @var FormConfigurationModifierInterface[][] */
    private $formConfigurationModifiers = [];

    /** @var FormFieldResolverInterface[][] */
    private $formFieldResolvers = [];

    /** @var FormViewProviderInterface[][] */
    private $formViewProviders = [];

    /** @var FormConfigurationModifierInterface[] */
    private $sortedFormConfigurationModifiers = [];

    /** @var FormFieldResolverInterface[] */
    private $sortedFormFieldResolvers = [];

    /** @var FormViewProviderInterface[] */
    private $sortedFormViewProviders = [];

    /** @var bool */
    private $needsSorting = false;

    /**
     * Adds modifier for form's configuration
     *
     * @param FormConfigurationModifierInterface $modifier
     * @param int $priority
     */
    public function addFormConfigurationModifier(FormConfigurationModifierInterface $modifier, $priority = 0)
    {
        $this->formConfigurationModifiers[$priority][] = $modifier;
        $this->needsSorting = true;
    }

    /**
     * Adds resolver for form's fields
     *
     * @param FormFieldResolverInterface $resolver
     * @param int $priority
     */
    public function addFormFieldResolver(FormFieldResolverInterface $resolver, $priority = 0)
    {
        $this->formFieldResolvers[$priority][] = $resolver;
        $this->needsSorting = true;
    }

    /**
     * Adds provider for defining default fields for form
     *
     * @param FormViewProviderInterface $provider
     * @param int $priority
     */
    public function addFormViewProvider(FormViewProviderInterface $provider, $priority = 0)
    {
        $this->formViewProviders[$priority][] = $provider;
        $this->needsSorting = true;
    }

    /**
     * Gets FormConfigurationModifiers sorted by priority.
     *
     * @return FormConfigurationModifierInterface[]
     */
    public function getFormConfigurationModifiers()
    {
        if ($this->needsSorting) {
            $this->sortRegisteredServices();
        }
        return $this->sortedFormConfigurationModifiers;
    }

    /**
     * Gets FormFieldResolvers sorted by priority.
     *
     * @return FormFieldResolverInterface[]
     */
    public function getFormFieldResolvers()
    {
        if ($this->needsSorting) {
            $this->sortRegisteredServices();
        }
        return $this->sortedFormFieldResolvers;
    }

    /**
     * Gets FormViewProviders sorted by priority.
     *
     * @return FormViewProviderInterface[]
     */
    public function getFormViewProviders()
    {
        if ($this->needsSorting) {
            $this->sortRegisteredServices();
        }
        return $this->sortedFormViewProviders;
    }

    /**
     * Sorts all registered adjusters by priority.
     */
    private function sortRegisteredServices()
    {
        krsort($this->formConfigurationModifiers);
        if ( ! empty($this->formConfigurationModifiers)) {
            $this->sortedFormConfigurationModifiers = call_user_func_array('array_merge', $this->formConfigurationModifiers);
        }
        krsort($this->formFieldResolvers);
        if ( ! empty($this->formFieldResolvers)) {
            $this->sortedFormFieldResolvers = call_user_func_array('array_merge', $this->formFieldResolvers);
        }
        krsort($this->formViewProviders);
        if ( ! empty($this->formViewProviders)) {
            $this->sortedFormViewProviders = call_user_func_array('array_merge', $this->formViewProviders);
        }
    }
}
