<?php

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\ElementLink\MethodElementLink;
use Mockery;
use PHPUnit_Framework_TestCase;


class MethodElementLinkTest extends PHPUnit_Framework_TestCase
{

	public function testCreateForElementClass()
	{
		$routerMock = Mockery::mock(RouterInterface::class, [
			'constructUrl' => 'link'
		]);

		$classElementLink = new MethodElementLink(new LinkBuilder, $routerMock);

		$methodReflectionMock = Mockery::mock(MethodReflectionInterface::class, [
			'getName' => 'getSome',
			'getDeclaringClassName' => 'SomeClass'
		]);

		$this->assertSame(
			'<a href="link">SomeClass::getSome()</a>',
			$classElementLink->buildLink($methodReflectionMock)
		);
	}

}
