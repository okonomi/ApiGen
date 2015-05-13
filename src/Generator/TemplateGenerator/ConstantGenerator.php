<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Generator\GeneratorType;


class ConstantGenerator extends AbstractElementGenerator
{

	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->elementStorage->getConstants() as $name => $constantReflection) {
			$template = $this->templateFactory->create(GeneratorType::CONSTANT_, $constantReflection);
			$template = $this->elementDecorator->decorate($template, $constantReflection);
			$template->addParameters(['constant' => $constantReflection]);
			$template->save();

			$this->progressBarFacade->advance();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getConstants());
	}

}
