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
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Templating\Filters\Helpers\Strings;


class LicenseAnnotationDecoration implements AnnotationDecorationInterface
{

	/**
	 * @var LinkBuilderInterface
	 */
	private $linkBuilder;


	public function __construct(LinkBuilderInterface $linkBuilder)
	{
		$this->linkBuilder = $linkBuilder;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSupportedAnnotations()
	{
		return ['license'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		list($url, $description) = Strings::split($content);
		return $this->linkBuilder->build($url, $description ?: $url);
	}

}
