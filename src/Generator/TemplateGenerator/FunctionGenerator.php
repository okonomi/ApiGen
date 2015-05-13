<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Generator\GeneratorType;


class FunctionGenerator extends AbstractElementGenerator
{

	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->elementStorage->getFunctions() as $name => $functionReflection) {
			$template = $this->templateFactory->create(GeneratorType::FUNCTION_, $name);
			$template = $this->elementDecorator->decorate($template, $functionReflection);
			$template->addParameters(['function' => $functionReflection]);
			$template->save();

			$this->progressBarFacade->advance();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getFunctions());
	}

}
