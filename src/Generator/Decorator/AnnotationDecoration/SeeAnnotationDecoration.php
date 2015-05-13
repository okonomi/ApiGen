<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\AnnotationDecoration;

use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Filters\Helpers\TypeLinkBuilderInterface;


class SeeAnnotationDecoration implements AnnotationDecorationInterface
{

	/**
	 * @var TypeLinkBuilderInterface
	 */
	private $typeLinkBuilder;

	/**
	 * @var ElementResolverInterface
	 */
	private $elementResolver;


	public function __construct(TypeLinkBuilderInterface $typeLinkBuilder, ElementResolverInterface $elementResolver)
	{
		$this->typeLinkBuilder = $typeLinkBuilder;
		$this->elementResolver = $elementResolver;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSupportedAnnotations()
	{
		return ['see'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		$doc = [];
		foreach (preg_split('~\\s*,\\s*~', $content) as $link) {
			if ($this->elementResolver->resolveElement($link, $elementReflection) !== NULL) {
				$doc[] = $this->typeLinkBuilder->build($link, $elementReflection);

			} else {
				// TODO: extract from AnnotationResolver
				$doc[] = $this->resolveDocBlock($link, $elementReflection);
			}
		}
		return implode(', ', $doc);
	}

}
