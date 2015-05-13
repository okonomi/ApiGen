<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Templating\Template;
use Latte\Engine;
use PHPUnit_Framework_TestCase;


class TemplateTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var Template
	 */
	private $template;


	protected function setUp()
	{
		$this->template = new Template(new Engine);
	}


	public function testFileIsSavedWithContent()
	{
		$this->template->setFile(__DIR__ . '/TemplateSource/template.latte');
		$this->template->addParameters(['name' => 'World!']);
		$this->template->setSavePath(TEMP_DIR . '/dir/hello-world.html');
		$this->template->save();
		$this->assertFileExists(TEMP_DIR . '/dir/hello-world.html');
		$generatedContent = file_get_contents(TEMP_DIR . '/dir/hello-world.html');
		$this->assertSame('Hello World!', trim($generatedContent));
	}

}
