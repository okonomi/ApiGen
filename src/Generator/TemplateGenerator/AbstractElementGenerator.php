<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Console\Helper\ProgressBarFacadeInterface;
use ApiGen\Contracts\Generator\Decorator\Template\ElementDecoratorInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;


abstract class AbstractElementGenerator implements GeneratorInterface, StepCounterInterface
{

	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * @var TemplateFactoryInterface
	 */
	protected $templateFactory;

	/**
	 * @var ElementStorageInterface
	 */
	protected $elementStorage;

	/**
	 * @var ProgressBarFacadeInterface
	 */
	protected $progressBarFacade;

	/**
	 * @var ElementDecoratorInterface
	 */
	protected $elementDecorator;


	public function __construct(
		Configuration $configuration,
		TemplateFactoryInterface $templateFactory,
		ElementStorageInterface $elementStorage,
		ProgressBarFacadeInterface $progressBarFacade,
		ElementDecoratorInterface $classDecorator
	) {
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->elementStorage = $elementStorage;
		$this->progressBarFacade = $progressBarFacade;
		$this->elementDecorator = $classDecorator;
	}

}
