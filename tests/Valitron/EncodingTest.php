<?php
use Valitron\Validator;

class EncodingTest extends BaseTestCase
{
    public function testDefaultEncoding(){
        $v = new Validator(array());
        $this->assertEquals('UTF-8', $v->getEncoding());

        Validator::setDefaultEncoding('ASCII');
        $this->assertEquals('ASCII', $v->getEncoding());
        //reset encoding for further tests
        Validator::setDefaultEncoding('UTF-8');
    }

    public function testOwnEncoding(){
        $v = new Validator(array());

        $v->setEncoding('ASCII');
        $this->assertEquals('ASCII', $v->getEncoding());

        //reset to default
        $v->setEncoding();
        $this->assertEquals('UTF-8', $v->getEncoding());

    }

}