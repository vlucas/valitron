<?php

use Valitron\ValidationSetInterface;
use Valitron\Validator;

class ConditionTest extends BaseTestCase
{
    public function testDependentSuccessfulValidation()
    {
        $v = new Validator(array('name' => 'John Doe', 'age' => '17'));
        $v->condition('min', 'age', 17, function(ValidationSetInterface $c) {
                $c->rule('required', 'name');
            })
			->condition('max', 'age', 15, function(ValidationSetInterface $c) {
                $c->rule('required', 'foo');
            });
        $this->assertTrue($v->validate());
    }

    public function testDependentRequired()
    {
        $v = new Validator(array('pets' => 'gerbil'));
        $v->rule('required', 'pets');
        $v->condition('required', 'name', function(ValidationSetInterface $c) {
                $c->rule('required', 'age');
            });
        $this->assertTrue($v->validate());
    }

    public function testDependentSuccessfulValidationTwoLevels()
    {
        $v = new Validator(array('name' => 'John Doe', 'age' => '17', 'pets' => 'dog'));
        $v->condition('min', 'age', 17, function(ValidationSetInterface $c) {
                    $c->condition('required', 'name', function(ValidationSetInterface $c) {
                        $c->rule('in', 'pets', array('dog', 'cat', 'goldfish'));
                    });
            })
            ->condition('max', 'age', 15, function(ValidationSetInterface $c) {
                $c->rule('required', 'foo');
            });
        $this->assertTrue($v->validate());
    }

    public function testDependentFailedValidation()
    {
        $v = new Validator(array('name' => 'John Doe', 'age' => '17'));
        $v->condition('min', 'age', 17, function(ValidationSetInterface $c) {
                $c->rule('required', 'name')
                    ->rule('required', 'foo');
            });
        $this->assertFalse($v->validate());
    }

    public function testDependentFailedValidationTwoLevels()
    {
        $v = new Validator(array('name' => 'John Doe', 'age' => '17', 'pets' => 'gerbil'));
        $v->condition('min', 'age', 17, function(ValidationSetInterface $c) {
                $c->condition('required', 'name', function(ValidationSetInterface $c) {
                        $c->rule('in', 'pets', array('dog', 'cat', 'goldfish'));
                    });
            })
            ->condition('max', 'age', 15, function(ValidationSetInterface $c) {
                $c->rule('required', 'foo');
            });
        $this->assertFalse($v->validate());
    }

    public function testDependentSetLabel()
    {
        $v = new Validator(array('age' => '17'));
        $v->condition('min', 'age', 17, function(ValidationSetInterface $c) {
                $c->rule('required', 'name')->label('Your name')
					->rule('required', 'bar')->message('{field} Ouch.')
					->rule('required', 'buzz');
            });

        $v->labels(array('bar' => 'A man walks into a bar.'));

        $v->validate();
        $expectedErrors = array(
            'name' => array('Your name is required'),
            'bar' => array('A man walks into a bar. Ouch.'),
            'buzz' => array('Buzz is required'),
        );
        $this->assertEquals($expectedErrors, $v->errors());
    }
}
