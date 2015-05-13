<?php

namespace ApiGen\Routing\Tests;

use ApiGen\Contracts\Routing\Configuration\RoutingConfigurationInterface;
use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\Exceptions\UnsupportedRouteException;
use ApiGen\Routing\Router;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class RouterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var RouterInterface
	 */
	private $router;


	protected function setUp()
	{
		$configuration = Mockery::mock(RoutingConfigurationInterface::class, [
			'getDestination' => __DIR__
		]);
		$this->router = new Router($configuration);
	}


	public function testAddRoute()
	{
		$routeMock = Mockery::mock(RouteInterface::class, ['getKey' => '']);
		$this->router->addRoute($routeMock);

		$this->assertCount(1, PHPUnit_Framework_Assert::getObjectAttribute($this->router, 'routes'));
	}


	public function testConstructUnsupported()
	{
		$this->setExpectedException(UnsupportedRouteException::class);
		$this->router->constructUrl('someKey');
	}


	public function testConstruct()
	{
		$routeMock = Mockery::mock(RouteInterface::class, [
			'getKey' => 'someRouteKey'
		]);
		$routeMock->shouldReceive('constructUrl')->andReturnUsing(function ($name) {
			return 'route-' . $name;
		});
		$this->router->addRoute($routeMock);

		$this->assertSame('route-someName', $this->router->constructUrl('someRouteKey', 'someName'));

		$this->assertSame(__DIR__ . '/route-someName', $this->router->constructUrl('someRouteKey', 'someName', TRUE));
	}

}
