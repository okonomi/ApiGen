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


class PackageUrlFilters implements FilterProviderInterface
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
			'packageUrl' => function ($name) {
				return $this->router->constructUrl('package',$name);
			},
			'subgroupName' => function ($name) {
				if ($pos = strrpos($name, self::NAMESPACE_SEP)) {
					return substr($name, $pos + 1);
				}
				return $name;
			},
			'packageLinks' => function ($package, $skipLast = TRUE) {
				return $this->packageLinks($package, $skipLast);
			}
		];
	}


	/**
	 * @param string $package
	 * @param bool $skipLast
	 * @return string
	 */
	private function packageLinks($package, $skipLast = TRUE)
	{
		if ( ! $this->elementStorage->getPackages()) {
			return $package;
		}

		$links = [];

		$parent = '';
		foreach (explode(self::NAMESPACE_SEP, $package) as $part) {
			$parent = ltrim($parent . self::NAMESPACE_SEP . $part, self::NAMESPACE_SEP);
			$links[] = ($skipLast || $parent !== $package)
				? $this->linkBuilder->build($this->router->constructUrl('package', $parent), $part)
				: $part;
		}

		return implode(self::NAMESPACE_SEP, $links);
	}

}
