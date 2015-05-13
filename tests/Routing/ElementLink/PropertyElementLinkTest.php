<?php

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\ElementLink\PropertyElementLink;
use Mockery;
use PHPUnit_Framework_TestCase;


class PropertyElementLinkTest extends PHPUnit_Framework_TestCase
{

	public function testCreateForElementClass()
	{
		$routerMock = Mockery::mock(RouterInterface::class, [
			'constructUrl' => 'link'
		]);

		$classElementLink = new PropertyElementLink(new LinkBuilder, $routerMock);

		$propertyReflectionMock = Mockery::mock(PropertyReflectionInterface::class, [
			'getName' => 'someProperty',
			'getDeclaringClassName' => 'SomeClass'
		]);

		$this->assertSame(
			'<a href="link">SomeClass::<var>$someProperty</var></a>',
			$classElementLink->buildLink($propertyReflectionMock)
		);
	}

}
