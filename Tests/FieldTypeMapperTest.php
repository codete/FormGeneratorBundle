<?php
namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\FieldTypeMapper;

class FieldTypeMapperTest extends BaseTest
{
    /**
     * @dataProvider dataProvider
     * @param $actual
     * @param $expected
     */
    public function testMap($actual, $expected)
    {
        $this->assertEquals(FieldTypeMapper::map($actual), $expected);
    }

    public function dataProvider()
    {
        if (! method_exists('\Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return [
                ['choice', 'choice'],
                ['text', 'text'],
                ['dateTime', 'dateTime'],
                [null, null],
                ['unknown', 'unknown'],
                ['embed', 'embed']
            ];
        } else {
            return [
                ['choice', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType'],
                ['text', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType'],
                ['dateTime', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateTimeType'],
                [null, null],
                ['unknown', 'unknown'],
                ['embed', 'Codete\\FormGeneratorBundle\\Form\\Type\\EmbedType']
            ];
        }
    }
}
