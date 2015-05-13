<?php

namespace ApiGen\Generator\Tests;

use ApiGen\Contracts\Generator\GeneratorQueueInterface;
use ApiGen\Generator\DI\GeneratorExtension;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Generator\TemplateGenerator\AnnotationGroupsGenerator;
use ApiGen\Tests\MethodInvoker;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;


class GeneratorExtensionTest extends PHPUnit_Framework_TestCase
{

	public function testLoadGeneratorQueue()
	{
		$extension = $this->getExtension();
		$extension->loadConfiguration();

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		MethodInvoker::callMethodOnObject($extension, 'loadGeneratorQueue');

		$definition = $builder->getDefinition($builder->getByType(GeneratorQueueInterface::class));
		$this->assertSame(GeneratorQueue::class, $definition->getClass());

		$filterService = $definition->getSetup()[1]->arguments[0];
		$command = $builder->getDefinition($builder->getServiceName($filterService));
		$this->assertSame(AnnotationGroupsGenerator::class, $command->getClass());
	}


	/**
	 * @return GeneratorExtension
	 */
	private function getExtension()
	{
		$extension = new GeneratorExtension;
		$extension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');
		return $extension;
	}

}
