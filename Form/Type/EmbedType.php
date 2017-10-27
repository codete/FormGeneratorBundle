<?php

namespace Codete\FormGeneratorBundle\Form\Type;

use Codete\FormGeneratorBundle\FormGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmbedType extends AbstractType
{
    /**
     * @var FormGenerator $formGenerator
     */
    private $formGenerator;

    /**
     * @param FormGenerator $formGenerator
     */
    public function __construct(FormGenerator $formGenerator)
    {
        $this->formGenerator = $formGenerator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formGenerator->populateFormBuilder($builder, $options['model'], $options['view'], isset($options['context']) ? $options['context'] : []);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'view' => 'default',
            'class' => '',
            'context' => [],
            'model' => null
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'embed';
    }
}
