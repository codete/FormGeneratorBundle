<?php

namespace Codete\FormGeneratorBundle;

class FieldTypeMapper
{
    private static $typeFQCN = null;

    private static $typeNS = [
        'Symfony\\Component\\Form\\Extension\\Core\\Type',
        'Symfony\\Bridge\\Doctrine\\Form\\Type',
    ];

    /**
     * @param string $type
     * @return string
     */
    public static function map($type)
    {
        if (self::$typeFQCN === null) {
            self::$typeFQCN = method_exists('\Symfony\Component\Form\AbstractType', 'getBlockPrefix');
        }

        if (! self::$typeFQCN || $type === null) {
            return $type;
        }

        $formattedType = ucfirst($type);
        foreach (self::$typeNS as $nameSpace) {
            if (class_exists($nameSpace . '\\' . $formattedType . 'Type')) {
                return $nameSpace . '\\' . $formattedType . 'Type';
            }
        }

        return $type;
    }
}
