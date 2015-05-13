<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\ElementLink;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use ApiGen\Contracts\Routing\RouterInterface;
use ApiGen\Contracts\Routing\ElementLink\ElementLinkInterface;
use Nette\Utils\Html;


class ConstantElementLink implements ElementLinkInterface
{

	/**
	 * @var LinkBuilderInterface
	 */
	private $linkBuilder;

	/**
	 * @var RouterInterface
	 */
	private $router;


	public function __construct(LinkBuilderInterface $linkBuilder, RouterInterface $router)
	{
		$this->linkBuilder = $linkBuilder;
		$this->router = $router;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return ConstantReflectionInterface::class;
	}


	/**
	 * {@inheritdoc}
	 */
	public function buildLink(ElementReflectionInterface $element, array $classes = [])
	{
		$url = $this->router->constructUrl('constant', $element);
		$text = $this->getGlobalConstantName($element);
		return $this->linkBuilder->build($url, $text, FALSE, $classes);
	}


	/**
	 * @return string|Html
	 */
	private function getGlobalConstantName(ConstantReflectionInterface $reflectionConstant)
	{
		if ($reflectionConstant->inNamespace()) {
			return $reflectionConstant->getNamespaceName() . '\\' .
			Html::el('b')->setText($reflectionConstant->getShortName());

		} else {
			return Html::el('b')->setText($reflectionConstant->getName());
		}
	}

}
