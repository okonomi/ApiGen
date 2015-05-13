<?php

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\ElementLink\ClassConstantElementLink;
use Mockery;
use PHPUnit_Framework_TestCase;


class ClassConstantElementLinkTest extends PHPUnit_Framework_TestCase
{

	public function testBuildLink()
	{
		$routerMock = Mockery::mock(RouterInterface::class, [
			'constructUrl' => 'link'
		]);

		$classConstantElementLink = new ClassConstantElementLink(new LinkBuilder, $routerMock);

		$constantReflectionMock = Mockery::mock(ConstantReflectionInterface::class, [
			'getName' => 'SOME_CONSTANT',
			'getDeclaringClassName' => 'DeclaringClass'
		]);

		$this->assertSame(
			'<a href="link">DeclaringClass::<b>SOME_CONSTANT</b></a>',
			$classConstantElementLink->buildLink($constantReflectionMock)
		);
	}

}
