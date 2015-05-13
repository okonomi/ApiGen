<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\TemplateFile;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Templating\TemplateFile\TemplateFileInterface;
use ApiGen\Generator\GeneratorType;


class OverviewTemplateFile implements TemplateFileInterface
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
	public function getKey()
	{
		return GeneratorType::OVERVIEW;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFile()
	{
		return $this->configuration->getThemeConfiguration()->getOverviewTemplate();
	}

}
