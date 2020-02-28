<?php

namespace Valitron\Validator;

use BaseTestCase;
use Valitron\Validator;

final class PrependErrorMessageTest extends BaseTestCase
{
    public function testWithPrependErrorMessage()
    {
        $v = new Validator(array('test' => 'test value'));
        Validator::addRule('custom', function() { return false; }, 'Validation failed');

        $v->rule('custom', 'test');
        $v->rule('required', 'foo');
        $v->validate();
        $messages = $v->errors();
        $this->assertContains('Test Validation failed', $messages['test']);
        $this->assertContains('Foo is required', $messages['foo']);
    }

    public function testWithoutPrependErrorMessage()
    {
        $v = new Validator(array('test' => 'test value'));
        Validator::addRule('custom', function() { return false; }, 'Validation failed');
        Validator::prependErrorMessages(false);
        $v->rule('custom', 'test');
        $v->rule('required', 'foo');
        $v->validate();
        $messages = $v->errors();
        $this->assertNotContains('Test Validation failed', $messages['test']);
        $this->assertContains('Validation failed', $messages['test']);
        $this->assertContains('is required', $messages['foo']);
    }
}