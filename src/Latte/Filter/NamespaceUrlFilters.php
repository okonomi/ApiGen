<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Contracts\Routing\RouterInterface;


class NamespaceUrlFilters implements FilterProviderInterface
{

	/**
	 * @var string
	 */
	const NAMESPACE_SEP = '\\';

	/**
	 * @var LinkBuilderInterface
	 */
	private $linkBuilder;

	/**
	 * @var ElementStorageInterface
	 */
	private $elementStorage;

	/**
	 * @var RouterInterface
	 */
	private $router;


	public function __construct(
		LinkBuilderInterface $linkBuilder,
		ElementStorageInterface $elementStorage,
		RouterInterface $router
	) {
		$this->linkBuilder = $linkBuilder;
		$this->elementStorage = $elementStorage;
		$this->router = $router;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'namespaceUrl' => function ($name) {
				return $this->router->constructUrl('namespace', $name);
			},
			'namespaceLinks' => function ($namespace, $skipLast = TRUE) {
				return $this->namespaceLinks($namespace, $skipLast);
			}
		];
	}


	/**
	 * @param string $namespace
	 * @param bool $skipLast
	 * @return string
	 */
	private function namespaceLinks($namespace, $skipLast = TRUE)
	{
		if ( ! $this->elementStorage->getNamespaces()) {
			return $namespace;
		}

		$links = [];

		$parent = '';
		foreach (explode(self::NAMESPACE_SEP, $namespace) as $part) {
			$parent = ltrim($parent . self::NAMESPACE_SEP . $part, self::NAMESPACE_SEP);
			$links[] = $skipLast || $parent !== $namespace
				? $this->linkBuilder->build($this->router->constructUrl('namespace', $parent), $part)
				: $part;
		}

		return implode(self::NAMESPACE_SEP, $links);
	}

}
