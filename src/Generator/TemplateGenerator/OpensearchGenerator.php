<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\ConditionalGeneratorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;


class OpensearchGenerator implements ConditionalGeneratorInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;


	public function __construct(ConfigurationInterface $configuration, TemplateFactoryInterface $templateFactory)
	{
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		$template = $this->templateFactory->create(GeneratorType::OPENSEARCH);
		$template->save();
	}


	/**
	 * {@inheritdoc}
	 */
	public function isAllowed()
	{
		return $this->configuration->getGoogleCseId() && $this->configuration->getBaseUrl();
	}

}
