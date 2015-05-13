<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\ElementLink;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;


class FunctionElementLink implements ElementLinkInterface
{

	/**
	 * @var LinkBuilderInterface
	 */
	private $linkBuilder;

	/**
	 * @var RouterInterface
	 */
	private $router;


	public function __construct(LinkBuilderInterface $linkBuilder, RouterInterface $router)
	{
		$this->linkBuilder = $linkBuilder;
		$this->router = $router;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return FunctionReflectionInterface::class;
	}


	/**
	 * {@inheritdoc}
	 */
	public function buildLink(ElementReflectionInterface $element, array $classes = [])
	{
		return $this->linkBuilder->build(
			$this->router->constructUrl('function', $element),
			$element->getName() . '()',
			TRUE,
			$classes
		);
	}

}
