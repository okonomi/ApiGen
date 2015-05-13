<?php

namespace ApiGen\Routing\Tests\LinkBuilder\ElementLinkBuilderSource;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;


class ElementLinkBuilderSource implements ElementLinkInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		// TODO: Implement getKey() method.
	}


	/**
	 * @param ElementReflectionInterface $element
	 * @param string[] $classes
	 * @return string
	 */
	function buildLink(ElementReflectionInterface $element, array $classes = [])
	{
		// TODO: Implement buildLink() method.
	}
}
