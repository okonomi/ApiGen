<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;
use ApiGen\Generator\GeneratorType;


class InterfaceGenerator extends AbstractElementGenerator
{

	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->elementStorage->getInterfaces() as $name => $classReflection) {
			$template = $this->templateFactory->create(GeneratorType::CLASS_, $classReflection);
			$template = $this->elementDecorator->decorate($template, $classReflection);
			$template = $this->loadTemplateWithParameters($template, $classReflection);
			$template->save();

			$this->progressBarFacade->advance();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getStepCount()
	{
		return count($this->elementStorage->getInterfaces());
	}


	/**
	 * @return TemplateInterface
	 */
	private function loadTemplateWithParameters(TemplateInterface $template, ClassReflectionInterface $class)
	{
		$template->addParameters([
			'class' => $class,
			'tree' => array_merge(array_reverse($class->getParentClasses()), [$class]),
			'directSubClasses' => $class->getDirectSubClasses(),
			'indirectSubClasses' => $class->getIndirectSubClasses(),
			'directImplementers' => $class->getDirectImplementers(),
			'indirectImplementers' => $class->getIndirectImplementers(),
			'directUsers' => $class->getDirectUsers(),
			'indirectUsers' => $class->getIndirectUsers(),
		]);
		return $template;
	}

}
