<?php

use Valitron\Validator;

class IndexErrorByRuleTest extends BaseTestCase
{
    public function testErrorMessageIsIndexedByNumberByDefault()
    {
        $v = new Validator(array());
        $v->rule('required', 'name');
        $v->validate();
        $this->assertSame(array("Name is required"), $v->errors('name'));
    }

    public function testErrorMessageIsIndexedByRuleName()
    {
        $v = new Validator(array());
        $v->setIndexErrorByRule(/*true*/);
        $v->rule('required', 'name');
        $v->validate();
        $this->assertSame(array("required" => "Name is required"), $v->errors('name'));
    }

    public function testDisableIndexByRuleName()
    {
        $v = new Validator(array());
        $v->setIndexErrorByRule(false);
        $v->rule('required', 'name');
        $v->validate();
        $this->assertSame(array("Name is required"), $v->errors('name'));
    }

    public function testFieldWithSeveralMessages()
    {
        $v = new Validator(array('name' => 'Joe'));
        $v->setIndexErrorByRule(/*true*/);
        $v->rule('length', 'name', 5);
        $v->rule('lengthMin', 'name', 10);
        $v->rule('lengthMax', 'name', 1);
        $v->validate();
        $this->assertSame(
            array(
                'length' => 'Name must be 5 characters long',
                'lengthMin' => 'Name must be at least 10 characters long',
                'lengthMax' => 'Name must not exceed 1 characters'
            ),
            $v->errors('name')
        );
    }
}