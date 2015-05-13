<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;


class ResolverFilters implements FilterProviderInterface
{

	/**
	 * @var ElementResolverInterface
	 */
	private $elementResolver;


	public function __construct(ElementResolverInterface $elementResolver)
	{
		$this->elementResolver = $elementResolver;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'getClass' => function ($className, $namespace = NULL) {
				return $this->getClass($className, $namespace);
			},
			'resolveElement' => function ($definition, ElementReflectionInterface $context, &$expectedName = NULL) {
				return $this->resolveElement($definition, $context, $expectedName);
			}
		];
	}


	/**
	 * @param string $className
	 * @param string|NULL $namespace
	 * @return ClassReflectionInterface|FALSE
	 */
	private function getClass($className, $namespace = NULL)
	{
		$reflection = $this->elementResolver->getClass($className, $namespace);
		if ($reflection) {
			return $reflection;
		}
		return FALSE;
	}


	/**
	 * @param string $definition
	 * @param ElementReflectionInterface $context
	 * @param NULL $expectedName
	 * @return ElementReflectionInterface|bool|NULL
	 */
	private function resolveElement($definition, ElementReflectionInterface $context, &$expectedName = NULL)
	{
		$reflection = $this->elementResolver->resolveElement($definition, $context, $expectedName);
		if ($reflection) {
			return $reflection;
		}
		return FALSE;
	}

}
