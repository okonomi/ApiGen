<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Generator\Resolvers\LinkResolverInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Routing\LinkBuilder\ElementLinkBuilderInterface;


class LinkResolver implements LinkResolverInterface
{

	/**
	 * @var ElementResolverInterface
	 */
	private $elementResolver;

	/**
	 * @var ElementLinkBuilderInterface
	 */
	private $elementLinkFactory;


	public function __construct(ElementResolverInterface $elementResolver, ElementLinkBuilderInterface $elementLinkBuilder)
	{
		$this->elementResolver = $elementResolver;
		$this->elementLinkFactory = $elementLinkBuilder;
	}


	/**
	 * {@inheritdoc}
	 */
	public function resolve($definition, ElementReflectionInterface $reflectionElement)
	{
		if (empty($definition)) {
			return NULL;
		}

		$suffix = '';
		if (substr($definition, -2) === '[]') {
			$definition = substr($definition, 0, -2);
			$suffix = '[]';
		}

		$element = $this->elementResolver->resolveElement($definition, $reflectionElement, $expectedName);
		if ($element === NULL) {
			return $expectedName;
		}

		$classes = [];
		if ($element->isDeprecated()) {
			$classes[] = 'deprecated';
		}

		/** @var FunctionReflectionInterface $element */
		if ( ! $element->isValid()) {
			$classes[] = 'invalid';
		}

		$link = $this->elementLinkFactory->createForElement($element, $classes);
		return sprintf('<code>%s%s</code>', $link, $suffix);
	}

}
