<?php

use Valitron\Validator;

class StopOnFirstFailTest extends BaseTestCase
{
    public function testStopOnFirstFail()
    {
        $rules = array(
            'myField1' => array(
                array('lengthMin', 5, 'message' => 'myField1 must be 5 characters minimum'),
                array('url', 'message' => 'myField1 is not a valid url'),
                array('urlActive', 'message' => 'myField1 is not an active url')
            )
        );

        $v = new Validator(array(
            'myField1' => 'myVal'
        ));

        $v->mapFieldsRules($rules);
        $v->stopOnFirstFail(true);
        $this->assertFalse($v->validate());

        $errors = $v->errors();
        $this->assertCount(1, $errors['myField1']);
    }
}
