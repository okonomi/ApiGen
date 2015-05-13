<?php

namespace ApiGen\Latte\Tests\DI\FiltersSource;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;


class FooFilters implements FilterProviderInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'bazFilter' => function ($text) {
				return 'Filtered: ' . $text;
			}
		];
	}

}
