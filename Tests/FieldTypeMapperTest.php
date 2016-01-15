<?php
namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\FieldTypeMapper;

class FieldTypeMapperTest extends BaseTest
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMapThrowsEcxeption()
    {
        FieldTypeMapper::map('unknownType');
    }

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
        return [
            ['choice', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType'],
            ['TEXT', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType'],
            [null, null],
        ];
    }
}
