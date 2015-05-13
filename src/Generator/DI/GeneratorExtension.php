<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\DI;

use ApiGen\Contracts\Generator\Decorator\AnnotationDecoration\AnnotationDecorationInterface;
use ApiGen\Contracts\Generator\Decorator\AnnotationDecoratorInterface;
use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use Nette\DI\CompilerExtension;


class GeneratorExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/services.neon');
		$this->compiler->parseServices($builder, $config);
	}


	public function beforeCompile()
	{
		$this->loadAnnotationDecorator();
		$this->loadGeneratorQueue();
	}


	private function loadAnnotationDecorator()
	{
		$this->loadMediator(AnnotationDecoratorInterface::class, AnnotationDecorationInterface::class, 'addDecoration');
	}


	private function loadGeneratorQueue()
	{
		$this->loadMediator(GeneratorQueueInterface::class, GeneratorInterface::class, 'addGenerator');
	}


	/**
	 * @param string $mediator
	 * @param string $event
	 * @param string $adderMethod
	 */
	private function loadMediator($mediator, $client, $adderMethod)
	{
		$builder = $this->getContainerBuilder();

		$mediatorDefinition = $builder->getDefinition($builder->getByType($mediator));
		foreach ($builder->findByType($client) as $clientDefinition) {
			$mediatorDefinition->addSetup($adderMethod, ['@' . $clientDefinition->getClass()]);
		}
	}

}
