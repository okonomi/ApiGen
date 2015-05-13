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
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Filters\Helpers\TypeLinkBuilderInterface;
use ApiGen\Templating\Filters\Helpers\Strings;


class UsesAnnotationDecoration implements AnnotationDecorationInterface
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
		return ['uses', 'usedby'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		list($link, $description) = Strings::split($content);

		$separator = $this->determineSeparator($elementReflection, $description);
		if ($this->elementResolver->resolveElement($link, $elementReflection)) {
			$value = $this->typeLinkBuilder->build($link, $elementReflection) . $separator . $description;
			return trim($value);
		}
		return NULL;
	}


	/**
	 * @param ElementReflectionInterface $elementReflection
	 * @param string $description
	 * @return string
	 */
	private function determineSeparator(ElementReflectionInterface $elementReflection, $description)
	{
		if ($elementReflection instanceof ClassReflectionInterface || ! $description) {
			return ' ';
		}
		return '<br>';
	}

}
