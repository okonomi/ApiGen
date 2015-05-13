<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\Resolvers\RelativePathResolverInterface;
use ApiGen\Utils\FileSystem;
use InvalidArgumentException;


class RelativePathResolver implements RelativePathResolverInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var FileSystem
	 */
	private $fileSystem;


	public function __construct(ConfigurationInterface $configuration, FileSystem $fileSystem)
	{
		$this->configuration = $configuration;
		$this->fileSystem = $fileSystem;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getRelativePath($fileName)
	{
		foreach ($this->configuration->getSource() as $directory) {
			if (strpos($fileName, $directory) === 0) {
				return $this->getFileNameWithoutSourcePath($fileName, $directory);
			}
		}
		throw new InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
	}


	/**
	 * @param string $fileName
	 * @param string $directory
	 * @return string
	 */
	private function getFileNameWithoutSourcePath($fileName, $directory)
	{
		$directory = rtrim($directory, '/');
		$fileName = substr($fileName, strlen($directory) + 1);
		return $this->fileSystem->normalizePath($fileName);
	}

}
