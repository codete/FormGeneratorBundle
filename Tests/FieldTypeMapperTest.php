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
            return array(
                array('choice', 'choice'),
                array('text', 'text'),
                array('dateTime', 'dateTime'),
                array(null, null),
                array('unknown', 'unknown'),
            );
        } else {
            return array(
                array('choice', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType'),
                array('text', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType'),
                array('dateTime', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\DateTimeType'),
                array(null, null),
                array('unknown', 'unknown'),
            );
        }
    }
}
