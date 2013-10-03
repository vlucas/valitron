<?php
use Valitron\Validator;

class DataBindingTest extends BaseTestCase
{


    public function testValidateWithLateBoundData()
    {
        $v = new Validator();
        $v->rule('required', array ( 'foo', 'bar' ));
        $v->setData(array ( 'foo' => 'test' ));
        $this->assertFalse($v->validate());
        $this->assertCount(1, $v->errors());
    }

    public function testResetData()
    {
        $v = new Validator(array ( 'foo' => 'bar' ));
        $this->assertEquals(array ( 'foo' => 'bar' ), $v->data());
        $v->setData(null);
        $this->assertEmpty($v->data());
    }

    public function testValidateCallWithData()
    {
        $v = new Validator();
        $v->rule('required', array ( 'foo', 'bar' ));
        $this->assertFalse($v->validate(array ( 'foo' => 'test' )));
        $this->assertCount(1, $v->errors());
    }

}
