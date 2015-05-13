<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\Decorator\Template;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Generator\Decorator\Template\ElementDecoratorInterface;
use ApiGen\Contracts\Generator\Decorator\Template\NamespaceDecoratorInterface;
use ApiGen\Contracts\Generator\Decorator\Template\PackageDecoratorInterface;
use ApiGen\Contracts\Generator\Decorator\Template\TemplateDecoratorViaReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;


class ElementDecorator implements ElementDecoratorInterface, TemplateDecoratorViaReflectionInterface
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var NamespaceDecoratorInterface
	 */
	private $namespaceDecorator;

	/**
	 * @var PackageDecoratorInterface
	 */
	private $packageDecorator;


	public function __construct(
		Configuration $configuration,
		NamespaceDecoratorInterface $namespaceDecorator,
		PackageDecoratorInterface $packageDecorator
	) {
		$this->configuration = $configuration;
		$this->namespaceDecorator = $namespaceDecorator;
		$this->packageDecorator = $packageDecorator;
	}


	/**
	 * {@inheritdoc}
	 */
	public function decorate(TemplateInterface $template, ElementReflectionInterface $elementReflection)
	{
		if ($this->configuration->areNamespacesEnabled()) {
			$template = $this->namespaceDecorator->decorate($template, $elementReflection->getPseudoNamespaceName());

		} elseif ($this->configuration->arePackagesEnabled()) {
			$template = $this->packageDecorator->decorate($template, $elementReflection->getPseudoPackageName());
		}
		return $template;
	}

}
