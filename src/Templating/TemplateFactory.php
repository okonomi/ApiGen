<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Generator\Decorator\Template\LayoutDecoratorInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Contracts\Templating\TemplateFileManagerInterface;
use ApiGen\Contracts\Theme\Configuration\ThemeConfigurationInterface;
use Latte\Engine;


class TemplateFactory implements TemplateFactoryInterface
{

	/**
	 * @var Engine
	 */
	private $latteEngine;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @var TemplateInterface
	 */
	private $builtTemplate;

	/**
	 * @var TemplateFileManagerInterface
	 */
	private $templateFileManager;

	/**
	 * @var LayoutDecoratorInterface
	 */
	private $layoutDecorator;


	public function __construct(
		Engine $latteEngine,
		Configuration $configuration,
		TemplateFileManagerInterface $templateFileManager,
		RouterInterface $templateNavigator,
		LayoutDecoratorInterface $layoutDecorator
	) {
		$this->latteEngine = $latteEngine;
		$this->configuration = $configuration;
		$this->templateFileManager = $templateFileManager;
		$this->router = $templateNavigator;
		$this->layoutDecorator = $layoutDecorator;
	}


	/**
	 * {@inheritdoc}
	 */
	public function create($key = NULL, $element = NULL)
	{
		$template = $this->buildTemplate();
		if ($key) {
			$template->setFile($this->templateFileManager->getFile($key));
			$template->setSavePath($this->router->constructUrl($key, $element, TRUE));
		}

		$this->layoutDecorator->decorate($template);

//		$this->templateElementsLoader->addElementsToTemplate($template);

		$template = $this->setEmptyDefaults($template);
		return $template;
	}


	/**
	 * @return TemplateInterface
	 */
	private function buildTemplate()
	{
		if ($this->builtTemplate === NULL) {
			$template = $this->createTemplate();
			$template->addParameters([
				'title' => $this->configuration->getTitle(),
				'googleCseId' => $this->configuration->getGoogleCseId(),
				'googleAnalytics' => $this->configuration->getGoogleAnalytics(),
				'main' => $this->configuration->getMain(),
				'download' => $this->configuration->isAvailableForDownload(),
				'tree' => $this->configuration->isTreeAllowed(),
				'baseUrl' => $this->configuration->getBaseUrl(),
				'sourceCode' => $this->configuration->shouldGenerateSourceCode(),
				'elementDetailsCollapsed' => $this->configuration->getThemeConfiguration()->shouldElementDetailsCollapse()
			]);

			$this->builtTemplate = $template;
		}
		return $this->layoutDecorator->decorate($this->builtTemplate);
	}


	/**
	 * @return TemplateInterface
	 */
	private function setEmptyDefaults(TemplateInterface $template)
	{
		$template->addParameters([
			'namespace' => NULL,
			'package' => NULL
		]);
		return $template;
	}


	/**
	 * @return TemplateInterface
	 */
	private function createTemplate()
	{
		return new Template($this->latteEngine);
	}

}
