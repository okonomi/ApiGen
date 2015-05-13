<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Filters\Helpers\TypeLinkBuilderInterface;
use ApiGen\Contracts\Utils\NormalizerInterface;
use ApiGen\Generator\Resolvers\LinkResolver;
use ApiGen\Latte\Strings;
use Latte\Runtime\Filters;


class TypeLinkBuilder implements TypeLinkBuilderInterface
{

	/**
	 * @var NormalizerInterface
	 */
	private $normalizer;

	/**
	 * @var LinkResolver
	 */
	private $linkResolver;


	public function __construct(NormalizerInterface $normalizer, LinkResolver $linkResolver)
	{
		$this->normalizer = $normalizer;
		$this->linkResolver = $linkResolver;
	}


	/**
	 * {@inheritdoc}
	 */
	public function build($annotation, ElementReflectionInterface $reflectionElement)
	{
		$links = [];
		list($types) = Strings::split($annotation);
		if ( ! empty($types) && $types[0] === '$') {
			$types = NULL;
		}

		foreach (explode('|', $types) as $type) {
			$type = $this->normalizer->normalizerType($type, FALSE);

			$link = $this->linkResolver->resolve($type, $reflectionElement) ?: Filters::escapeHtml(ltrim($type, '\\'));
			$links[] = $link;
		}

		return implode('|', $links);
	}

}
