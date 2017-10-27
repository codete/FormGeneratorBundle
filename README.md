FormGeneratorBundle
===================

[![Build Status](https://travis-ci.org/codete/FormGeneratorBundle.svg?branch=master)](https://travis-ci.org/codete/FormGeneratorBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/8893e0c9-ed68-498e-aa86-63320ac43a62/mini.png)](https://insight.sensiolabs.com/projects/8893e0c9-ed68-498e-aa86-63320ac43a62)

We were extremely bored with writing/generating/keeping-up-to-date
our FormType classes so we wanted to automate the process and limit
required changes only to Entity/Document/Whatever class and get new
form out of the box - this is how FormGenerator was invented.

We use annotations on daily basis so it was natural choice for 
forms' configuration although YAML/XML support is planned.

Basic Usages
------------

**`@Form\Display` annotation was deprecated in 1.3 and will be removed
in 2.0. Please use `@Form\Field` instead.**

Consider a class

``` php
/**
 * @Form\Form(
 *  personal = { "title", "name", "surname", "photo", "active" },
 *  work = { "salary" },
 *  admin = { "id" = { "type" = "number" }, "surname" }
 * )
 */
class Person
{
    public $id;
    
    /**
     * @Form\Field(type="choice", choices = { "Mr." = "mr", "Ms." = "ms" })
     */
    public $title;
    
    /**
     * @Form\Field(type="text")
     */
    public $name;
    
    /**
     * @Form\Field(type="text")
     */
    public $surname;
    
    /**
     * @Form\Field(type="file")
     */
    public $photo;
    
    /**
     * @Form\Field(type="checkbox")
     */
    public $active;
    
    /**
     * @Form\Field(type="money")
     */
    public $salary;
}
```

Now instead of writing whole ``PersonFormType`` and populating
FormBuilder there we can use instead:

``` php
use Codete\FormGeneratorBundle\FormGenerator;

// Symfony 3.3+ way
$generator = $this->get(FormGenerator::class);
// "old way"
$generator = $this->get('form_generator');

$person = new Person();
$form = $generator->createFormBuilder($person)->getForm();
$form->handleRequest($request);
```

Voila! Form for editing all annotated properties is generated for us.
We could even omit ``type=".."`` in annotations if Symfony will be
able to guess field type for us.

**Since Symfony 3.0, if you use a custom form type, you must specify the complete
namespace when specifying type**

Specifying Field Options
------------------------

By default everything you specify in `@Form\Field` (except for `type`) annotation
will be passed as an option to generated form type. To illustrate:

```php
/**
 * @Form\Field(type="choice", choices = { "Mr." = "mr", "Ms." = "ms" }, "attr" = { "class" = "foo" })
 */
public $title;
```

is equivalent to:

```php
$fb->add('title', ChoiceType::class, [
    'choices' => [ 'Mr.' => 'mr', 'Ms.' => 'ms' ],
    'attr' => [ 'class' => 'foo' ],
]);
```

**Specifying field options through `options`, described below, was added in 1.3.0.**

This approach has few advantages like saving you a bunch of keystrokes each time you
are specifying options, but there are downsides too. First, if you have any custom
option for one of your modifiers you forget to `unset`, Symfony will be unhappy and
will let you know by throwing an exception. Another downside is that we have reserved
`type` property and it's needed as an option for a repeated type. If you ever find
yourself in one of described cases, or you just prefer to be explicit, you can put
all Symfony fields' options into an `options` property:

```php
/**
 * @Form\Field(
 *   type="choice",
 *   options={ "choices" = { "Mr." = "mr", "Ms." = "ms" }, "attr" = { "class" = "foo" } }
 * )
 */
public $title;
```

When Form Generator creates a form field and finds `options` property, it will pass
them as that field's options to the `FormBuilder`. Effectively this allows you to
separate field's options from options for your configuration modifiers which can be
a gain on its own.

Adding fields not mapped to a property
--------------------------------------

**This feature was added in 1.3.0.**

Sometimes you may need to add a field that will not be mapped to a property. An example
of such use case is adding buttons to the form:

```php
/**
 * The first value in Field annotation specifies field's name.
 *
 * @Form\Field("reset", type="reset")
 * @Form\Field("submit", type="submit", "label"="Save")
 */
class Person
```

All fields added on the class level come last in the generated form, unless a form view 
(described below) specifies otherwise. Contrary to other class-level settings, `@Field`s
will not be inherited by child classes.

Form Views
----------

In the example we have defined additional form views in ``@Form\Form``
annotation so we can add another argument to ``createFormBuilder``

``` php
$form = $generator->createFormBuilder($person, 'personal')->getForm();
```

And we will get Form with properties specified in annotation. We can 
also add/override fields and their properties like this:

``` php
/**
 * @Form\Form(
 *  work = { "salary" = { "attr" = { "class" = "foo" } } }
 * )
 */
class Person
```

But if you need something more sophisticated than Annotations we 
have prepared few possibilities that can be either added manually
or by tagging your services. For each of them FormGenerator allows 
you to pass any additional informations you want in optional 
``$context`` argument. Both ways allows you to specify `priority`
which defines order of execution (default is `0`, if two or more
services have same priority then first added is executed first).

**If you have enabled [Service autoconfiguration](http://symfony.com/blog/new-in-symfony-3-3-service-autoconfiguration)
the bundle (since version 1.2.0) will automatically tag services for you.**

FormViewProvider
----------------

These are used to provide fields list and/or basic configuration
for Forms and are doing exactly same thing as ``@Form\Form``
annotation.

Tag for service: ``form_generator.view_provider``

FormConfigurationModifier
-------------------------

These can modify any form configuration provided by class
itself or FormViewProviders. Feel free to remove or add more
stuff to your Form or tweak existing configuration

Tag for service: ``form_generator.configuration_modifier``

``` php
class InactivePersonModifier implements FormConfigurationModifierInterface
{
    public function modify($model, $configuration, $context) 
    {
        unset($configuration['salary']);
        return $configuration;
    }

    public function supports($model, $configuration, $context) 
    {
        return $model instanceof Person && $model->active === false;
    }
}
```

FormFieldResolver
-----------------

These are responsible for creating actual field in Form and can
be used for instance to attach Transformers to your fields.

Tag for service: ``form_generator.field_resolver``

``` php
class PersonSalaryResolver implements FormFieldResolverInterface
{
    public function getFormField(FormBuilderInterface $fb, $field, $type, $options, $context) 
    {
        $transformer = new /* ... */;
        return $fb->create($field, $type, $options)
                ->addViewTransformer($transformer);
    }

    public function supports($model, $field, $type, $options, $context) 
    {
        return $model instanceof Person && $field === 'salary';
    }
}
```

Embedded Forms
--------------

If you need embedded forms we got you covered:

``` php
/**
 * @Form\Embed(class="Codete\FormGeneratorBundle\Tests\Model\Person")
 */
public $person;
```

Such sub-form will contain all annotated properties from given model.
To specify a view for the generated embedded form just specify it in
the configuration:

``` php
/**
 * @Form\Embed(
 *  class="Codete\FormGeneratorBundle\Tests\Model\Person",
 *  view="work"
 * )
 */
public $employee;
```
