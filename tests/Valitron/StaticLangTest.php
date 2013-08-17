<?php
use Valitron\Validator;

class StaticLangTest extends \PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		$this->resetProperty('_lang');
		$this->resetProperty('_langDir');
		$this->resetProperty('_ruleMessages', array());
	}

	protected function resetProperty($name, $value = null)
	{
		$prop = new ReflectionProperty('Valitron\Validator', $name);
		$prop->setAccessible(true);
		$prop->setValue($value);
		$prop->setAccessible(false);
	}

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
