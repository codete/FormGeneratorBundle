<?php

namespace Codete\FormGeneratorBundle\Tests\Annotations;

use Codete\FormGeneratorBundle\Annotations\Form;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultAlwaysExists()
    {
        $f = new Form(array());
        $this->assertSame(array(), $f->getForm('default'));
    }
    
    public function testDefaultCanBeOverwritten()
    {
        $d = array('foo' => 'bar');
        $f = new Form(array('default' => $d));
        $this->assertSame($d, $f->getForm('default'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown form 'foo'
     */
    public function testUnknownFormThrowsException()
    {
        $f = new Form(array());
        $f->getForm('foo');
    }
    
    public function testNonDefaultForm()
    {
        $foo = array('foo' => 'bar', 'baz');
        $f = new Form(array('foo' => $foo));
        $this->assertSame($foo, $f->getForm('foo'));
    }
}
 