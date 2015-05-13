<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\DI;

use ApiGen\Contracts\Templating\TemplateFile\TemplateFileInterface;
use ApiGen\Contracts\Templating\TemplateFileManagerInterface;
use Nette\DI\CompilerExtension;


class TemplatingExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/services.neon');
		$this->compiler->parseServices($builder, $config);
	}


	public function beforeCompile()
	{
		$this->loadTemplateFiles();
	}


	private function loadTemplateFiles()
	{
		$builder = $this->getContainerBuilder();
		$builder->prepareClassList();

		$templateFileManager = $builder->getDefinition($builder->getByType(TemplateFileManagerInterface::class));
		foreach ($builder->findByType(TemplateFileInterface::class) as $templateFileDefinition) {
			$templateFileManager->addSetup('addTemplateFile', ['@' . $templateFileDefinition->getClass()]);
		}
	}

}
