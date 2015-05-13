<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\AnnotationDecoration;

use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Generator\Resolvers\LinkResolverInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Templating\Filters\Helpers\Strings;
use Nette\Utils\Validators;


class LinkAndSeeAnnotationDecoration implements AnnotationDecorationInterface
{

	/**
	 * @var LinkBuilderInterface
	 */
	private $linkBuilder;

	/**
	 * @var LinkResolverInterface
	 */
	private $linkResolver;


	public function __construct(LinkBuilderInterface $linkBuilder, LinkResolverInterface $linkResolver)
	{
		$this->linkBuilder = $linkBuilder;
		$this->linkResolver = $linkResolver;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getSupportedAnnotations()
	{
		return ['link', 'see'];
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate($content, ElementReflectionInterface $elementReflection)
	{
		return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($elementReflection) {
			list($url, $description) = Strings::split($matches[1]);

			if (Validators::isUri($url)) {
				return $this->linkBuilder->build($url, $description ?: $url);
			}

			if ($link = $this->linkResolver->resolve($matches[1], $elementReflection)) {
				return $link;
			}

			return $matches[1];
		}, $content);
	}

}
