<?php

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\ElementLink\ClassElementLink;
use Mockery;
use PHPUnit_Framework_TestCase;


class ClassElementLinkTest extends PHPUnit_Framework_TestCase
{

	public function testBuildLink()
	{
		$routerMock = Mockery::mock(RouterInterface::class, [
			'constructUrl' => 'link'
		]);

		$classElementLink = new ClassElementLink(new LinkBuilder, $routerMock);

		$reflectionClassMock = Mockery::mock(ClassReflectionInterface::class, [
			'getName' => 'SomeClass',
		]);

		$this->assertSame(
			'<a href="link">SomeClass</a>',
			$classElementLink->buildLink($reflectionClassMock)
		);
	}

}
