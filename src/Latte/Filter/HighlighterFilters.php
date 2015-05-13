<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Generator\Resolvers\LinkResolverInterface;
use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Markup\PhpCodeHighlighter\PhpCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Utils\NormalizerInterface;


class HighlighterFilters implements FilterProviderInterface
{

	/**
	 * @var NormalizerInterface
	 */
	private $normalizer;

	/**
	 * @var PhpCodeHighlighterInterface
	 */
	private $phpCodeHighlighter;

	/**
	 * @var LinkResolverInterface
	 */
	private $linkResolver;


	public function __construct(
		NormalizerInterface $normalizer,
		PhpCodeHighlighterInterface $phpCodeHighlighter,
		LinkResolverInterface $linkResolver
	) {
		$this->normalizer = $normalizer;
		$this->phpCodeHighlighter = $phpCodeHighlighter;
		$this->linkResolver = $linkResolver;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'highlightPhp' => function ($source, ElementReflectionInterface $elementReflection) {
				return $this->highlightPhp($source, $elementReflection);
			},
			'highlightValue' => function ($definition, ElementReflectionInterface $elementReflection) {
				return $this->highlightValue($definition, $elementReflection);
			}
		];
	}


	/**
	 * @param string $source
	 * @param ElementReflectionInterface $reflectionElement
	 * @return string
	 */
	private function highlightPhp($source, ElementReflectionInterface $reflectionElement)
	{
		$link = $this->linkResolver->resolve($this->normalizer->normalizerType($source), $reflectionElement);
		if ($link) {
			return $link;
		}
		return $this->phpCodeHighlighter->highlight((string) $source);
	}


	/**
	 * @param string $definition
	 * @param ElementReflectionInterface $reflectionElement
	 * @return string
	 */
	private function highlightValue($definition, ElementReflectionInterface $reflectionElement)
	{
		return $this->highlightPhp(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $reflectionElement);
	}

}
