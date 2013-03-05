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

    public function testAccurateErrorCount()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->rule('required', 'name');
        $this->assertSame(1, count($v->errors('name')));
    }

    public function testAccurateErrorMessage()
    {
        $v = new Validator(array());
        $v->rule('required', 'name');
        $v->validate();
        $this->assertSame(array("Required"), $v->errors('name'));
    }

    public function testAccurateErrorMessageParams()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 6);
        $v->validate();
        $this->assertSame(array("Must be greater than 6"), $v->errors('num'));
    }

    public function testCustomErrorMessage()
    {
        $v = new Validator(array());
        $v->rule('required', 'name')->message('Name is required');
        $v->validate();
        $errors = $v->errors('name');
        $this->assertSame('Name is required', $errors[0]);
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

    public function testRequiredValid()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->rule('required', 'name');
        $this->assertTrue($v->validate());
    }

    public function testRequiredNonExistentField()
    {
        $v = new Validator(array('name' => 'Chester Tester'));
        $v->rule('required', 'nonexistent_field');
        $this->assertFalse($v->validate());
    }

    public function testEqualsValid()
    {
        $v = new Validator(array('foo' => 'bar', 'bar' => 'bar'));
        $v->rule('equals', 'foo', 'bar');
        $this->assertTrue($v->validate());
    }

    public function testEqualsInvalid()
    {
        $v = new Validator(array('foo' => 'foo', 'bar' => 'bar'));
        $v->rule('equals', 'foo', 'bar');
        $this->assertFalse($v->validate());
    }

    public function testDifferentValid()
    {
        $v = new Validator(array('foo' => 'bar', 'bar' => 'baz'));
        $v->rule('different', 'foo', 'bar');
        $this->assertTrue($v->validate());
    }

    public function testDifferentInvalid()
    {
        $v = new Validator(array('test1' => 'test', 'test2' => 'test'));
        $v->rule('different', 'foo', 'bar');
        $this->assertFalse($v->validate());
    }

    public function testAcceptedValid()
    {
        $v = new Validator(array('agree' => 'yes'));
        $v->rule('accepted', 'agree');
        $this->assertTrue($v->validate());
    }

    public function testAcceptedInvalid()
    {
        $v = new Validator(array('agree' => 'no'));
        $v->rule('accepted', 'agree');
        $this->assertFalse($v->validate());
    }

    public function testNumericValid()
    {
        $v = new Validator(array('num' => '42.341569'));
        $v->rule('numeric', 'num');
        $this->assertTrue($v->validate());
    }

    public function testNumericInvalid()
    {
        $v = new Validator(array('num' => 'nope'));
        $v->rule('numeric', 'num');
        $this->assertFalse($v->validate());
    }

    public function testIntegerValid()
    {
        $v = new Validator(array('num' => '41243'));
        $v->rule('integer', 'num');
        $this->assertTrue($v->validate());
    }

    public function testIntegerInvalid()
    {
        $v = new Validator(array('num' => '42.341569'));
        $v->rule('integer', 'num');
        $this->assertFalse($v->validate());
    }

    public function testLengthValid()
    {
        $v = new Validator(array('str' => 'happy'));
        $v->rule('length', 'str', 5);
        $this->assertTrue($v->validate());
    }

    public function testLengthInvalid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('length', 'str', 6);
        $this->assertFalse($v->validate());
    }

    public function testLengthBetweenValid()
    {
        $v = new Validator(array('str' => 'happy'));
        $v->rule('length', 'str', 2, 8);
        $this->assertTrue($v->validate());
    }

    public function testLengthBetweenInvalid()
    {
        $v = new Validator(array('str' => 'sad'));
        $v->rule('length', 'str', 4, 10);
        $this->assertFalse($v->validate());
    }

    public function testMinValid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 2);
        $this->assertTrue($v->validate());
    }

    public function testMinInvalid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('min', 'num', 6);
        $this->assertFalse($v->validate());
    }

    public function testMaxValid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('max', 'num', 6);
        $this->assertTrue($v->validate());
    }

    public function testMaxInvalid()
    {
        $v = new Validator(array('num' => 5));
        $v->rule('max', 'num', 4);
        $this->assertFalse($v->validate());
    }

    public function testInValid()
    {
        $v = new Validator(array('color' => 'green'));
        $v->rule('in', 'color', array('red', 'green', 'blue'));
        $this->assertTrue($v->validate());
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

    public function testNotInInvalid()
    {
        $v = new Validator(array('color' => 'blue'));
        $v->rule('notIn', 'color', array('red', 'green', 'blue'));
        $this->assertFalse($v->validate());
    }

    public function testIpValid()
    {
        $v = new Validator(array('ip' => '127.0.0.1'));
        $v->rule('ip', 'ip');
        $this->assertTrue($v->validate());
    }

    public function testIpInvalid()
    {
        $v = new Validator(array('ip' => 'buy viagra now!'));
        $v->rule('ip', 'ip');
        $this->assertFalse($v->validate());
    }

    public function testEmailValid()
    {
        $v = new Validator(array('name' => 'Chester Tester', 'email' => 'chester@tester.com'));
        $v->rule('email', 'email');
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

    public function testUrlActive()
    {
        $v = new Validator(array('website' => 'http://google.com'));
        $v->rule('urlActive', 'website');
        $this->assertTrue($v->validate());
    }

    public function testUrlInactive()
    {
        $v = new Validator(array('website' => 'http://sonotgoogleitsnotevenfunny.dev'));
        $v->rule('urlActive', 'website');
        $this->assertFalse($v->validate());
    }

    public function testAlphaValid()
    {
        $v = new Validator(array('test' => 'abcDEF'));
        $v->rule('alpha', 'test');
        $this->assertTrue($v->validate());
    }

    public function testAlphaInvalid()
    {
        $v = new Validator(array('test' => 'abc123'));
        $v->rule('alpha', 'test');
        $this->assertFalse($v->validate());
    }

    public function testAlphaNumValid()
    {
        $v = new Validator(array('test' => 'abc123'));
        $v->rule('alphaNum', 'test');
        $this->assertTrue($v->validate());
    }

    public function testAlphaNumInvalid()
    {
        $v = new Validator(array('test' => 'abc123$%^'));
        $v->rule('alphaNum', 'test');
        $this->assertFalse($v->validate());
    }

    public function testAlphaDashValid()
    {
        $v = new Validator(array('test' => 'abc-123_DEF'));
        $v->rule('slug', 'test');
        $this->assertTrue($v->validate());
    }

    public function testAlphaDashInvalid()
    {
        $v = new Validator(array('test' => 'abc-123_DEF $%^'));
        $v->rule('slug', 'test');
        $this->assertFalse($v->validate());
    }

    public function testRegexValid()
    {
        $v = new Validator(array('test' => '42'));
        $v->rule('regex', 'test', '/[\d]+/');
        $this->assertTrue($v->validate());
    }

    public function testRegexInvalid()
    {
        $v = new Validator(array('test' => 'istheanswer'));
        $v->rule('regex', 'test', '/[\d]+/');
        $this->assertFalse($v->validate());
    }

    public function testDateValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('date', 'date');
        $this->assertTrue($v->validate());
    }

    public function testDateInvalid()
    {
        $v = new Validator(array('date' => 'no thanks'));
        $v->rule('date', 'date');
        $this->assertFalse($v->validate());
    }

    public function testDateFormatValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateFormat', 'date', 'Y-m-d');
        $this->assertTrue($v->validate());
    }

    public function testDateFormatInvalid()
    {
        $v = new Validator(array('date' => 'no thanks'));
        $v->rule('dateFormat', 'date', 'Y-m-d');
        $this->assertFalse($v->validate());
    }

    public function testDateBeforeValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateBefore', 'date', new \DateTime('2013-01-28'));
        $this->assertTrue($v->validate());
    }

    public function testDateBeforeInvalid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateBefore', 'date', '2013-01-26');
        $this->assertFalse($v->validate());
    }

    public function testDateAfterValid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateAfter', 'date', new \DateTime('2013-01-26'));
        $this->assertTrue($v->validate());
    }

    public function testDateAfterInvalid()
    {
        $v = new Validator(array('date' => '2013-01-27'));
        $v->rule('dateAfter', 'date', '2013-01-28');
        $this->assertFalse($v->validate());
    }

    public function testContainsValid()
    {
        $v = new Validator(array('test_string' => 'this is a test'));
        $v->rule('contains', 'test_string', 'a test');
        $this->assertTrue($v->validate());
    }

    public function testContainsNotFound()
    {
        $v = new Validator(array('test_string' => 'this is a test'));
        $v->rule('contains', 'test_string', 'foobar');
        $this->assertFalse($v->validate());
    }

    public function testContainsInvalidValue()
    {
        $v = new Validator(array('test_string' => 'this is a test'));
        $v->rule('contains', 'test_string', array('test'));
        $this->assertFalse($v->validate());
    }
}

