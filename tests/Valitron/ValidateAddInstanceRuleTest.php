<?php
use Valitron\Validator;

class ValidateAddInstanceRuleTest extends BaseTestCase
{
    protected function assertValid($v)
    {
        $msg = "\tErrors:\n";
        $status = $v->validate();
        foreach ($v->errors() as $label => $messages)
        {
            foreach ($messages as $theMessage)
            {
                $msg .= "\n\t{$label}: {$theMessage}";
            }
        }

        $this->assertTrue($v->validate(), $msg);
    }

    public function testAddInstanceRule()
    {
        $v = new Validator(array(
            "foo" => "bar",
            "fuzz" => "bazz",
        ));

        $v->addInstanceRule("fooRule", function($field, $value)
        {
            return $field !== "foo" || $value !== "barz";
        });

        Validator::addRule("fuzzerRule", function($field, $value)
        {
            return $field !== "fuzz" || $value === "bazz";
        });

        $v->rule("required", array("foo", "fuzz"));
        $v->rule("fuzzerRule", "fuzz");
        $v->rule("fooRule", "foo");

        $this->assertValid($v);
    }

    public function testAddInstanceRuleFail()
    {
        $v = new Validator(array("foo" => "bar"));
        $v->addInstanceRule("fooRule", function($field)
        {
            return $field === "for";
        });
        $v->rule("fooRule", "foo");
        $this->assertFalse($v->validate());
    }

    public function testAddAddRuleWithCallback()
    {
        $v = new Validator(array("foo" => "bar"));
        $v->rule(function($field, $value) {
            return $field === "foo" && $value === "bar";
        }, "foo");

        $this->assertValid($v);
    }

    public function testAddAddRuleWithCallbackFail()
    {
        $v = new Validator(array("foo" => "baz"));
        $v->rule(function($field, $value) {
            return $field === "foo" && $value === "bar";
        }, "foo");

        $this->assertFalse($v->validate());
    }

    public function testAddAddRuleWithCallbackFailMessage()
    {
        $v = new Validator(array("foo" => "baz"));
        $v->rule(function($field, $value) {
            return $field === "foo" && $value === "bar";
        }, "foo", "test error message");

        $this->assertFalse($v->validate());
    $errors = $v->errors();
        $this->assertArrayHasKey("foo", $errors);
        $this->assertCount(1, $errors["foo"]);
        $this->assertEquals("Foo test error message", $errors["foo"][0]);
    }

    public function testUniqueRuleName()
    {
        $v = new Validator(array());
        $args = array("foo", "bar");
        $this->assertEquals("foo_bar_rule", $v->getUniqueRuleName($args));
        $this->assertEquals("foo_rule", $v->getUniqueRuleName("foo"));

        $v->addInstanceRule("foo_rule", function() {});
        $u = $v->getUniqueRuleName("foo");
        $this->assertRegExp("/^foo_rule_[0-9]{1,5}$/", $u);
    }
}
