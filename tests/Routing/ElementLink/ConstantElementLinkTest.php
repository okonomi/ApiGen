<?php

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Routing\ElementLink\ConstantElementLink;
use Mockery;
use PHPUnit_Framework_TestCase;


class ConstantElementLinkTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ConstantElementLink
	 */
	private $constantElementLink;


	protected function setUp()
	{
		$routerMock = Mockery::mock(RouterInterface::class, [
			'constructUrl' => 'link',
		]);

		$this->constantElementLink = new ConstantElementLink(new LinkBuilder, $routerMock);
	}


	public function testBuildLinkForConstantInNamespace()
	{
		$constantReflectionMock = Mockery::mock(ConstantReflectionInterface::class, [
			'getShortName' => 'SOME_CONSTANT',
			'inNamespace' => TRUE,
			'getNamespaceName' => 'SomeNamespace',
		]);

		$this->assertSame(
			'<a href="link">SomeNamespace\<b>SOME_CONSTANT</b></a>',
			$this->constantElementLink->buildLink($constantReflectionMock)
		);
	}


	public function testBuildLinkForNonNamespaced()
	{
		$constantReflectionMock = Mockery::mock(ConstantReflectionInterface::class, [
			'getName' => 'SOME_CONSTANT',
			'getDeclaringClassName' => NULL,
			'inNamespace' => FALSE
		]);

		$this->assertSame(
			'<a href="link"><b>SOME_CONSTANT</b></a>',
			$this->constantElementLink->buildLink($constantReflectionMock)
		);
	}

}
