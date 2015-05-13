<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Latte\Tests\DI\FiltersSource\FooFilters;
use PHPUnit_Framework_TestCase;


class FiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var FilterProviderInterface
	 */
	private $filters;


	protected function setUp()
	{
		$this->filters = new FooFilters;
	}


	public function testProvider()
	{
		$filters = $this->filters->getFilters();
		$this->assertSame('Filtered: foo', $filters['bazFilter']('foo'));
	}

}
