<?php

namespace ApiGen\Routing\Tests\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;
use ApiGen\Contracts\Routing\LinkBuilder\ElementLinkBuilderInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Routing\Exceptions\UnsupportedElementLinkException;
use ApiGen\Routing\LinkBuilder\ElementLinkBuilder;
use Mockery;
use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;


class ElementLinkBuilderTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ElementLinkBuilderInterface
	 */
	private $elementLinkBuilder;


	protected function setUp()
	{
		$routerMock = Mockery::mock(RouterInterface::class);
		$linkBuilderMock = Mockery::mock(LinkBuilderInterface::class);
		$this->elementLinkBuilder = new ElementLinkBuilder($routerMock, $linkBuilderMock);
	}


	public function testAddElementLink()
	{
		$elementLinkMock = Mockery::mock(ElementLinkInterface::class, ['getKey' => '']);
		$this->elementLinkBuilder->addElementLink($elementLinkMock);

		$this->assertCount(1, PHPUnit_Framework_Assert::getObjectAttribute($this->elementLinkBuilder, 'elementLinks'));
	}


	public function testConstructUnsupported()
	{
		$elementReflectionMock = Mockery::mock(ElementReflectionInterface::class);
		$this->setExpectedException(UnsupportedElementLinkException::class);
		$this->elementLinkBuilder->createForElement($elementReflectionMock);
	}


	public function testConstruct()
	{
		$elementLinkMock = Mockery::mock(ElementLinkInterface::class, [
			'getKey' => ReflectionClass::class,
			'buildLink' => 'someLink'
		]);
		$this->elementLinkBuilder->addElementLink($elementLinkMock);

		$classReflection = (new \ReflectionClass(ReflectionClass::class))->newInstanceWithoutConstructor();

		$this->assertSame('someLink', $this->elementLinkBuilder->createForElement($classReflection));
	}

}
