<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator;

use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Generator\Decorator\AnnotationDecoratorInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;


class AnnotationDecorator implements AnnotationDecoratorInterface
{

	/**
	 * @var AnnotationDecorationInterface[][]
	 */
	private $annotationDecorations = [];


	/**
	 * {@inheritdoc}
	 */
	public function addDecoration(AnnotationDecorationInterface $annotationDecoration)
	{
		foreach ($annotationDecoration->getSupportedAnnotations() as $annotation) {
			$this->annotationDecorations[$annotation][] = $annotationDecoration;
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($annotation, $content, ElementReflectionInterface $elementReflection)
	{
		if (isset($this->annotationDecorations[$annotation])) {
			foreach ($this->annotationDecorations[$annotation] as $annotationDecoration) {
				$content = $annotationDecoration->decorate($content, $elementReflection);
			}
			return $content;
		}

		return $this->decorate('default', $content, $elementReflection);
	}

}
