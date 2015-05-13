<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Routing\RouteInterface;


class PropertyRoute implements RouteInterface
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
		return 'property';
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($element = NULL)
	{
		/** @var PropertyReflectionInterface $element */
		return $this->classRoute->constructUrl($element->getDeclaringClass()) .
			'#' .
			($element->isMagic() ? 'm' : '') .
			'$' .
			$element->getName()
		;
	}

}
