<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\DI;

use ApiGen\Contracts\Routing\LinkBuilder\ElementLinkBuilderInterface;
use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;
use Nette\DI\CompilerExtension;


class RoutingExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/services.neon');
		$this->compiler->parseServices($builder, $config);
	}


	public function beforeCompile()
	{
		$this->loadRouterWithRoutes();
		$this->loadElementLinkBuilderWithElementLinks();
	}


	private function loadRouterWithRoutes()
	{
		$this->loadMediator(RouterInterface::class, RouteInterface::class, 'addRoute');
	}


	private function loadElementLinkBuilderWithElementLinks()
	{
		$this->loadMediator(ElementLinkBuilderInterface::class, ElementLinkInterface::class, 'addElementLink');
	}


	/**
	 * @param string $mediator
	 * @param string $event
	 * @param string $adderMethod
	 */
	private function loadMediator($mediator, $client, $adderMethod)
	{
		$builder = $this->getContainerBuilder();
		$builder->prepareClassList();

		$mediatorDefinition = $builder->getDefinition($builder->getByType($mediator));
		foreach ($builder->findByType($client) as $clientDefinition) {
			$mediatorDefinition->addSetup($adderMethod, ['@' . $clientDefinition->getClass()]);
		}
	}

}
