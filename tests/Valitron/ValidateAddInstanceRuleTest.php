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
}
