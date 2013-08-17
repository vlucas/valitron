<?php
use Valitron\Validator;

class StaticLangTest extends BaseTestCase
{
	protected function getLangDir()
	{
		return __DIR__.'/../../lang';
	}

	/**
	 * Lang defined statically should not be overrided by constructor default
	 */
    public function testLangDefinedStatically()
    {
    	$lang = 'ar';
    	Validator::lang($lang);
    	$validator = new Validator(array());
    	$this->assertEquals($lang, Validator::lang());
	}

	/**
	 * LangDir defined statically should not be overrided by constructor default
	 */
    public function testLangDirDefinedStatically()
    {
    	$langDir = $this->getLangDir();
    	Validator::langDir($langDir);
    	$validator = new Validator(array());
    	$this->assertEquals($langDir, Validator::langDir());
	}

	public function testDefaultLangShouldBeEn()
	{
		$validator = new Validator(array());
		$this->assertEquals('en', Validator::lang());
	}

	public function testDefaultLangDirShouldBePackageLangDir()
	{
		$validator = new Validator(array());
		$this->assertEquals(realpath($this->getLangDir()), realpath(Validator::langDir()));
	}
}
