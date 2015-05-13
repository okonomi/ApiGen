<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;


class ElementUrlFilters implements FilterProviderInterface
{

	/**
	 * @var RouterInterface
	 */
	private $router;


	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'elementUrl' => function ($element) {
				return $this->constructElementUrl($element);
			},
			'classUrl' => function ($element) {
				return $this->router->constructUrl('class', $element);
			},
			'methodUrl' => function ($element) {
				return $this->router->constructUrl('method', $element);
			},
			'functionUrl' => function ($element) {
				return $this->router->constructUrl('function', $element);
			},
			'constantUrl' => function ($element) {
				return $this->router->constructUrl('constant', $element);
			},
			'propertyUrl' => function ($element) {
				return $this->router->constructUrl('property', $element);
			},
		];
	}


	/**
	 * @return string
	 */
	private function constructElementUrl(ElementReflectionInterface $element)
	{
		if ($element instanceof ClassReflectionInterface) {
			return $this->router->constructUrl('class', $element);

		} elseif ($element instanceof FunctionReflectionInterface) {
			return $this->router->constructUrl('function', $element);

		} elseif ($element instanceof ConstantReflectionInterface) {
			return $this->router->constructUrl('constant', $element);

		} elseif ($element instanceof MethodReflectionInterface) {
			return $this->router->constructUrl('method', $element);

		} elseif ($element instanceof PropertyReflectionInterface) {
			return $this->router->constructUrl('property', $element);
		}
	}

}
