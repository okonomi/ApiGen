<?php

namespace ApiGen\Routing\Tests;

use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\DI\RoutingExtension;
use ApiGen\Routing\Router;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;


class RoutingExtensionTest extends PHPUnit_Framework_TestCase
{

	public function testLoadConfiguration()
	{
		$extension = $this->getExtension();
		$extension->loadConfiguration();

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		$routerDefinition = $builder->getDefinition($builder->getByType(RouterInterface::class));
		$this->assertSame(Router::class, $routerDefinition->getClass());
	}


	/**
	 * @return RoutingExtension
	 */
	private function getExtension()
	{
		$extension = new RoutingExtension;
		$extension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');
		return $extension;
	}

}
