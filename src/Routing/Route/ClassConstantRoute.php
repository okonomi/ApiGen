<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Parser\Reflection\ClassConstantReflectionInterface;
use ApiGen\Contracts\Routing\RouteInterface;


class ClassConstantRoute implements RouteInterface
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
		return 'classConstant';
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($constantReflection = NULL)
	{
		/** @var ClassConstantReflectionInterface $constantReflection */
		return $this->classRoute->constructUrl($constantReflection->getDeclaringClass()) .
			'#' .
			$constantReflection->getName()
		;
	}

}
