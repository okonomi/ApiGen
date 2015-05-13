<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Filters\Helpers\TypeLinkBuilderInterface;


class UrlFilters implements FilterProviderInterface
{

	/**
	 * @var TypeLinkBuilderInterface
	 */
	private $typeLinkBuilder;


	public function __construct(TypeLinkBuilderInterface $typeLinkBuilder)
	{
		$this->typeLinkBuilder = $typeLinkBuilder;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'rawurlencode' => function ($text) {
				return rawurlencode($text);
			},
			'typeLinks' => function ($className, ElementReflectionInterface $element) {
				return $this->typeLinkBuilder->build($className, $element);
			}
		];
	}

}
