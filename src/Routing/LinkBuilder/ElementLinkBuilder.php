<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Routing\LinkBuilder\ElementLinkBuilderInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;
use ApiGen\Routing\Exceptions\UnsupportedElementLinkException;


class ElementLinkBuilder implements ElementLinkBuilderInterface
{

	/**
	 * @var ElementLinkInterface[]
	 */
	private $elementLinks;


	public function __construct(RouterInterface $router, LinkBuilderInterface $linkBuilder)
	{
		$this->router = $router;
		$this->linkBuilder = $linkBuilder;
	}


	/**
	 * {@inheritdoc}
	 */
	public function addElementLink(ElementLinkInterface $elementLink)
	{
		$this->elementLinks[$elementLink->getKey()] = $elementLink;
	}


	/**
	 * {@inheritdoc}
	 */
	public function createForElement(ElementReflectionInterface $element, array $classes = [])
	{
		foreach ($this->elementLinks as $type => $elementLink) {
			if ($element instanceof $type) {
				return $elementLink->buildLink($element, $classes);
			}
		}

		throw new UnsupportedElementLinkException(
			sprintf('Type "%s" is not supported yet.', get_class($element))
		);
	}

}
