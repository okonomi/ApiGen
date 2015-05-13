<?php

namespace ApiGen\Tests\DI;

use ApiGen\Contracts\VersionInterface;
use ApiGen\DI\ApiGenExtension;
use ApiGen\Generator\TemplateGenerators\ClassElementGenerator;
use ApiGen\Version;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;


class ApiGenExtensionTest extends PHPUnit_Framework_TestCase
{

	public function testLoadConfiguration()
	{
		$extension = $this->getExtension();
		$extension->loadConfiguration();

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		$versionDefinition = $builder->getDefinition($builder->getByType(VersionInterface::class));
		$this->assertSame(Version::class, $versionDefinition->getClass());
	}


	/**
	 * @return ApiGenExtension
	 */
	private function getExtension()
	{
		$extension = new ApiGenExtension;
		$extension->setCompiler($this->getCompiler(), 'compiler');
		return $extension;
	}


	/**
	 * @return Compiler
	 */
	private function getCompiler()
	{
		$compiler = new Compiler(new ContainerBuilder);
		$compiler->compile(['parameters' => [
			'rootDir' => __DIR__ . '/..',
			'tempDir' => __DIR__ . '/../temp'
		]], NULL, NULL);
		return $compiler;
	}

}
