<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Generators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\ConditionalGeneratorInterface;
use ApiGen\Utils\ZipArchiveGenerator;


class ZipGenerator implements ConditionalGeneratorInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var ZipArchiveGenerator
	 */
	private $zipArchiveGenerator;


	public function __construct(ConfigurationInterface $configuration, ZipArchiveGenerator $zipArchiveGenerator)
	{
		$this->configuration = $configuration;
		$this->zipArchiveGenerator = $zipArchiveGenerator;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		$destination = $this->configuration->getDestination();
		$zipFile = $destination . '/' . $this->configuration->getZipFileName();
		$this->zipArchiveGenerator->zipDirToFile($destination, $zipFile);
	}


	/**
	 * {@inheritdoc}
	 */
	public function isAllowed()
	{
		return $this->configuration->isAvailableForDownload();
	}

}
