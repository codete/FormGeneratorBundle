<?php

namespace Codete\FormGeneratorBundle\Tests;

use Codete\FormGeneratorBundle\AdjusterRegistry;
use Codete\FormGeneratorBundle\FormConfigurationFactory;

class FormConfigurationFactoryTest extends BaseTest
{
    /**
     * @dataProvider provideFieldsNormalization
     */
    public function testFieldsNormalization($toNormalize, $expected)
    {
        $factory = new FormConfigurationFactory(new AdjusterRegistry());
        $r = new \ReflectionObject($factory);
        $m = $r->getMethod('normalizeFields');
        $m->setAccessible(true);
        $this->assertSame($expected, $m->invoke($factory, $toNormalize));
    }

    public function provideFieldsNormalization()
    {
        return [
            [
                ['foo', 'bar'],
                ['foo' => [], 'bar' => []],
            ],
            [
                ['foo' => ['bar' => 'baz']],
                ['foo' => ['bar' => 'baz']],
            ],
            [
                ['foo', 'bar' => []],
                ['foo' => [], 'bar' => []],
            ],
        ];
    }
}
