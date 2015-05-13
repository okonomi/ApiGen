<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\DI;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use Latte\Engine;
use Nette\DI\CompilerExtension;


class LatteExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/services.neon');
		$this->compiler->parseServices($builder, $config);
	}


	public function beforeCompile()
	{
		$this->loadLatteFilters();
	}


	private function loadLatteFilters()
	{
		$builder = $this->getContainerBuilder();

		$latteFactory = $builder->getDefinition($builder->getByType(Engine::class));
		foreach ($builder->findByType(FilterProviderInterface::class) as $filterDefinition) {
			$latteFactory->addSetup(
				'foreach (?->getFilters() as $name => $callback) { ?->addFilter($name, $callback); }',
				['@' . $filterDefinition->getClass(), '@self']
			);
		}
	}

}
