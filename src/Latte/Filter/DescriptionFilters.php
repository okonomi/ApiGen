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
use ApiGen\Generator\Decorator\AnnotationDecoration\DefaultAnnotationDecoration;


class DescriptionFilters implements FilterProviderInterface
{

	/**
	 * @var DefaultAnnotationDecoration
	 */
	private $defaultAnnotationDecoration;


	public function __construct(DefaultAnnotationDecoration $defaultAnnotationDecoration)
	{
		$this->defaultAnnotationDecoration = $defaultAnnotationDecoration;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'description' => function ($annotation, ElementReflectionInterface $element) {
				return $this->description($annotation, $element);
			},
			'shortDescription' => function(ElementReflectionInterface $element, $block = FALSE) {
				return $this->shortDescription($element, $block);
			},
			'longDescription' => function (ElementReflectionInterface $element) {
				return $this->longDescription($element);
			},
		];
	}


	/**
	 * @param string $annotation
	 * @param ElementReflectionInterface $reflectionElement
	 * @return string
	 */
	private function description($annotation, ElementReflectionInterface $reflectionElement)
	{
		$description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
		return $this->defaultAnnotationDecoration->decorate($description, $reflectionElement);
	}


	/**
	 * @param ElementReflectionInterface $reflectionElement
	 * @param bool $block
	 * @return string
	 */
	private function shortDescription(ElementReflectionInterface $reflectionElement, $block = FALSE)
	{
		return $this->defaultAnnotationDecoration->decorate(
			$reflectionElement->getShortDescription(), $reflectionElement
		);
		// , $block);
	}


	/**
	 * @return string
	 */
	private function longDescription(ElementReflectionInterface $element)
	{
		$long = $element->getLongDescription();

		// Merge lines
		$long = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function ($matches) {
			return ! empty($matches[2])
				? preg_replace('~\n(?:\t|[ ])+~', ' ', $matches[2])
				: $matches[0];
		}, $long);

		return $this->defaultAnnotationDecoration->decorate($long, $element, TRUE);
	}

}
