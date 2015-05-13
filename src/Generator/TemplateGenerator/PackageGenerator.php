<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Console\Helper\ProgressBarFacadeInterface;
use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Generator\Decorator\Template\PackageDecoratorInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;


class PackageGenerator implements GeneratorInterface, StepCounterInterface
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
	 * @var PackageDecoratorInterface
	 */
	private $packageDecorator;

	/**
	 * @var ProgressBarInterface
	 */
	private $progressBarFacade;


	public function __construct(
		TemplateFactoryInterface $templateFactory,
		ElementStorageInterface $elementStorage,
		PackageDecoratorInterface $packageDecorator,
		ProgressBarFacadeInterface $progressBarFacade
	) {
		$this->templateFactory = $templateFactory;
		$this->elementStorage = $elementStorage;
		$this->packageDecorator = $packageDecorator;
		$this->progressBarFacade = $progressBarFacade;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->elementStorage->getPackages() as $name => $package) {
			$template = $this->templateFactory->create(GeneratorType::PACKAGE, $name);
			$template = $this->packageDecorator->decorate($template, $name);
			$template->save();

			$this->progressBarFacade->advance();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getPackages());
	}

}
