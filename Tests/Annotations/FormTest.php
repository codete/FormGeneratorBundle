<?php

namespace Codete\FormGeneratorBundle\Tests\Annotations;

use Codete\FormGeneratorBundle\Annotations\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultAlwaysExists()
    {
        $f = new Form([]);
        $this->assertSame([], $f->getForm('default'));
    }
    
    public function testDefaultCanBeOverwritten()
    {
        $d = ['foo' => 'bar'];
        $f = new Form(['default' => $d]);
        $this->assertSame($d, $f->getForm('default'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown form 'foo'
     */
    public function testUnknownFormThrowsException()
    {
        $f = new Form([]);
        $f->getForm('foo');
    }
    
    public function testNonDefaultForm()
    {
        $foo = ['foo' => 'bar', 'baz'];
        $f = new Form(['foo' => $foo]);
        $this->assertSame($foo, $f->getForm('foo'));
    }
}
 