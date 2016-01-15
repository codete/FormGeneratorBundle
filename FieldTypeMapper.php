<?php

namespace Codete\FormGeneratorBundle;

use Symfony\Component\HttpKernel\Kernel;

class FieldTypeMapper
{
    protected static $typeNS = array(
        'Symfony\\Component\\Form\\Extension\\Core\\Type',
        'Symfony\\Bridge\\Doctrine\\Form\\Type'
    );

    /**
     * @param string $type
     * @return string
     */
    public static function map($type)
    {
        $sfVersion = intval(Kernel::VERSION);

        if (is_null($type) || $sfVersion < 3) {
            return $type;
        }

        $formattedType = ucfirst($type);
        foreach (self::$typeNS as $nameSpace) {
            if (class_exists($nameSpace.'\\'.$formattedType.'Type')) {
                return $nameSpace.'\\'.$formattedType.'Type';
            }
        }

        return $type;
    }
}
