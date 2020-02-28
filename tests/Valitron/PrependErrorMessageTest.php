<?php

namespace Valitron\Validator;

use BaseTestCase;
use Valitron\Validator;

final class PrependErrorMessageTest extends BaseTestCase
{
    /**
     * Default behavior, fieldname is always in error message.
     */
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

    /**
     * Remove fieldname in error message.
     */
    public function testWithoutPrependErrorMessage()
    {
        $v = new Validator(array('test' => 'test value', 'nested' => array('custom' => 'nested.custom value')));
        Validator::addRule('custom', function() { return false; }, 'Validation failed');
        Validator::prependErrorMessages(false);
        $v->rule('custom', 'test');
        $v->rule('custom', 'nested.custom');
        $v->rule('required', 'foo');
        $v->validate();
        $messages = $v->errors();
        $this->assertNotContains('Test Validation failed', $messages['test']);
        $this->assertContains('Validation failed', $messages['test']);
        $this->assertContains('Validation failed', $messages['nested.custom']);
        $this->assertContains('is required', $messages['foo']);
    }
}