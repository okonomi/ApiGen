<?php

/**
 * This file is part of apigen
 *
 * Copyright (c) 2014 Pears Health Cyber, s.r.o. (http://pearshealthcyber.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Latte\Tests;

use ApiGen\Latte\DI\LatteExtension;
use ApiGen\Latte\Filter\AnnotationFilters;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;


class LatteExtensionTest extends PHPUnit_Framework_TestCase
{

	public function testLoadLatteFilters()
	{
		$extension = $this->getExtension();
		$extension->loadConfiguration();

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		MethodInvoker::callMethodOnObject($extension, 'loadLatteFilters');

		$definition = $builder->getDefinition($builder->getByType(Engine::class));
		$this->assertSame(Engine::class, $definition->getClass());

		$filterService = $definition->getSetup()[1]->arguments[0];
		$this->assertSame('@' . AnnotationFilters::class, $filterService);
	}


	/**
	 * @return LatteExtension
	 */
	private function getExtension()
	{
		$extension = new LatteExtension;
		$extension->setCompiler($this->getCompiler(), 'compiler');
		return $extension;
	}


	/**
	 * @return Compiler
	 */
	private function getCompiler()
	{
		$compiler = new Compiler(new ContainerBuilder);
		$compiler->compile(['parameters' => [
			'tempDir' =>TEMP_DIR
		]], NULL, NULL);
		return $compiler;
	}

}
