<?php
use Valitron\Validator;

class LangTest extends BaseTestCase
{
	protected function getLangDir()
	{
		return __DIR__.'/../../lang';
	}

	/**
	 * Lang defined statically should not be override by constructor default
	 */
    public function testLangDefinedStatically()
    {
    	$lang = 'ar';
    	Validator::lang($lang);
    	$this->assertEquals($lang, Validator::lang());
        Validator::lang('en');
	}

	/**
	 * LangDir defined statically should not be override by constructor default
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


	public function testLangException()
	{
	    $this->expectException("InvalidArgumentException");
        $this->expectExceptionMessage("Fail to load language file '/this/dir/does/not/exists/en.php'");
		new Validator(array(), array(), 'en', '/this/dir/does/not/exists');
	}


	public function testLoadingNorwegianLoadsNNVariant(){
	    $validator = new Validator(array(), array(),'no', $this->getLangDir());
	    $validator->rule('required','test');
	    $validator->validate();
	    $errors =$validator->errors('test');
	    $this->assertEquals('Test er nødvendig', $errors[0]);
    }
}
