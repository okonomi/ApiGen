<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\ElementLink;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;
use Nette\Utils\Html;


class PropertyElementLink implements ElementLinkInterface
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
		return PropertyReflectionInterface::class;
	}


	/**
	 * {@inheritdoc}
	 */
	public function buildLink(ElementReflectionInterface $element, array $classes = [])
	{
		/** @var InClassInterface|NamedInterface $element */
		$text = $element->getDeclaringClassName() . '::' . Html::el('var')->setText('$' . $element->getName());

		return $this->linkBuilder->build(
			$this->router->constructUrl('property', $element),
			$text,
			FALSE,
			$classes
		);
	}

}
