<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Routing\RouteInterface;


class MethodRoute implements RouteInterface
{

	/**
	 * @var ClassRoute
	 */
	private $classRoute;


	public function __construct(ClassRoute $classRoute)
	{
		$this->classRoute = $classRoute;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return 'method';
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($methodReflection = NULL)
	{
		/** @var MethodReflectionInterface $methodReflection */
		return $this->classRoute->constructUrl($methodReflection->getDeclaringClass()) .
			'#' .
			($methodReflection->isMagic() ? 'm' : '') .
			'_' .
			($methodReflection->getOriginalName() ?: $methodReflection->getName())
		;
	}

}
