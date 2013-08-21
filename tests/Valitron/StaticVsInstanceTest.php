<?php
use Valitron\Validator;

class StaticVsInstanceTest extends BaseTestCase
{
	public function testInstanceOverrideStaticLang()
	{
		Validator::lang('ar');
		new Validator(array(), array(), 'en');
		$this->assertEquals('ar', Validator::lang(),
							'instance defined lang should not replace static global lang');
	}

	/**
	 * Rules messages added with Validator::addRule are replaced after creating validator instance
	 */
	public function testRuleMessagesReplacedAfterConstructor()
	{
		$customMessage = 'custom message';
		$ruleName = 'foo';
		Validator::addRule($ruleName, function() {}, $customMessage);

		$prop = new ReflectionProperty('Valitron\Validator', '_ruleMessages');
		$prop->setAccessible(true);
		$messages = $prop->getValue();

		$this->assertEquals($customMessage, $messages[$ruleName]);

		new Validator(array(), array());

		$messages = $prop->getValue();
		$this->assertArrayHasKey($ruleName, $messages);
		$this->assertEquals($customMessage, $messages[$ruleName]);
	}
}
