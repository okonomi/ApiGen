<?php

namespace ApiGen\Templating\Tests;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Contracts\Templating\TemplateFileManagerInterface;
use ApiGen\Contracts\Theme\Configuration\ThemeConfigurationInterface;
use ApiGen\Templating\TemplateElementsLoader;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;
use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;


class TemplateFactoryTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;


	protected function setUp()
	{
		$latteEngineMock = Mockery::mock(Engine::class);

		$configurationMock = Mockery::mock(Configuration::class);

		$configurationReflection = new \ReflectionClass(Configuration::class);
		foreach ($configurationReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflection) {
			$configurationMock->shouldReceive($methodReflection->getName())->andReturn('');
		}

		$templateElementsLoaderMock = Mockery::mock(TemplateElementsLoader::class);
		$templateElementsLoaderMock->shouldReceive('addElementsToTemplate')->andReturnUsing(function ($args) {
			return $args;
		});


		$routerMock = Mockery::mock(RouterInterface::class);

		$this->templateFactory = new TemplateFactory(
			$latteEngineMock, $configurationMock, $this->getTemplateFileManagerMock(),
			$routerMock,
			$templateElementsLoaderMock
		);


		$themeConfiguration = Mockery::mock(ThemeConfigurationInterface::class, [
			'getTemplatesPath' => '...',
			'shouldElementDetailsCollapse' => TRUE
		]);
		$this->templateFactory->setThemeConfiguration($themeConfiguration);
	}


	public function testCreate()
	{
		$this->assertInstanceOf(TemplateInterface::class, $this->templateFactory->create());
	}


	public function testBuildTemplateCache()
	{
		$template = MethodInvoker::callMethodOnObject($this->templateFactory, 'buildTemplate');
		$template2 = MethodInvoker::callMethodOnObject($this->templateFactory, 'buildTemplate');
		$this->assertSame($template, $template2);
	}


	/**
	 * @return MockInterface|TemplateFileManagerInterface
	 */
	private function getTemplateFileManagerMock()
	{
		$templateNavigatorMock = Mockery::mock(TemplateFileManagerInterface::class, [
			'getTemplatePath' => function ($arg) {
				return $arg . '-template-path.latte';
			},
			'getTemplateFileName' =>'...',
			'getTemplatePathForClass' => '',
			'getTemplatePathForConstant' => '',
			'getTemplatePathForFunction' => '',
			'getTemplatePathForSourceElement' => '',
			'getTemplatePathForNamespace' => '',
			'getTemplatePathForPackage' => ''
		]);
		return $templateNavigatorMock;
	}

}
