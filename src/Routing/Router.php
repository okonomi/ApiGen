<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\Exceptions\UnsupportedRouteException;


class Router implements RouterInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var RouteInterface[]
	 */
	private $routes;


	public function __construct(ConfigurationInterface $configuration)
	{
		$this->configuration = $configuration;
	}


	/**
	 * {@inheritdoc}
	 */
	public function addRoute(RouteInterface $route)
	{
		$this->routes[$route->getKey()] = $route;
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($key, $element = NULL, $absolute = FALSE)
	{
		if (isset($this->routes[$key])) {
			$url = $this->routes[$key]->constructUrl($element);
			if ($absolute) {
				return $this->configuration->getDestination() . '/' . $url;
			}
			return $url;
		}

		throw new UnsupportedRouteException(
			sprintf('Route for "%s" was not found.', $key)
		);
	}

}
