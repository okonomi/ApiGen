<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\ConditionalGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;
use ApiGen\Tree;


class TreeGenerator implements ConditionalGeneratorInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;

	/**
	 * @var array
	 */
	private $processed = [];

	/**
	 * @var array[]
	 */
	private $treeStorage = [
		ElementsInterface::CLASSES => [],
		ElementsInterface::INTERFACES => [],
		ElementsInterface::TRAITS => [],
		ElementsInterface::EXCEPTIONS => []
	];

	/**
	 * @var ParserStorageInterface
	 */
	private $parserStorage;


	public function __construct(
		ConfigurationInterface $configuration,
		TemplateFactoryInterface $templateFactory,
		ParserStorageInterface $parserStorage
	) {
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->parserStorage = $parserStorage;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		$template = $this->templateFactory->create(GeneratorType::TREE);

		$classes = $this->parserStorage->getClasses();
		foreach ($classes as $className => $reflection) {
			if ($this->canBeProcessed($reflection)) {
				$this->addToTreeByReflection($reflection);
			}
		}

		$this->sortTreeStorageElements();

		$template->addParameters([
			'classTree' => new Tree($this->treeStorage[ElementsInterface::CLASSES], $classes),
			'interfaceTree' => new Tree($this->treeStorage[ElementsInterface::INTERFACES], $classes),
			'traitTree' => new Tree($this->treeStorage[ElementsInterface::TRAITS], $classes),
			'exceptionTree' => new Tree($this->treeStorage[ElementsInterface::EXCEPTIONS], $classes)
		]);

		$template->save();
	}


	/**
	 * {@inheritdoc}
	 */
	public function isAllowed()
	{
		return $this->configuration->isTreeAllowed();
	}


	/**
	 * @return bool
	 */
	private function canBeProcessed(ClassReflectionInterface $reflection)
	{
		if ( ! $reflection->isMain()) {
			return FALSE;
		}
		if ( ! $reflection->isDocumented()) {
			return FALSE;
		}
		if (isset($this->processed[$reflection->getName()])) {
			return FALSE;
		}
		return TRUE;
	}


	private function addToTreeByReflection(ClassReflectionInterface $reflection)
	{
		if ($reflection->getParentClassName() === NULL) {
			$type = $this->getTypeByReflection($reflection);
			$this->addToTreeByTypeAndName($type, $reflection->getName());

		} else {
			foreach (array_values(array_reverse($reflection->getParentClasses())) as $level => $parent) {
				$type = NULL;
				if ($level === 0) {
					// The topmost parent decides about the reflection type
					$type = $this->getTypeByReflection($reflection);
				}

				/** @var ClassReflectionInterface $parent */
				$parentName = $parent->getName();
				if ( ! isset($this->treeStorage[$type][$parentName])) {
					$this->addToTreeByTypeAndName($type, $parentName);
				}
			}
		}
	}


	/**
	 * @return string
	 */
	private function getTypeByReflection(ClassReflectionInterface $reflection)
	{
		if ($reflection->isInterface()) {
			return ElementsInterface::INTERFACES;

		} elseif ($reflection->isTrait()) {
			return ElementsInterface::TRAITS;

		} elseif ($reflection->isException()) {
			return ElementsInterface::EXCEPTIONS;

		} else {
			return ElementsInterface::CLASSES;
		}
	}


	/**
	 * @param string $type
	 * @param string $name
	 */
	private function addToTreeByTypeAndName($type, $name)
	{
		$this->treeStorage[$type][$name] = [];
		$this->processed[$name] = TRUE;
	}


	private function sortTreeStorageElements()
	{
		foreach ($this->treeStorage as $key => $elements) {
			ksort($elements, SORT_STRING);
			$this->treeStorage[$key] = $elements;
		}
	}

}
