<?php

use Valitron\Validator;

class ValidateTest extends BaseTestCase
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

    public function testAccurateErrorShouldReturnFalse()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->rule('required', 'name');
        $this->assertFalse($v->errors('name'));
    }

    public function testArrayOfFieldsToValidate()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chester@tester.com'));
        $v->rule('required', array('name', 'email'));
        $this->assertTrue($v->validate());
    }

    public function testArrayOfFieldsToValidateOneEmpty()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => ''));
        $v->rule('required', array('name', 'email'));
        $this->assertFalse($v->validate());
    }

    public function testRequiredSubfieldsArrayStringValue()
    {
        $v = new Validator(array('name' => 'bob'));
        $v->rule('required', array('name.*.red'));
        $this->assertFalse($v->validate());
    }

    public function testRequiredValid()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->rule('required', 'name');
        $this->assertTrue($v->validate());
    }

    public function testRequiredValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'spiderman', 'password' => 'Gr33nG0Blin', 'required_but_null' => null));
        $v->rules(array(
            'required' => array(
                array('username'),
                array('password'),
                array('required_but_null', true) // boolean flag allows empty value so long as the field name is set on the data array
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testRequiredNonExistentField()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->rule('required', 'nonexistent_field');
        $this->assertFalse($v->validate());
    }

    public function testRequiredNonExistentFieldAltSyntax()
    {
        $v = new Valitron\Validator(array('boozername' => 'spiderman', 'notPassword' => 'Gr33nG0Blin'));
        $v->rules(array(
            'required' => array(
                array('username'),
                array('password')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testEqualsValid()
    {
        $v = new Validator(array('foo' => 'bar', 'bar' => 'bar'));
        $v->rule('equals', 'foo', 'bar');
        $this->assertTrue($v->validate());
    }

    public function testEqualsValidAltSyntax()
    {
        $v = new Validator(array('password' => 'youshouldnotseethis', 'confirmPassword' => 'youshouldnotseethis'));
        $v->rules(array(
            'equals' => array(
                array('password', 'confirmPassword')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testEqualsInvalid()
    {
        $v = new Validator(array('foo' => 'foo', 'bar' => 'bar'));
        $v->rule('equals', 'foo', 'bar');
        $this->assertFalse($v->validate());
    }

    public function testEqualsInvalidAltSyntax()
    {
        $v = new Validator(array('password' => 'youshouldnotseethis', 'confirmPassword' => 'differentpassword'));
        $v->rules(array(
            'equals' => array(
                array('password', 'confirmPassword')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testEqualsBothNull()
    {
        $v = new Validator(array('foo' => null, 'bar' => null));
        $v->rule('equals', 'foo', 'bar');
        $this->assertTrue($v->validate());
    }

    public function testEqualsBothNullRequired()
    {
        $v = new Validator(array('foo' => null, 'bar' => null));
        $v->rule('required', array('foo', 'bar'));
        $v->rule('equals', 'foo', 'bar');
        $this->assertFalse($v->validate());
    }

    public function testEqualsBothUnset()
    {
        $v = new Validator(array('foo' => 1));
        $v->rule('equals', 'bar', 'baz');
        $this->assertTrue($v->validate());
    }

    public function testEqualsBothUnsetRequired()
    {
        $v = new Validator(array('foo' => 1));
        $v->rule('required', array('bar', 'baz'));
        $v->rule('equals', 'bar', 'baz');
        $this->assertFalse($v->validate());
    }

    public function testDifferentValid()
    {
        $v = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v->rule('different', 'foo', 'bar');
        $this->assertTrue($v->validate());
    }

    public function testDifferentValidAltSyntax()
    {
        $v = new Validator(array('username' => 'test', 'password' => 'test123'));
        $v->rules(array(
            'different' => array(
                array('username', 'password')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testDifferentInvalid()
    {
        $v = new Validator(array('foo' => 'baz', 'bar' => 'baz'));
        $v->rule('different', 'foo', 'bar');
        $this->assertFalse($v->validate());
    }

    public function testDifferentInvalidAltSyntax()
    {
        $v = new Validator(array('username' => 'test', 'password' => 'test'));
        $v->rules(array(
            'different' => array(
                array('username', 'password')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testDifferentBothNull()
    {
        $v = new Validator(array('foo' => null, 'bar' => null));
        $v->rule('equals', 'foo', 'bar');
        $this->assertTrue($v->validate());
    }

    public function testDifferentBothNullRequired()
    {
        $v = new Validator(array('foo' => null, 'bar' => null));
        $v->rule('required', array('foo', 'bar'));
        $v->rule('equals', 'foo', 'bar');
        $this->assertFalse($v->validate());
    }

    public function testDifferentBothUnset()
    {
        $v = new Validator(array('foo' => 1));
        $v->rule('equals', 'bar', 'baz');
        $this->assertTrue($v->validate());
    }

    public function testDifferentBothUnsetRequired()
    {
        $v = new Validator(array('foo' => 1));
        $v->rule('required', array('bar', 'baz'));
        $v->rule('equals', 'bar', 'baz');
        $this->assertFalse($v->validate());
    }

    public function testAcceptedValid()
    {
        $v = new Validator(array('agree' => 'yes'));
        $v->rule('accepted', 'agree');
        $this->assertTrue($v->validate());
    }

    public function testAcceptedValidAltSyntax()
    {
        $v = new Valitron\Validator(array('remember_me' => true));
        $v->rules(array(
            'accepted' => array(
                array('remember_me')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testAcceptedInvalid()
    {
        $v = new Validator(array('agree' => 'no'));
        $v->rule('accepted', 'agree');
        $this->assertFalse($v->validate());
    }

    public function testAcceptedInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('remember_me' => false));
        $v->rules(array(
            'accepted' => array(
                array('remember_me')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testAcceptedNotSet()
    {
        $v = new Validator();
        $v->rule('accepted', 'agree');
        $this->assertFalse($v->validate());
    }

    public function testNumericValid()
    {
        $v = new Validator(array('num' => '42.341569'));
        $v->rule('numeric', 'num');
        $this->assertTrue($v->validate());
    }

    public function testNumericValidAltSyntax()
    {
        $v = new Valitron\Validator(array('amount' => 3.14));
        $v->rules(array(
            'numeric' => array(
                array('amount')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testNumericInvalid()
    {
        $v = new Validator(array('num' => 'nope'));
        $v->rule('numeric', 'num');
        $this->assertFalse($v->validate());
    }

    public function testNumericInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('amount' => 'banana'));
        $v->rules(array(
            'numeric' => array(
                array('amount')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testIntegerValid()
    {
        $v = new Validator(array('num' => '41243'));
        $v->rule('integer', 'num');
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => '-41243'));
        $v->rule('integer', 'num');
        $this->assertTrue($v->validate());
    }

    public function testIntegerValidAltSyntax()
    {
        $v = new Valitron\Validator(array('age' => 27));
        $v->rules(array(
            'integer' => array(
                array('age', true)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testIntegerStrict()
    {

        $v = new Validator(array('num' => ' 41243'));
        $v->rule('integer', 'num');
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => ' 41243'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '+41243'));
        $v->rule('integer', 'num');
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => '+41243'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '-1'));
        $v->rule('integer', 'num', true);
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => '-0'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '0'));
        $v->rule('integer', 'num', true);
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => '+0'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '+1'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '0123'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '-0123'));
        $v->rule('integer', 'num', true);
        $this->assertFalse($v->validate());
    }

    public function testIntegerInvalid()
    {
        $v = new Validator(array('num' => '42.341569'));
        $v->rule('integer', 'num');
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '--1231'));
        $v->rule('integer', 'num');
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => '0x3a'));
        $v->rule('integer', 'num');
        $this->assertFalse($v->validate());
    }

    public function testIntegerInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('age' => 3.14));
        $v->rules(array(
            'integer' => array(
                array('age')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testLengthValid()
    {
        $v = new Validator(array('str' => 'happy'));
        $v->rule('length', 'str', 5);
        $this->assertTrue($v->validate());
    }

    public function testLengthValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'bobburgers'));
        $v->rules(array(
            'length' => array(
                array('username', 10)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testLengthInvalid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('length', 'str', 6);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => array()));
        $v->rule('length', 'test', 1);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => new stdClass));
        $v->rule('length', 'test', 1);
        $this->assertFalse($v->validate());
    }

    public function testLengthInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'hi'));
        $v->rules(array(
            'length' => array(
                array('username', 10)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testLengthBetweenValid()
    {
        $v = new Validator(array('str' => 'happy'));
        $v->rule('lengthBetween', 'str', 2, 8);
        $this->assertTrue($v->validate());
    }

    public function testLengthBetweenValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'bobburgers'));
        $v->rules(array(
            'lengthBetween' => array(
                array('username', 1, 10)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testLengthBetweenInvalid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('lengthBetween', 'str', 4, 10);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => array()));
        $v->rule('lengthBetween', 'test', 50, 60);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => new stdClass));
        $v->rule('lengthBetween', 'test', 99, 100);
        $this->assertFalse($v->validate());
    }

    public function testLengthBetweenInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'hi'));
        $v->rules(array(
            'lengthBetween' => array(
                array('username', 3, 10)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testLengthMinValid()
    {
        $v = new Validator(array('str' => 'happy'));
        $v->rule('lengthMin', 'str', 4);
        $this->assertTrue($v->validate());
    }
    
    public function testLengthMinValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'martha'));
        $v->rules(array(
            'lengthMin' => array(
                array('username', 5)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testLengthMinInvalid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('lengthMin', 'str', 4);
        $this->assertFalse($v->validate());
    }

    public function testLengthMinInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'abc'));
        $v->rules(array(
            'lengthMin' => array(
                array('username', 5)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testLengthMaxValid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('lengthMax', 'str', 4);
        $this->assertTrue($v->validate());
    }

    public function testLengthMaxValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'bruins91'));
        $v->rules(array(
            'lengthMax' => array(
                array('username', 10)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testLengthMaxInvalid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('lengthMax', 'str', 2);
        $this->assertFalse($v->validate());
    }

    public function testLengthMaxInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'bruins91'));
        $v->rules(array(
            'lengthMax' => array(
                array('username', 3)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testMinValid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 2);
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 5);
        $this->assertTrue($v->validate());
    }

    public function testMinValidAltSyntax()
    {
        $v = new Valitron\Validator(array('age' => 28));
        $v->rules(array(
            'min' => array(
                array('age', 18)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testMinValidFloat()
    {
        if (!function_exists('bccomp')) {
            $this->markTestSkipped("Floating point comparison requires the BC Math extension to be installed");
        }

        $v = new Validator(array('num' => 0.9));
        $v->rule('min', 'num', 0.5);
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => 1 - 0.81));
        $v->rule('min', 'num', 0.19);
        $this->assertTrue($v->validate());
    }

    public function testMinInvalid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 6);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => array()));
        $v->rule('min', 'test', 1);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => new stdClass));
        $v->rule('min', 'test', 1);
        $this->assertFalse($v->validate());
    }

    public function testMinInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('age' => 16));
        $v->rules(array(
            'min' => array(
                array('age', 18)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testMinInvalidFloat()
    {
        $v = new Validator(array('num' => 0.5));
        $v->rule('min', 'num', 0.9);
        $this->assertFalse($v->validate());
    }

    public function testMaxValid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('max', 'num', 6);
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => 5));
        $v->rule('max', 'num', 5);
        $this->assertTrue($v->validate());
    }

    public function testMaxValidAltSyntax()
    {
        $v = new Valitron\Validator(array('age' => 10));
        $v->rules(array(
            'max' => array(
                array('age', 12)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testMaxValidFloat()
    {
        if (!function_exists('bccomp')) {
            $this->markTestSkipped("Accurate floating point comparison requires the BC Math extension to be installed");
        }

        $v = new Validator(array('num' => 0.4));
        $v->rule('max', 'num', 0.5);
        $this->assertTrue($v->validate());

        $v = new Validator(array('num' => 1 - 0.83));
        $v->rule('max', 'num', 0.17);
        $this->assertTrue($v->validate());
    }

    public function testMaxInvalid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('max', 'num', 4);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => array()));
        $v->rule('min', 'test', 1);
        $this->assertFalse($v->validate());

        $v = new Validator(array('test' => new stdClass));
        $v->rule('min', 'test', 1);
        $this->assertFalse($v->validate());
    }

    public function testMaxInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('age' => 29));
        $v->rules(array(
            'max' => array(
                array('age', 12)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testMaxInvalidFloat()
    {
        $v = new Validator(array('num' => 0.9));
        $v->rule('max', 'num', 0.5);
        $this->assertFalse($v->validate());
    }

    public function testBetweenValid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('between', 'num', array(3, 7));
        $this->assertTrue($v->validate());
    }

    public function testBetweenInvalid()
    {
        $v = new Validator(array('num' => 3));
        $v->rule('between', 'num', array(5, 10));
        $this->assertFalse($v->validate());
    }

    public function testBetweenInvalidValue()
    {
        $v = new Validator(array('num' => array(3)));
        $v->rule('between', 'num', array(5, 10));
        $this->assertFalse($v->validate());
    }

    public function testBetweenInvalidRange()
    {
        $v = new Validator(array('num' => 3));
        $v->rule('between', 'num');
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => 3));
        $v->rule('between', 'num', 5);
        $this->assertFalse($v->validate());

        $v = new Validator(array('num' => 3));
        $v->rule('between', 'num', array(5));
        $this->assertFalse($v->validate());
    }

    public function testInValid()
    {
        $v = new Validator(array('color' => 'green'));
        $v->rule('in', 'color', array('red', 'green', 'blue'));
        $this->assertTrue($v->validate());
    }

    public function testInValidAltSyntax()
    {
        $v = new Valitron\Validator(array('color' => 'purple'));
        $v->rules(array(
            'in' => array(
                array('color', array('blue', 'green', 'red', 'purple'))
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testInInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('color' => 'orange'));
        $v->rules(array(
            'in' => array(
                array('color', array('blue', 'green', 'red', 'purple'))
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testInValidAssociativeArray()
    {
        $v = new Validator(array('color' => 'green'));
        $v->rule('in', 'color', array(
            'red' => 'Red',
            'green' => 'Green',
            'blue' => 'Blue'
        ));
        $this->assertTrue($v->validate());
    }

    public function testInStrictInvalid()
    {
        $v = new Validator(array('color' => '1'));
        $v->rule('in', 'color', array(1, 2, 3), true);
        $this->assertFalse($v->validate());
    }

    public function testArrayValid()
    {
        $v = new Validator(array('colors' => array('yellow')));
        $v->rule('array', 'colors');
        $this->assertTrue($v->validate());
    }

    public function testArrayValidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_notifications' => array('bulletin_notifications' => true, 'marketing_notifications' => false, 'message_notification' => true)));
        $v->rules(array(
            'array' => array(
                array('user_notifications')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testAssocArrayValid()
    {
        $v = new Validator(array('settings' => array('color' => 'yellow')));
        $v->rule('array', 'settings');
        $this->assertTrue($v->validate());
    }

    public function testArrayInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_notifications' => 'string'));
        $v->rules(array(
            'array' => array(
                array('user_notifications')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testArrayInvalid()
    {
        $v = new Validator(array('colors' => 'yellow'));
        $v->rule('array', 'colors');
        $this->assertFalse($v->validate());
    }

    public function testArrayAccess()
    {
        $v = new Validator(array('settings' => array('enabled' => true)));
        $v->rule('boolean', 'settings.enabled');
        $this->assertTrue($v->validate());
    }

    public function testArrayAccessInvalid()
    {
        $v = new Validator(array('settings' => array('threshold' => 500)));
        $v->rule('max', 'settings.threshold', 100);
        $this->assertFalse($v->validate());
    }

    public function testForeachDiscreteValues()
    {
        $v = new Validator(array('values' => array(5, 10, 15, 20, 25)));
        $v->rule('integer', 'values.*');
        $this->assertTrue($v->validate());
    }

    public function testForeachAssocValues()
    {
        $v = new Validator(array('values' => array(
            'foo' => 5,
            'bar' => 10,
            'baz' => 15
        )));
        $v->rule('integer', 'values.*');
        $this->assertTrue($v->validate());
    }

    public function testForeachAssocValuesFails()
    {
        $v = new Validator(array('values' => array(
            'foo' => 5,
            'bar' => 10,
            'baz' => 'faz'
        )));
        $v->rule('integer', 'values.*');
        $this->assertFalse($v->validate());
    }

    public function testForeachArrayAccess()
    {
        $v = new Validator(array('settings' => array(
            array('enabled' => true),
            array('enabled' => true)
        )));
        $v->rule('boolean', 'settings.*.enabled');
        $this->assertTrue($v->validate());
    }

    public function testForeachArrayAccessInvalid()
    {
        $v = new Validator(array('settings' => array(
            array('threshold' => 50),
            array('threshold' => 500)
        )));
        $v->rule('max', 'settings.*.threshold', 100);
        $this->assertFalse($v->validate());
    }

    public function testNestedForeachArrayAccess()
    {
        $v = new Validator(array('widgets' => array(
            array('settings' => array(
                array('enabled' => true),
                array('enabled' => true)
            )),
            array('settings' => array(
                array('enabled' => true),
                array('enabled' => true)
            ))
        )));
        $v->rule('boolean', 'widgets.*.settings.*.enabled');
        $this->assertTrue($v->validate());
    }

    public function testNestedForeachArrayAccessInvalid()
    {
        $v = new Validator(array('widgets' => array(
            array('settings' => array(
                array('threshold' => 50),
                array('threshold' => 90)
            )),
            array('settings' => array(
                array('threshold' => 40),
                array('threshold' => 500)
            ))
        )));
        $v->rule('max', 'widgets.*.settings.*.threshold', 100);
        $this->assertFalse($v->validate());
    }

    public function testInInvalid()
    {
        $v = new Validator(array('color' => 'yellow'));
        $v->rule('in', 'color', array('red', 'green', 'blue'));
        $this->assertFalse($v->validate());
    }

    public function testNotInValid()
    {
        $v = new Validator(array('color' => 'yellow'));
        $v->rule('notIn', 'color', array('red', 'green', 'blue'));
        $this->assertTrue($v->validate());
    }

    public function testNotInValidAltSyntax()
    {
        $v = new Valitron\Validator(array('color' => 'purple'));
        $v->rules(array(
            'notIn' => array(
                array('color', array('blue', 'green', 'red', 'yellow'))
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testNotInInvalid()
    {
        $v = new Validator(array('color' => 'blue'));
        $v->rule('notIn', 'color', array('red', 'green', 'blue'));
        $this->assertFalse($v->validate());
    }

    public function testNotInInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('color' => 'yellow'));
        $v->rules(array(
            'notIn' => array(
                array('color', array('blue', 'green', 'red', 'yellow'))
            )
        ));
    }

    public function testAsciiValid()
    {
        $v = new Validator(array('text' => '12345 abcde'));
        $v->rule('ascii', 'text');
        $this->assertTrue($v->validate());
    }

    public function testAsciiValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'batman123'));
        $v->rules(array(
            'ascii' => array(
                array('username')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testAsciiInvalid()
    {
        $v = new Validator(array('text' => '12345 abcdé'));
        $v->rule('ascii', 'text');
        $this->assertFalse($v->validate());
    }

    public function testAsciiInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => '12345 abcdé'));
        $v->rules(array(
            'ascii' => array(
                array('username')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testIpValid()
    {
        $v = new Validator(array('ip' => '127.0.0.1'));
        $v->rule('ip', 'ip');
        $this->assertTrue($v->validate());
    }

    public function testIpValidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_ip' => '127.0.0.1'));
        $v->rules(array(
            'ip' => array(
                array('user_ip')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testIpInvalid()
    {
        $v = new Validator(array('ip' => 'buy viagra now!'));
        $v->rule('ip', 'ip');
        $this->assertFalse($v->validate());
    }

    public function testIpInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_ip' => '127.0.0.1.345'));
        $v->rules(array(
            'ip' => array(
                array('user_ip')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testIpv4Valid()
    {
        $v = new Validator(array('ip' => '127.0.0.1'));
        $v->rule('ipv4', 'ip');
        $this->assertTrue($v->validate());
    }

    public function testIpv4ValidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_ip' => '127.0.0.1'));
        $v->rules(array(
            'ipv4' => array(
                array('user_ip')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testIpv4Invalid()
    {
        $v = new Validator(array('ip' => 'FE80::0202:B3FF:FE1E:8329'));
        $v->rule('ipv4', 'ip');
        $this->assertFalse($v->validate());
    }

    public function testIpv4InvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_ip' => '127.0.0.1.234'));
        $v->rules(array(
            'ipv4' => array(
                array('user_ip')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testIpv6Valid()
    {
        $v = new Validator(array('ip' => 'FE80::0202:B3FF:FE1E:8329'));
        $v->rule('ipv6', 'ip');
        $this->assertTrue($v->validate());
    }

    public function testIpv6ValidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_ipv6' => '0:0:0:0:0:0:0:1'));
        $v->rules(array(
            'ipv6' => array(
                array('user_ipv6')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testIpv6Invalid()
    {
        $v = new Validator(array('ip' => '127.0.0.1'));
        $v->rule('ipv6', 'ip');
        $this->assertFalse($v->validate());
    }

    public function testIpv6InvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_ipv6' => '0:0:0:0:0:0:0:1:3:4:5'));
        $v->rules(array(
            'ipv6' => array(
                array('user_ipv6')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testEmailValid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chester@tester.com'));
        $v->rule('email', 'email');
        $this->assertTrue($v->validate());
    }

    public function testEmailValidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_email' => 'someone@example.com'));
        $v->rules(array(
            'email' => array(
                array('user_email')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testEmailInvalid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chestertesterman'));
        $v->rule('email', 'email');
        $this->assertFalse($v->validate());
    }

    public function testEmailInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_email' => 'example.com'));
        $v->rules(array(
            'email' => array(
                array('user_email')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testEmailDnsValid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chester@tester.com'));
        $v->rule('emailDNS', 'email');
        $this->assertTrue($v->validate());
    }

    public function testEmailDnsValidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_email' => 'some_fake_email_address@gmail.com'));
        $v->rules(array(
            'emailDNS' => array(
                array('user_email')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testEmailDnsInvalid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chester@tester.zyx'));
        $v->rule('emailDNS', 'email');
        $this->assertFalse($v->validate());
    }

    public function testEmailDnsInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('user_email' => 'some_fake_email_address@gmail.zyx'));
        $v->rules(array(
            'emailDNS' => array(
                array('user_email')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testUrlValid()
    {
        $v = new Validator(array('website' => 'http://google.com'));
        $v->rule('url', 'website');
        $this->assertTrue($v->validate());
    }

    public function testUrlValidAltSyntax()
    {
        $v = new Valitron\Validator(array('website' => 'https://example.com/contact'));
        $v->rules(array(
            'url' => array(
                array('website')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testUrlInvalid()
    {
        $v = new Validator(array('website' => 'shoobedobop'));
        $v->rule('url', 'website');
        $this->assertFalse($v->validate());
    }

    public function testUrlInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('website' => 'thisisjusttext'));
        $v->rules(array(
            'url' => array(
                array('website')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testUrlActive()
    {
        $v = new Validator(array('website' => 'http://google.com'));
        $v->rule('urlActive', 'website');
        $this->assertTrue($v->validate());
    }

    public function testUrlActiveValidAltSyntax()
    {
        $v = new Valitron\Validator(array('website' => 'https://example.com/contact'));
        $v->rules(array(
            'urlActive' => array(
                array('website')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testUrlInactive()
    {
        $v = new Validator(array('website' => 'http://example-test-domain-' . md5(time()) . '.com'));
        $v->rule('urlActive', 'website');
        $this->assertFalse($v->validate());
    }

    public function testUrlActiveInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('website' => 'https://example-domain'));
        $v->rules(array(
            'urlActive' => array(
                array('website')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testAlphaValid()
    {
        $v = new Validator(array('test' => 'abcDEF'));
        $v->rule('alpha', 'test');
        $this->assertTrue($v->validate());
    }

    public function testAlphaValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'batman'));
        $v->rules(array(
            'alpha' => array(
                array('username')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testAlphaInvalid()
    {
        $v = new Validator(array('test' => 'abc123'));
        $v->rule('alpha', 'test');
        $this->assertFalse($v->validate());
    }

    public function testAlphaInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => '123456asdf'));
        $v->rules(array(
            'alpha' => array(
                array('username')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testAlphaNumValid()
    {
        $v = new Validator(array('test' => 'abc123'));
        $v->rule('alphaNum', 'test');
        $this->assertTrue($v->validate());
    }

    public function testAlphaNumValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'batman123'));
        $v->rules(array(
            'alphaNum' => array(
                array('username')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testAlphaNumInvalid()
    {
        $v = new Validator(array('test' => 'abc123$%^'));
        $v->rule('alphaNum', 'test');
        $this->assertFalse($v->validate());
    }

    public function testAlphaNumInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'batman123-$'));
        $v->rules(array(
            'alphaNum' => array(
                array('username')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testAlphaDashValid()
    {
        $v = new Validator(array('test' => 'abc-123_DEF'));
        $v->rule('slug', 'test');
        $this->assertTrue($v->validate());
    }

    public function testSlugValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'L337-H4ckZ0rz_123'));
        $v->rules(array(
            'slug' => array(
                array('username')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testAlphaDashInvalid()
    {
        $v = new Validator(array('test' => 'abc-123_DEF $%^'));
        $v->rule('slug', 'test');
        $this->assertFalse($v->validate());
    }

    public function testSlugInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'L337-H4ckZ0rz_123 $%^'));
        $v->rules(array(
            'slug' => array(
                array('username')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testNoErrorFailOnArray()
    {
        $v = new Validator(array('test' => array()));
        $v->rule('slug', 'test');
        $this->assertFalse($v->validate());
    }

    public function testRegexValid()
    {
        $v = new Validator(array('test' => '42'));
        $v->rule('regex', 'test', '/[\d]+/');
        $this->assertTrue($v->validate());
    }

    public function testRegexValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'Batman123'));
        $v->rules(array(
            'regex' => array(
                array('username', '/^[a-zA-Z0-9]{5,10}$/')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testRegexInvalid()
    {
        $v = new Validator(array('test' => 'istheanswer'));
        $v->rule('regex', 'test', '/[\d]+/');
        $this->assertFalse($v->validate());
    }

    public function testRegexInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'Batman_123'));
        $v->rules(array(
            'regex' => array(
                array('username', '/^[a-zA-Z0-9]{5,10}$/')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testDateValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('date', 'date');
        $this->assertTrue($v->validate());
    }

    public function testDateValidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '2018-10-13'));
        $v->rules(array(
            'date' => array(
                array('created_at')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testDateValidWithDateTimeObject()
    {
        $v = new Validator(array('date' => new DateTime()));
        $v->rule('date', 'date');
        $this->assertTrue($v->validate());
    }

    public function testDateInvalid()
    {
        $v = new Validator(array('date' => 'no thanks'));
        $v->rule('date', 'date');
        $this->assertFalse($v->validate());
    }

    public function testDateInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => 'bananas'));
        $v->rules(array(
            'date' => array(
                array('created_at')
            )
        ));
        $this->assertFalse($v->validate());
    }

    /**
     * @group issue-13
     */
    public function testDateValidWhenEmptyButNotRequired()
    {
        $v = new Validator(array('date' => ''));
        $v->rule('date', 'date');
        $this->assertTrue($v->validate());
    }

    public function testDateFormatValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateFormat', 'date', 'Y-m-d');
        $this->assertTrue($v->validate());
    }

    public function testDateFormatValidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '2018-10-13'));
        $v->rules(array(
            'dateFormat' => array(
                array('created_at', 'Y-m-d')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testDateFormatInvalid()
    {
        $v = new Validator(array('date' => 'no thanks'));
        $v->rule('dateFormat', 'date', 'Y-m-d');
        $this->assertFalse($v->validate());

        $v = new Validator(array('date' => '2013-27-01'));
        $v->rule('dateFormat', 'date', 'Y-m-d');
        $this->assertFalse($v->validate());
    }

    public function testDateFormatInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '10-13-2018'));
        $v->rules(array(
            'dateFormat' => array(
                array('created_at', 'Y-m-d')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testDateBeforeValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateBefore', 'date', new \DateTime('2013-01-28'));
        $this->assertTrue($v->validate());
    }

    public function testDateBeforeValidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '2018-09-01'));
        $v->rules(array(
            'dateBefore' => array(
                array('created_at', '2018-10-13')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testDateWarningsWithObjectParams()
    {
        $v = new Validator(array('startDate' => '2013-01-27', 'endDate' => '2013-05-08'));
        $v->rule(
            'date',
            array(
                'startDate',
                'endDate'
            )
        );

        $v->rule(
            'dateBefore',
            'endDate',
            new DateTime('2013-04-08')
        )->label('End date')->message('{field} must be before the end of the fiscal year, %s.');

        $v->rule(
            'dateAfter',
            'startDate',
            new DateTime('2013-02-17')
        )->label('Start date')->message('{field} must be after the beginning of the fiscal year, %s.');

        $this->assertFalse($v->validate());
    }

    public function testDateBeforeInvalid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateBefore', 'date', '2013-01-26');
        $this->assertFalse($v->validate());
    }

    public function testDateBeforeInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '2018-11-01'));
        $v->rules(array(
            'dateBefore' => array(
                array('created_at', '2018-10-13')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testDateAfterValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateAfter', 'date', new \DateTime('2013-01-26'));
        $this->assertTrue($v->validate());
    }

    public function testDateAfterValidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '2018-09-01'));
        $v->rules(array(
            'dateAfter' => array(
                array('created_at', '2018-01-01')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testDateAfterInvalid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateAfter', 'date', '2013-01-28');
        $this->assertFalse($v->validate());
    }

    public function testDateAfterInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('created_at' => '2017-09-01'));
        $v->rules(array(
            'dateAfter' => array(
                array('created_at', '2018-01-01')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testContainsValid()
    {
        $v = new Validator(array('test_string' => 'this is a Test'));
        $v->rule('contains', 'test_string', 'Test');
        $this->assertTrue($v->validate());
    }

    public function testContainsValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'Batman123'));
        $v->rules(array(
            'contains' => array(
                array('username', 'man'),
                array('username', 'man', true)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testContainsNonStrictValid()
    {
        $v = new Validator(array('test_string' => 'this is a Test'));
        $v->rule('contains', 'test_string', 'test', false);
        $this->assertTrue($v->validate());
    }

    public function testContainsInvalid()
    {
        $v = new Validator(array('test_string' => 'this is a test'));
        $v->rule('contains', 'test_string', 'foobar');
        $this->assertFalse($v->validate());
    }

    public function testContainsInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'Batman123'));
        $v->rules(array(
            'contains' => array(
                array('username', 'Man', true)
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testContainsStrictInvalid()
    {
        $v = new Validator(array('test_string' => 'this is a Test'));
        $v->rule('contains', 'test_string', 'test');
        $this->assertFalse($v->validate());
    }

    public function testContainsInvalidValue()
    {
        $v = new Validator(array('test_string' => false));
        $v->rule('contains', 'test_string', 'foobar');
        $this->assertFalse($v->validate());
    }

    public function testContainsInvalidRule()
    {
        $v = new Validator(array('test_string' => 'this is a test'));
        $v->rule('contains', 'test_string', null);
        $this->assertFalse($v->validate());
    }

    public function testSubsetValid()
    {
        // numeric values
        $v = new Validator(array('test_field' => array(81, 3, 15)));
        $v->rule('subset', 'test_field', array(45, 15, 3, 7, 28, 81));
        $this->assertTrue($v->validate());

        // string values
        $v = new Validator(array('test_field' => array('white', 'green', 'blue')));
        $v->rule('subset', 'test_field', array('green', 'orange', 'blue', 'yellow', 'white', 'brown'));
        $this->assertTrue($v->validate());

        // mixed values
        $v = new Validator(array('test_field' => array(81, false, 'orange')));
        $v->rule('subset', 'test_field', array(45, 'green', true, 'orange', null, 81, false));
        $this->assertTrue($v->validate());

        // string value and validation target cast to array
        $v = new Validator(array('test_field' => 'blue'));
        $v->rule('subset', 'test_field', 'blue');
        $this->assertTrue($v->validate());
    }

    public function testSubsetValidAltSyntax()
    {
        $v = new Valitron\Validator(array('colors' => array('green', 'blue')));
        $v->rules(array(
            'subset' => array(
                array('colors', array('orange', 'green', 'blue', 'red'))
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testSubsetInvalid()
    {
        $v = new Validator(array('test_field' => array(81, false, 'orange')));
        $v->rule('subset', 'test_field', array(45, 'green', true, 'orange', null, false, 7));
        $this->assertFalse($v->validate());
    }

    public function testSubsetInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('colors' => array('purple', 'blue')));
        $v->rules(array(
            'subset' => array(
                array('colors', array('orange', 'green', 'blue', 'red'))
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testSubsetInvalidValue()
    {
        $v = new Validator(array('test_field' => 'black 45'));
        $v->rule('subset', 'test_field', array('black', 45));
        $this->assertFalse($v->validate());
    }

    public function testSubsetInvalidRule()
    {
        // rule value has invalid type
        $v = new Validator(array('test_field' => array('black', 45)));
        $v->rule('subset', 'test_field', 'black 45');
        $this->assertFalse($v->validate());

        // rule value not specified
        $v = new Validator(array('test_field' => array('black', 45)));
        $v->rule('subset', 'test_field');
        $this->assertFalse($v->validate());
    }

    public function testContainsUniqueValid()
    {
        // numeric values
        $v = new Validator(array('test_field' => array(81, 3, 15)));
        $v->rule('containsUnique', 'test_field');
        $this->assertTrue($v->validate());

        // string values
        $v = new Validator(array('test_field' => array('white', 'green', 'blue')));
        $v->rule('containsUnique', 'test_field');
        $this->assertTrue($v->validate());

        // mixed values
        $v = new Validator(array('test_field' => array(81, false, 'orange')));
        $v->rule('containsUnique', 'test_field');
        $this->assertTrue($v->validate());
    }

    public function testContainsUniqueValidAltSyntax()
    {
        $v = new Valitron\Validator(array('colors' => array('purple', 'blue')));
        $v->rules(array(
            'containsUnique' => array(
                array('colors')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testContainsUniqueInvalid()
    {
        $v = new Validator(array('test_field' => array(81, false, 'orange', false)));
        $v->rule('containsUnique', 'test_field');
        $this->assertFalse($v->validate());
    }

    public function testContainsUniqueInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('colors' => array('purple', 'purple')));
        $v->rules(array(
            'containsUnique' => array(
                array('colors')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testContainsUniqueInvalidValue()
    {
        $v = new Validator(array('test_field' => 'lorem ipsum'));
        $v->rule('containsUnique', 'test_field');
        $this->assertFalse($v->validate());
    }

    public function testAcceptBulkRulesWithSingleParams()
    {
        $rules = array(
            'required' => 'nonexistent_field',
            'accepted' => 'foo',
            'integer' => 'foo'
        );

        $v1 = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v1->rules($rules);
        $v1->validate();

        $v2 = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v2->rule('required', 'nonexistent_field');
        $v2->rule('accepted', 'foo');
        $v2->rule('integer', 'foo');
        $v2->validate();

        $this->assertEquals($v1->errors(), $v2->errors());
    }

    public function testAcceptBulkRulesWithMultipleParams()
    {
        $rules = array(
            'required' => array(
                array(array('nonexistent_field', 'other_missing_field'))
            ),
            'equals' => array(
                array('foo', 'bar')
            ),
            'length' => array(
                array('foo', 5)
            )
        );

        $v1 = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v1->rules($rules);
        $v1->validate();

        $v2 = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v2->rule('required', array('nonexistent_field', 'other_missing_field'));
        $v2->rule('equals', 'foo', 'bar');
        $v2->rule('length', 'foo', 5);
        $v2->validate();

        $this->assertEquals($v1->errors(), $v2->errors());
    }

    public function testAcceptBulkRulesWithNestedRules()
    {
        $rules = array(
            'length' => array(
                array('foo', 5),
                array('bar', 5)
            )
        );

        $v1 = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v1->rules($rules);
        $v1->validate();

        $v2 = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v2->rule('length', 'foo', 5);
        $v2->rule('length', 'bar', 5);
        $v2->validate();

        $this->assertEquals($v1->errors(), $v2->errors());
    }

    public function testAcceptBulkRulesWithNestedRulesAndMultipleFields()
    {
        $rules = array(
            'length' => array(
                array(array('foo', 'bar'), 5),
                array('baz', 5)
            )
        );

        $v1 = new Validator(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'foo'));
        $v1->rules($rules);
        $v1->validate();

        $v2 = new Validator(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'foo'));
        $v2->rule('length', array('foo', 'bar'), 5);
        $v2->rule('length', 'baz', 5);
        $v2->validate();

        $this->assertEquals($v1->errors(), $v2->errors());
    }

    public function testAcceptBulkRulesWithMultipleArrayParams()
    {
        $rules = array(
            'in' => array(
                array(array('foo', 'bar'), array('x', 'y'))
            )
        );

        $v1 = new Validator(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'foo'));
        $v1->rules($rules);
        $v1->validate();

        $v2 = new Validator(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'foo'));
        $v2->rule('in', array('foo', 'bar'), array('x', 'y'));
        $v2->validate();

        $this->assertEquals($v1->errors(), $v2->errors());
    }

    public function testMalformedBulkRules()
    {
        $v = new Validator();
        $v->rules(
            array(
                'required' => array('foo', 'bar')
            )
        );

        $this->assertFalse($v->validate());
    }

    public function testCustomLabelInMessage()
    {
        $v = new Valitron\Validator(array());
        $v->rule('required', 'name')->message('{field} is required')->label('NAME!!!');
        $v->validate();
        $this->assertEquals(array('NAME!!! is required'), $v->errors('name'));
    }

    public function testCustomLabelArrayInMessage()
    {
        $v = new Valitron\Validator(array());
        $v->rule('required', array('name', 'email'))->message('{field} is required');
        $v->labels(array(
            'name' => 'Name',
            'email' => 'Email address'
        ));
        $v->validate();
        $this->assertEquals(array(
            'name' => array('Name is required'),
            'email' => array('Email address is required')
        ), $v->errors());
    }

    public function testCustomLabelArrayWithoutMessage()
    {
        $v = new Valitron\Validator(array(
            'password' => 'foo',
            'passwordConfirm' => 'bar'
        ));
        $v->rule('equals', 'password', 'passwordConfirm');
        $v->labels(array(
            'password' => 'Password',
            'passwordConfirm' => 'Password Confirm'
        ));
        $v->validate();
        $this->assertEquals(array(
            'password' => array("Password must be the same as 'Password Confirm'"),
        ), $v->errors());
    }

    /**
     * Custom rules and callbacks
     */

    public function testAddRuleClosure()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', function () {
            return true;
        });
        $v->rule('testRule', 'name');
        $this->assertTrue($v->validate());
    }

    public function testAddRuleClosureReturnsFalse()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', function () {
            return false;
        });
        $v->rule('testRule', 'name');
        $this->assertFalse($v->validate());
    }

    public function testAddRuleClosureWithFieldArray()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'foo@example.com'));
        $v->addRule('testRule', function () {
            return true;
        });
        $v->rule('testRule', array('name', 'email'));
        $this->assertTrue($v->validate());
    }

    public function testAddRuleClosureWithArrayAsExtraParameter()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', function () {
            return true;
        });
        $v->rule('testRule', 'name', array('foo', 'bar'));
        $this->assertTrue($v->validate());
    }

    public function testAddRuleCallback()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', 'sampleFunctionCallback');
        $v->rule('testRule', 'name');
        $this->assertTrue($v->validate());
    }

    public function sampleObjectCallback()
    {
        return true;
    }

    public function sampleObjectCallbackFalse()
    {
        return false;
    }

    public function testAddRuleCallbackArray()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', array($this, 'sampleObjectCallback'));
        $v->rule('testRule', 'name');
        $this->assertTrue($v->validate());
    }

    public function testAddRuleCallbackArrayWithArrayAsExtraParameter()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', array($this, 'sampleObjectCallback'));
        $v->rule('testRule', 'name', array('foo', 'bar'));
        $this->assertTrue($v->validate());
    }

    public function testAddRuleCallbackArrayWithArrayAsExtraParameterAndCustomMessage()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->addRule('testRule', array($this, 'sampleObjectCallbackFalse'));
        $v->rule('testRule', 'name', array('foo', 'bar'))->message('Invalid name selected.');
        $this->assertFalse($v->validate());
    }

    public function testAddRuleCallbackArrayWithArrayAsExtraParameterAndCustomMessageLabel()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->labels(array('name' => 'Name'));
        $v->addRule('testRule', array($this, 'sampleObjectCallbackFalse'));
        $v->rule('testRule', 'name', array('foo', 'bar'))->message('Invalid name selected.');
        $this->assertFalse($v->validate());
    }

    public function testBooleanValid()
    {
        $v = new Validator(array('test' => true));
        $v->rule('boolean', 'test');
        $this->assertTrue($v->validate());
    }

    public function testBooleanValidAltSyntax()
    {
        $v = new Valitron\Validator(array('remember_me' => true));
        $v->rules(array(
            'boolean' => array(
                array('remember_me')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testBooleanInvalid()
    {
        $v = new Validator(array('test' => 'true'));
        $v->rule('boolean', 'test');
        $this->assertFalse($v->validate());
    }

    public function testBooleanInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('remember_me' => 'lobster'));
        $v->rules(array(
            'boolean' => array(
                array('remember_me')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testZeroStillTriggersValidation()
    {
        $v = new Validator(array('test' => 0));
        $v->rule('min', 'test', 1);
        $this->assertFalse($v->validate());
    }

    public function testFalseStillTriggersValidation()
    {
        $v = new Validator(array('test' => false));
        $v->rule('min', 'test', 5);
        $this->assertFalse($v->validate());
    }

    public function testCreditCardValid()
    {
        $visa = array(4539511619543489, 4532949059629052, 4024007171194938, 4929646403373269, 4539135861690622);
        $mastercard = array(5162057048081965, 5382687859049349, 5484388880142230, 5464941521226434, 5473481232685965, 2223000048400011, 2223520043560014);
        $amex = array(371442067262027, 340743030537918, 345509167493596, 343665795576848, 346087552944316);
        $dinersclub = array(30363194756249, 30160097740704, 38186521192206, 38977384214552, 38563220301454);
        $discover = array(6011712400392605, 6011536340491809, 6011785775263015, 6011984124619056, 6011320958064251);

        foreach (compact('visa', 'mastercard', 'amex', 'dinersclub', 'discover') as $type => $numbers) {
            foreach ($numbers as $number) {
                $v = new Validator(array('test' => $number));
                $v->rule('creditCard', 'test');
                $this->assertTrue($v->validate());
                $v->rule('creditCard', 'test', array($type, 'mastercard', 'visa'));
                $this->assertTrue($v->validate());
                $v->rule('creditCard', 'test', $type);
                $this->assertTrue($v->validate());
                $v->rule('creditCard', 'test', $type, array($type, 'mastercard', 'visa'));
                $this->assertTrue($v->validate());
                unset($v);
            }
        }
    }

    public function testCreditCardInvalid()
    {
        $visa = array(3539511619543489, 3532949059629052, 3024007171194938, 3929646403373269, 3539135861690622);
        $mastercard = array(4162057048081965, 4382687859049349, 4484388880142230, 4464941521226434, 4473481232685965);
        $amex = array(271442067262027, 240743030537918, 245509167493596, 243665795576848, 246087552944316);
        $dinersclub = array(20363194756249, 20160097740704, 28186521192206, 28977384214552, 28563220301454);
        $discover = array(5011712400392605, 5011536340491809, 5011785775263015, 5011984124619056, 5011320958064251);

        foreach (compact('visa', 'mastercard', 'amex', 'dinersclub', 'discover') as $type => $numbers) {
            foreach ($numbers as $number) {
                $v = new Validator(array('test' => $number));
                $v->rule('creditCard', 'test');
                $this->assertFalse($v->validate());
                $v->rule('creditCard', 'test', array($type, 'mastercard', 'visa'));
                $this->assertFalse($v->validate());
                $v->rule('creditCard', 'test', $type);
                $this->assertFalse($v->validate());
                $v->rule('creditCard', 'test', $type, array($type, 'mastercard', 'visa'));
                $this->assertFalse($v->validate());
                $v->rule('creditCard', 'test', 'invalidCardName');
                $this->assertFalse($v->validate());
                $v->rule('creditCard', 'test', 'invalidCardName', array('invalidCardName', 'mastercard', 'visa'));
                $this->assertFalse($v->validate());
                unset($v);
            }
        }
    }

    public function testInstanceOfValidWithString()
    {
        $v = new Validator(array('attributeName' => new stdClass()));
        $v->rule('instanceOf', 'attributeName', 'stdClass');
        $this->assertTrue($v->validate());
    }

    public function testInstanceOfValidAltSyntax()
    {
        $v = new Valitron\Validator(array('date' => new \DateTime()));
        $existingDateObject = new \DateTime();
        $v->rules(array(
            'instanceOf' => array(
                array('date', 'DateTime'),
                array('date', $existingDateObject)
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testInstanceOfInvalidWithInstance()
    {
        $v = new Validator(array('attributeName' => new stdClass()));
        $v->rule('instanceOf', 'attributeName', new Validator(array()));
        $this->assertFalse($v->validate());
    }

    public function testInstanceOfInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('date' => new \DateTime()));
        $v->rules(array(
            'instanceOf' => array(
                array('date', 'stdClass')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testInstanceOfValidWithInstance()
    {
        $v = new Validator(array('attributeName' => new stdClass()));
        $v->rule('instanceOf', 'attributeName', new stdClass());
        $this->assertTrue($v->validate());
    }

    public function testInstanceOfErrorMessageShowsInstanceName()
    {
        $v = new Validator(array('attributeName' => new Validator(array())));
        $v->rule('instanceOf', 'attributeName', new stdClass());
        $v->validate();
        $expected_error = array(
            "attributeName" => array(
                "AttributeName must be an instance of 'stdClass'"
            )
        );
        $this->assertEquals($expected_error, $v->errors());
    }

    public function testInstanceOfInvalidWithString()
    {
        $v = new Validator(array('attributeName' => new stdClass()));
        $v->rule('instanceOf', 'attributeName', 'SomeOtherClass');
        $this->assertFalse($v->validate());
    }

    public function testInstanceOfWithAlternativeSyntaxValid()
    {
        $v = new Validator(array('attributeName' => new stdClass()));
        $v->rules(array(
            'instanceOf' => array(
                array('attributeName', 'stdClass')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testInstanceOfWithAlternativeSyntaxInvalid()
    {
        $v = new Validator(array('attributeName' => new stdClass()));
        $v->rules(array(
            'instanceOf' => array(
                array('attributeName', 'SomeOtherClassInAlternativeSyntaxInvalid')
            )
        ));
        $v->validate();
        $this->assertFalse($v->validate());
    }

    /**
     * @dataProvider dataProviderFor_testError
     */
    public function testError($expected, $input, $test, $message)
    {
        $v = new Validator(array('test' => $input));
        $v->error('test', $message, $test);

        $this->assertEquals(array('test' => array($expected)), $v->errors());
    }

    public function dataProviderFor_testError()
    {
        return array(
            array(
                'expected' => 'Test must be at least 140 long',
                'input' => 'tweeet',
                'test' => array(140),
                'message' => '{field} must be at least %d long',
            ),
            array(
                'expected' => 'Test must be between 1 and 140 characters',
                'input' => array(1, 2, 3),
                'test' => array(1, 140),
                'message' => 'Test must be between %d and %d characters',
            ),
        );
    }

    public function testOptionalProvidedValid()
    {
        $v = new Validator(array('address' => 'user@example.com'));
        $v->rule('optional', 'address')->rule('email', 'address');
        $this->assertTrue($v->validate());
    }

    public function testOptionalProvidedValidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'batman'));
        $v->rules(array(
            'alpha' => array(
                array('username')
            ),
            'optional' => array(
                array('username')
            )
        ));
        $this->assertTrue($v->validate());
    }

    public function testOptionalProvidedInvalid()
    {
        $v = new Validator(array('address' => 'userexample.com'));
        $v->rule('optional', 'address')->rule('email', 'address');
        $this->assertFalse($v->validate());
    }

    public function testChainingRules()
    {
        $v = new Valitron\Validator(array('email_address' => 'test@test.com'));
        $v->rule('required', 'email_address')->rule('email', 'email_address');
        $this->assertTrue($v->validate());
    }

    public function testNestedDotNotation()
    {
        $v = new Valitron\Validator(array('user' => array('first_name' => 'Steve', 'last_name' => 'Smith', 'username' => 'Batman123')));
        $v->rule('alpha', 'user.first_name')->rule('alpha', 'user.last_name')->rule('alphaNum', 'user.username');
        $this->assertTrue($v->validate());
    }

    public function testOptionalProvidedInvalidAltSyntax()
    {
        $v = new Valitron\Validator(array('username' => 'batman123'));
        $v->rules(array(
            'alpha' => array(
                array('username')
            ),
            'optional' => array(
                array('username')
            )
        ));
        $this->assertFalse($v->validate());
    }

    public function testOptionalNotProvided()
    {
        $v = new Validator(array());
        $v->rule('optional', 'address')->rule('email', 'address');
        $this->assertTrue($v->validate());
    }

    public function testWithData()
    {
        $v = new Validator(array());
        $v->rule('required', 'name');
        //validation failed, so must have errors
        $this->assertFalse($v->validate());
        $this->assertNotEmpty($v->errors());

        //create copy with valid data
        $v2 = $v->withData(array('name' => 'Chester Tester'));
        $this->assertTrue($v2->validate());
        $this->assertEmpty($v2->errors());

        //create copy with invalid data
        $v3 = $v->withData(array('firstname' => 'Chester'));
        $this->assertFalse($v3->validate());
        $this->assertNotEmpty($v3->errors());
    }

    public function testRequiredEdgeCases()
    {
        $v = new Validator(array(
            'zero' => 0,
            'zero_txt' => '0',
            'false' => false,
            'empty_array' => array()
        ));
        $v->rule('required', array('zero', 'zero_txt', 'false', 'empty_array'));

        $this->assertTrue($v->validate());
    }

    public function testRequiredAllowEmpty()
    {
        $data = array(
            'empty_text' => '',
            'null_value' => null,
            'in_array' => array(
                'empty_text' => ''
            )
        );

        $v1 = new Validator($data);
        $v1->rule('required', array('empty_text', 'null_value', 'in_array.empty_text'));
        $this->assertFalse($v1->validate());

        $v2 = new Validator($data);
        $v2->rule('required', array('empty_text', 'null_value', 'in_array.empty_text'), true);
        $this->assertTrue($v2->validate());
    }

    public function testNestedEqualsValid()
    {
        $v = new Validator(array('foo' => array('one' => 'bar', 'two' => 'bar')));
        $v->rule('equals', 'foo.one', 'foo.two');
        $this->assertTrue($v->validate());
    }

    public function testNestedEqualsInvalid()
    {
        $v = new Validator(array('foo' => array('one' => 'bar', 'two' => 'baz')));
        $v->rule('equals', 'foo.one', 'foo.two');
        $this->assertFalse($v->validate());
    }

    public function testNestedEqualsBothNull()
    {
        $v = new Validator(array('foo' => array('bar' => null, 'baz' => null)));
        $v->rule('equals', 'foo.bar', 'foo.baz');
        $this->assertTrue($v->validate());
    }

    public function testNestedEqualsBothNullRequired()
    {
        $v = new Validator(array('foo' => array('bar' => null, 'baz' => null)));
        $v->rule('required', array('foo.bar', 'foo.baz'));
        $v->rule('equals', 'foo.bar', 'foo.baz');
        $this->assertFalse($v->validate());
    }

    public function testNestedEqualsBothUnset()
    {
        $v = new Validator(array('foo' => 'bar'));
        $v->rule('equals', 'foo.one', 'foo.two');
        $this->assertTrue($v->validate());
    }

    public function testNestedEqualsBothUnsetRequired()
    {
        $v = new Validator(array('foo' => 'bar'));
        $v->rule('required', array('foo.one', 'foo.two'));
        $v->rule('equals', 'foo.one', 'foo.two');
        $this->assertFalse($v->validate());
    }

    public function testNestedDifferentValid()
    {
        $v = new Validator(array('foo' => array('one' => 'bar', 'two' => 'baz')));
        $v->rule('different', 'foo.one', 'foo.two');
        $this->assertTrue($v->validate());
    }

    public function testNestedDifferentInvalid()
    {
        $v = new Validator(array('foo' => array('one' => 'baz', 'two' => 'baz')));
        $v->rule('different', 'foo.one', 'foo.two');
        $this->assertFalse($v->validate());
    }

    public function testNestedDifferentBothNull()
    {
        $v = new Validator(array('foo' => array('bar' => null, 'baz' => null)));
        $v->rule('different', 'foo.bar', 'foo.baz');
        $this->assertTrue($v->validate());
    }

    public function testNestedDifferentBothNullRequired()
    {
        $v = new Validator(array('foo' => array('bar' => null, 'baz' => null)));
        $v->rule('required', array('foo.bar', 'foo.baz'));
        $v->rule('different', 'foo.bar', 'foo.baz');
        $this->assertFalse($v->validate());
    }

    public function testNestedDifferentBothUnset()
    {
        $v = new Validator(array('foo' => 'bar'));
        $v->rule('different', 'foo.bar', 'foo.baz');
        $this->assertTrue($v->validate());
    }

    public function testNestedDifferentBothUnsetRequired()
    {
        $v = new Validator(array('foo' => 'bar'));
        $v->rule('required', array('foo.bar', 'foo.baz'));
        $v->rule('different', 'foo.bar', 'foo.baz');
        $this->assertFalse($v->validate());
    }

    /**
     * @see https://github.com/vlucas/valitron/issues/262
     */
    public function testOptionalArrayPartsAreIgnored()
    {
        $v = new Validator(array(
                'data' => array(
                    array('foo' => '2018-01-01'),
                    array('bar' => 1)
                )
            )
        );
        $v->rule('date', 'data.*.foo');
        $this->assertTrue($v->validate());
    }

    /**
     * @see https://github.com/vlucas/valitron/issues/262
     */
    public function testRequiredArrayPartsAreNotIgnored()
    {
        $v = new Validator(array(
                'data' => array(
                    array('foo' => '2018-01-01'),
                    array('bar' => 1)
                )
            )
        );
        $v->rule('required', 'data.*.foo');
        $v->rule('date', 'data.*.foo');
        $this->assertFalse($v->validate());
    }

    public function testArrayHasKeysTrueIfAllFieldsExist()
    {
        $v = new Validator(array(
            'address' => array(
                'name' => 'Jane Doe',
                'street' => 'Doe Square',
                'city' => 'Doe D.C.'
            )
        ));
        $v->rule('arrayHasKeys', 'address', array('name', 'street', 'city'));
        $this->assertTrue($v->validate());
    }

    public function testArrayHasKeysFalseOnMissingField()
    {
        $v = new Validator(array(
            'address' => array(
                'name' => 'Jane Doe',
                'street' => 'Doe Square'
            )
        ));
        $v->rule('arrayHasKeys', 'address', array('name', 'street', 'city'));
        $this->assertFalse($v->validate());
    }

    public function testArrayHasKeysFalseOnNonArray()
    {
        $v = new Validator(array(
            'address' => 'Jane Doe, Doe Square'
        ));
        $v->rule('arrayHasKeys', 'address', array('name', 'street', 'city'));
        $this->assertFalse($v->validate());
    }

    public function testArrayHasKeysFalseOnEmptyRequiredFields()
    {
        $v = new Validator(array(
            'address' => array(
                'lat' => 77.547,
                'lon' => 16.337
            )
        ));
        $v->rule('arrayHasKeys', 'address', array());
        $this->assertFalse($v->validate());
    }

    public function testArrayHasKeysFalseOnUnspecifiedRequiredFields()
    {
        $v = new Validator(array(
            'address' => array(
                'lat' => 77.547,
                'lon' => 16.337
            )
        ));
        $v->rule('arrayHasKeys', 'address');
        $this->assertFalse($v->validate());
    }

    public function testArrayHasKeysTrueIfMissingAndOptional()
    {
        $v = new Validator(array());
        $v->rule('arrayHasKeys', 'address', array('name', 'street', 'city'));
        $v->rule('optional', 'address');
        $this->assertTrue($v->validate());
    }
}

function sampleFunctionCallback($field, $value, array $params)
{
    return true;
}
