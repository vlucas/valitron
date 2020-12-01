<?php

use Valitron\Validator;

class ErrorMessagesTest extends BaseTestCase
{
    public function testErrorMessageIncludesFieldName()
    {
        $v = new Validator(array());
        $v->rule('required', 'name');
        $v->validate();
        $this->assertSame(array("Name is required"), $v->errors('name'));
    }

    /**
     * Test the disabling of prepending the field labels
     * to error messages.
     */
    public function testErrorMessageExcludeFieldName()
    {
        $v = new Validator(array());
        $v->setPrependLabels(false);
        $v->rule('required', 'name');
        $v->validate();
        $this->assertSame(array("is required"), $v->errors('name'));
    }

    public function testAccurateErrorMessageParams()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 6);
        $v->validate();
        $this->assertSame(array("Num must be at least 6"), $v->errors('num'));
    }

    public function testCustomErrorMessage()
    {
        $v = new Validator(array());
        $v->rule('required', 'name')->message('Name is required');
        $v->validate();
        $errors = $v->errors('name');
        $this->assertSame('Name is required', $errors[0]);
    }

    public function testCustomLabel()
    {
        $v = new Validator(array());
        $v->rule('required', 'name')->message('{field} is required')->label('Custom Name');
        $v->validate();
        $errors = $v->errors('name');
        $this->assertSame('Custom Name is required', $errors[0]);
    }

    public function testCustomLabels()
    {
        $messages = array(
            'name' => array('Name is required'),
            'email' => array('Email should be a valid email address')
        );

        $v = new Validator(array('name' => '', 'email' => '$'));
        $v->rule('required', 'name')->message('{field} is required');
        $v->rule('email', 'email')->message('{field} should be a valid email address');

        $v->labels(array(
            'name' => 'Name',
            'email' => 'Email'
        ));

        $v->validate();
        $errors = $v->errors();
        $this->assertEquals($messages, $errors);
    }

    public function testMessageWithFieldSet()
    {
        $v = new Validator(array('name' => ''), array(), 'en', __DIR__ . '/../lang');
        $v->rule('required', 'name');
        $v->validate();
        $this->assertEquals($v->errors('name'), array('A value is required for Name'));
    }

    public function testMessageWithFieldAndLabelSet()
    {
        $v = new Validator(array('name' => ''), array(), 'en', __DIR__ . '/../lang');
        $v->rule('required', 'name')->label('my name');
        $v->validate();
        $this->assertEquals($v->errors('name'), array('A value is required for my name'));
    }
}

