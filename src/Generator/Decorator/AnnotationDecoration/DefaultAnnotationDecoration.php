<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\AnnotationDecoration;

use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Markup\Markdown\MarkdownParserInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;


class DefaultAnnotationDecoration implements AnnotationDecorationInterface
{

	/**
	 * @var MarkdownParserInterface
	 */
	private $markdownParser;

	/**
	 * @var InternalAnnotationDecoration
	 */
	private $internalAnnotationDecoration;

	/**
	 * @var LinkAndSeeAnnotationDecoration
	 */
	private $linkAndSeeAnnotationDecoration;


	public function __construct(
		MarkdownParserInterface $markdownParser,
		InternalAnnotationDecoration $internalAnnotationDecoration,
		LinkAndSeeAnnotationDecoration $linkAndSeeAnnotationDecoration
	) {
		$this->markdownParser = $markdownParser;
		$this->internalAnnotationDecoration = $internalAnnotationDecoration;
		$this->linkAndSeeAnnotationDecoration = $linkAndSeeAnnotationDecoration;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSupportedAnnotations()
	{
		return ['default'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		// @todo: figure out third $block option
		$content = $this->internalAnnotationDecoration->decorate($content, $elementReflection);
		$content = $this->markdownParser->parse($content);
		return $this->linkAndSeeAnnotationDecoration->decorate($content, $elementReflection);
	}

}
