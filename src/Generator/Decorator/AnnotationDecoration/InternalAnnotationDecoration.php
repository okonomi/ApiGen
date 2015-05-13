<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\AnnotationDecoration;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;


class InternalAnnotationDecoration implements AnnotationDecorationInterface
{

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSupportedAnnotations()
	{
		return ['internal'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		$pattern = '~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~';
		return preg_replace_callback($pattern, function ($matches) {
			if ($matches[1] !== 'internal') {
				return $matches[0];
			}

			if ($this->configuration->isInternalDocumented() && isset($matches[2])) {
				return $matches[2];
			}

			return '';
		}, $content);
	}

}
