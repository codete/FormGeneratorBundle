<?php

namespace Codete\FormGeneratorBundle;

class FieldTypeMapper
{
    protected static $typeNS = [
        'Symfony\\Component\\Form\\Extension\\Core\\Type',
        'Symfony\\Bridge\\Doctrine\\Form\\Type'
    ];

    /**
     * @param string $type
     * @return string
     */
    public static function map($type = 'text')
    {
        $formattedType = ucfirst(strtolower($type));
        foreach (self::$typeNS as $nameSpace) {
            if (class_exists($nameSpace.'\\'.$formattedType.'Type')) {
                return $nameSpace.'\\'.$formattedType.'Type';
            }
        }

        $message = 'The form type %s does not exists, consider update your dependencies.';
        throw new \InvalidArgumentException(sprintf($message, $type));
    }
}
