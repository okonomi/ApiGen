<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Contracts\Theme\Configuration\ThemeConfigurationInterface;
use ApiGen\Generator\GeneratorType;


class CombinedGenerator implements GeneratorInterface
{

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration, TemplateFactoryInterface $templateFactory)
	{
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		// todo: fix autocomplete
		$template = $this->templateFactory->create(GeneratorType::COMBINED);
		$template->addParameters([
			'basePath' => $this->configuration->getThemeConfiguration()->getTemplatesPath(),
			'elementDetailsCollapsed' => $this->configuration->getThemeConfiguration()
				->shouldElementDetailsCollapse(),
			'elementsOrder' => $this->configuration->getThemeConfiguration()->getElementsOrder(),
			'scripts' => [
				'jquery.min.js', 'jquery.cookie.js', 'jquery.sprintf.js', 'jquery.autocomplete.js',
				'jquery.sortElements.js', 'main.js'
			],
			'autocompleteFileNameMasks' => [
				'c' => 'class-%s.html',
				'co' => 'constant-%s.html',
				'f' => 'function-%s.html'
			]
		]);
		$template->save();
	}

}
