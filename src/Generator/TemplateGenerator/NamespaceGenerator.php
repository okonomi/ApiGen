<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Console\Helper\ProgressBarFacadeInterface;
use ApiGen\Contracts\Generator\Decorator\Template\NamespaceDecoratorInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;


class NamespaceGenerator implements GeneratorInterface, StepCounterInterface
{

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;

	/**
	 * @var ElementStorageInterface
	 */
	private $elementStorage;

	/**
	 * @var NamespaceDecoratorInterface
	 */
	private $namespaceDecorator;

	/**
	 * @var ProgressBarFacadeInterface
	 */
	private $progressBarFacade;


	public function __construct(
		TemplateFactoryInterface $templateFactory,
		ElementStorageInterface $elementStorage,
		NamespaceDecoratorInterface $namespaceDecorator,
		ProgressBarFacadeInterface $progressBarFacade
	) {
		$this->templateFactory = $templateFactory;
		$this->elementStorage = $elementStorage;
		$this->namespaceDecorator = $namespaceDecorator;
		$this->progressBarFacade = $progressBarFacade;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
			$template = $this->templateFactory->create(GeneratorType::NAMESPACE_, $name);
			$template = $this->namespaceDecorator->decorate($template, $name);
			$template->save();

			$this->progressBarFacade->advance();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getNamespaces());
	}

}
