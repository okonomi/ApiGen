<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\AnnotationDecoration;

use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Filters\Helpers\TypeLinkBuilderInterface;


class TypeAnnotationDecoration implements AnnotationDecorationInterface
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
	public function getSupportedAnnotations()
	{
		return ['return', 'throws'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		$description = $this->getDescriptionFromValue($content, $elementReflection);
		$typeLinks = $this->typeLinkBuilder->build($content, $elementReflection);
		return $typeLinks . $description;
	}


	/**
	 * @param string $value
	 * @param ElementReflectionInterface $elementReflection
	 * @return string
	 */
	private function getDescriptionFromValue($value, ElementReflectionInterface $elementReflection)
	{
		$description = trim(strpbrk($value, "\n\r\t $")) ?: NULL;
		if ($description) {
			// todo: extract to common deps
			$description = '<br>' . $this->resolveDocBlock($description, $elementReflection);
		}
		return $description;
	}

}
