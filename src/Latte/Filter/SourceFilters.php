<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Routing\RouterInterface;


class SourceFilters implements FilterProviderInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var RouterInterface
	 */
	private $router;


	public function __construct(ConfigurationInterface $configuration, RouterInterface $router)
	{
		$this->configuration = $configuration;
		$this->router = $router;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'staticFile' => function ($name) {
				return $this->staticFile($name);
			},
			'sourceUrl' => function (ElementReflectionInterface $element, $withLine = TRUE) {
				return $this->sourceUrl($element, $withLine);
			},
		];
	}


	/**
	 * @param string $name
	 * @return string
	 */
	private function staticFile($name)
	{
		$filename = $this->configuration->getDestination() . '/' . $name;
		if (is_file($filename)) {
			$name .= '?' . sha1_file($filename);
		}
		return $name;
	}


	/**
	 * @param ElementReflectionInterface $element
	 * @param bool $withLine Include file line number into the link.
	 * @return string
	 */
	private function sourceUrl(ElementReflectionInterface $element, $withLine = TRUE)
	{
		$url = $this->router->constructUrl('sourceCode', $element);
		if ($withLine) {
			$url .= $this->getElementLinesAnchor($element);
		}
		return $url;
	}


	/**
	 * @return string
	 */
	private function getElementLinesAnchor(LinedInterface $element)
	{
		$anchor = '#' . $element->getStartLine();
		if ($element->getStartLine() !== $element->getEndLine()) {
			$anchor .= '-' . $element->getEndLine();
		}
		return $anchor;
	}

}
