# Upgrading to 2.0

Following sections will describe all steps needed to update from version 1.x to 2.0.

## Requirements

This version of FormGeneratorBundle requires you to run at least PHP 7.1.
Additionally following requirements have been changed compared to 1.x:

- doctrine/annotations `~1.0` -> `~1.2` 
- symfony/form `>=2.7` -> `~3.4|~4.0` 
- symfony/framework-bundle `>=2.7` -> `~3.4|~4.0`

## Symfony2 form types are no longer allowed

With 1.x version of library it was possible to use names of form types and even when
using newer version, the FormGeneratorBundle was mapping them to their FQCN counterparts:

```php
/**
 * @Form\Field(type="choice", choices = { "Mr." = "mr", "Ms." = "ms" }, "attr" = { "class" = "foo" })
 */
public $title;
```

Each and every `type` must now contain the FQCN of the type to be used. We suggest to go 
with the `::class` notation:

```php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Form\Field(type=ChoiceType::class, choices = { "Mr." = "mr", "Ms." = "ms" }, "attr" = { "class" = "foo" })
 */
public $title;
```

## ChoiceType's choices format has been changed

Although this change does not come from the bundle itself, it's a notable one. Please mind
that Symfony changed the `choices` array format from `value => label` to `label => value`.
Also Symfony 4.0 removed the `choices_as_values` flag.

## Other changes

- The `Codete\FormGeneratorBundle\Form\Type\EmbedType::TYPE` constant has been removed
as it no longer served any purpose