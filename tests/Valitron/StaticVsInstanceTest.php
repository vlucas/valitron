<?php
use Valitron\Validator;

class StaticVsInstanceTest extends BaseTestCase
{
	public function testInstanceOverrideStatic()
	{
		Validator::lang('ar');
		new Validator(array(), array(), 'en');
		$this->assertEquals('ar', Validator::lang(),
							'lang defined statically should not be override by instance lang');
	}
}
