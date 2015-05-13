<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Contracts\Console\Helper\ProgressBarFacadeInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\ConditionalGeneratorInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;


class GeneratorQueue implements GeneratorQueueInterface
{

	/**
	 * @var GeneratorInterface[]
	 */
	private $generators = [];

	/**
	 * @var ProgressBarFacadeInterface
	 */
	private $progressBarFacade;


	public function __construct(ProgressBarFacadeInterface $progressBarFacade)
	{
		$this->progressBarFacade = $progressBarFacade;
	}


	/**
	 * {@inheritdoc}
	 */
	public function addGenerator(GeneratorInterface $generator)
	{
		$this->generators[] = $generator;
	}


	/**
	 * {@inheritdoc}
	 */
	public function run()
	{
		$this->progressBarFacade->init($this->getStepCount());

		foreach ($this->getAllowedQueue() as $generator) {
			$generator->generate();
		}
	}


	/**
	 * @return GeneratorInterface[]
	 */
	private function getAllowedQueue()
	{
		return array_filter($this->generators, function (GeneratorInterface $generator) {
			if ($generator instanceof ConditionalGeneratorInterface) {
				return $generator->isAllowed();

			} else {
				return TRUE;
			}
		});
	}


	/**
	 * @return int
	 */
	private function getStepCount()
	{
		$steps = 0;
		foreach ($this->getAllowedQueue() as $templateGenerator) {
			if ($templateGenerator instanceof StepCounterInterface) {
				$steps += $templateGenerator->getStepCount();
			}
		}
		return $steps;
	}

}
