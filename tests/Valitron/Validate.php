<?php
use Valitron\Validator;

class ValidateTest extends \PHPUnit_Framework_TestCase
{
    public function testValidWithNoRules()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $this->assertTrue($v->validate());
    }

    public function testOptionalFieldFilter()
    {
        $v = new Validator(array('foo' => 'bar', 'bar' => 'baz'), array('foo'));
        $this->assertEquals($v->data(), array('foo' => 'bar'));
    }

    public function testRequired()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->required('name');
        $this->assertTrue($v->validate());
        $this->assertSame(1, count($v->errors('name')));
    }

    public function testRequiredNonExistentField()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->required('nonexistent_field');
        $this->assertFalse($v->validate());
    }

    public function testEmailValid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chester@tester.com'));
        $v->email('email');
        $this->assertTrue($v->validate());
    }

    public function testEmailInvalid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chestertesterman'));
        $v->rule('email', 'email');
        $this->assertFalse($v->validate());
    }

    public function testUrlValid()
    {
        $v = new Validator(array('website' => 'http://google.com'));
        $v->rule('url', 'website');
        $this->assertTrue($v->validate());
    }

    public function testUrlInvalid()
    {
        $v = new Validator(array('website' => 'shoobedobop'));
        $v->rule('url', 'website');
        $this->assertFalse($v->validate());
    }
}

