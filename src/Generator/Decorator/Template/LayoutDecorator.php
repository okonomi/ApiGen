<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\Template;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Generator\Decorator\Template\LayoutDecoratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;
use ApiGen\Parser\Elements\AutocompleteElements;
use Closure;
use Nette\Utils\ArrayHash;


class LayoutDecorator implements LayoutDecoratorInterface
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ElementStorageInterface
	 */
	private $elementStorage;

	/**
	 * @var AutocompleteElements
	 */
	private $autocompleteElements;

	/**
	 * @var array
	 */
	private $parameters;


	public function __construct(
		Configuration $configuration,
		ElementStorageInterface $elementStorage,
		AutocompleteElements $autocompleteElements
	) {
		$this->configuration = $configuration;
		$this->elementStorage = $elementStorage;
		$this->autocompleteElements = $autocompleteElements;
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate(TemplateInterface $template)
	{
		$template->addParameters($this->getParameters());
		return $template;
	}


	/**
	 * @return Closure
	 */
	private function getMainFilter()
	{
		return function (ElementReflectionInterface $element) {
			return $element->isMain();
		};
	}


	/**
	 * @return array
	 */
	private function getParameters()
	{
		if ($this->parameters === NULL) {
			$parameters = [
				'annotationGroups' => $this->configuration->getAnnotationGroups(),
				'namespace' => NULL,
				'package' => NULL,
				'class' => NULL,
				'constant' => NULL,
				'function' => NULL,
				'namespaces' => array_keys($this->elementStorage->getNamespaces()),
				'packages' => array_keys($this->elementStorage->getPackages()),
				'classes' => array_filter($this->elementStorage->getClasses(), $this->getMainFilter()),
				'interfaces' => array_filter($this->elementStorage->getInterfaces(), $this->getMainFilter()),
				'traits' => array_filter($this->elementStorage->getTraits(), $this->getMainFilter()),
				'exceptions' => array_filter($this->elementStorage->getExceptions(), $this->getMainFilter()),
				'constants' => array_filter($this->elementStorage->getConstants(), $this->getMainFilter()),
				'functions' => array_filter($this->elementStorage->getFunctions(), $this->getMainFilter()),
				'elements' => $this->autocompleteElements->getElements(),
			];

			if ($this->configuration->isAvailableForDownload()) {
				$parameters['archive'] = basename($this->configuration->getZipFileName());
			}

			$this->parameters = $parameters + $this->getParametersBc();
		}
		return $this->parameters;
	}


	/**
	 * @deprecated since 4.2. To be remove in 5.0.
	 */
	private function getParametersBc()
	{
		$config = ArrayHash::from([
			'title' => $this->configuration->getTitle(),
			'googleAnalytics' => $this->configuration->getGoogleAnalytics(),
			'googleCseId' => $this->configuration->getGoogleCseId(),
			'main' => $this->configuration->getMain(),
			'tree' => $this->configuration->isTreeAllowed(),
			'download' => $this->configuration->isAvailableForDownload(),
			'sourceCode' => $this->configuration->shouldGenerateSourceCode(),
			'baseUrl' => $this->configuration->getThemeConfiguration()->getTemplatesPath(),
			'template' => [
				'options' => [
					'elementDetailsCollapsed' => $this->configuration->getThemeConfiguration()
						->shouldElementDetailsCollapse()
				]
			]
		]);

		$parameters['config'] = $config;
		return $parameters;
	}

}
