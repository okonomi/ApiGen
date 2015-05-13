<?php

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\ElementLink\FunctionElementLink;
use Mockery;
use PHPUnit_Framework_TestCase;


class FunctionElementLinkTest extends PHPUnit_Framework_TestCase
{

	public function testCreateForElementClass()
	{
		$routerMock = Mockery::mock(RouterInterface::class, [
			'constructUrl' => 'link'
		]);

		$classElementLink = new FunctionElementLink(new LinkBuilder, $routerMock);

		$functionReflectionMock = Mockery::mock(FunctionReflectionInterface::class, [
			'getName' => 'getSome',
			'getDeclaringClassName' => 'DeclaringClass'
		]);

		$this->assertSame(
			'<a href="link">getSome()</a>',
			$classElementLink->buildLink($functionReflectionMock)
		);
	}

}
