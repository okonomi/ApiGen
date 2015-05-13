<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Generator\Resolvers\RelativePathResolver;


class RelativePathFilters implements FilterProviderInterface
{

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;


	public function __construct(RelativePathResolver $relativePathResolver)
	{
		$this->relativePathResolver = $relativePathResolver;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'relativePath' => function ($filename) {
				return $this->relativePathResolver->getRelativePath($filename);
			}
		];
	}

}
