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
}
