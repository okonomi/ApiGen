<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\Template;

use ApiGen\Contracts\Generator\Decorator\Template\PackageDecoratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;


class PackageDecorator implements PackageDecoratorInterface
{

	/**
	 * @var ElementStorageInterface
	 */
	private $elementStorage;


	public function __construct(ElementStorageInterface $elementStorage)
	{
		$this->elementStorage = $elementStorage;
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate(TemplateInterface $template, $name)
	{
        $package = $this->elementStorage->getPackages()[$name];

		$template->addParameters([
			'namespace' => NULL,
			'package' => $name,
			'subpackages' => $this->getSubnamesForName($name, $template->getParameters()['packages'])
		]);
		$template = $this->loadTemplateWithElements($template, $package);
		return $template;
	}


	/**
	 * @param TemplateInterface $template
	 * @param array $elements
	 * @return TemplateInterface
	 */
	private function loadTemplateWithElements(TemplateInterface $template, $elements)
	{
		$template->addParameters([
			ElementsInterface::CLASSES => $elements[ElementsInterface::CLASSES],
			ElementsInterface::INTERFACES => $elements[ElementsInterface::INTERFACES],
			ElementsInterface::TRAITS => $elements[ElementsInterface::TRAITS],
			ElementsInterface::EXCEPTIONS => $elements[ElementsInterface::EXCEPTIONS],
			ElementsInterface::CONSTANTS => $elements[ElementsInterface::CONSTANTS],
			ElementsInterface::FUNCTIONS => $elements[ElementsInterface::FUNCTIONS]
		]);
		return $template;
	}


	/**
	 * @param string $name
	 * @param array $elements
	 * @return array
	 */
	private function getSubnamesForName($name, array $elements)
	{
		return array_filter($elements, function ($subname) use ($name) {
			$pattern = '~^' . preg_quote($name) . '\\\\[^\\\\]+$~';
			return (bool) preg_match($pattern, $subname);
		});
	}

}
