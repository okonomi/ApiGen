<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Contracts\Utils\NormalizerInterface;
use ApiGen\Generator\GeneratorType;


class SourceCodeRoute implements RouteInterface
{

	/**
	 * @var NormalizerInterface
	 */
	private $normalizer;


	public function __construct(NormalizerInterface $normalizer)
	{
		$this->normalizer = $normalizer;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return GeneratorType::SOURCE_CODE;
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($element = NULL)
	{
		/** @var ElementReflectionInterface $element */
		$file = '';
		if ($this->isDirectUrl($element)) {
			$elementName = $element->getName();
			if ($element instanceof ClassReflectionInterface) {
				$file = 'class-';

			} elseif ($element instanceof ConstantReflectionInterface) {
				$file = 'constant-';

			} elseif ($element instanceof FunctionReflectionInterface) {
				$file = 'function-';
			}

		} elseif ($element instanceof InClassInterface) {
			$elementName = $element->getDeclaringClass()->getName();
			$file = 'class-';
		}

		$file .= $this->normalizer->urlize($elementName);
		$url = sprintf('source-%s.html', $file);

//		$url .= $this->getElementLinesAnchor($element);

		return $url;
	}


	/**
	 * @return bool
	 */
	private function isDirectUrl(ElementReflectionInterface $element)
	{
		if ($element instanceof ClassReflectionInterface
			|| $element instanceof FunctionReflectionInterface
			|| $element instanceof ConstantReflectionInterface
		) {
			return TRUE;
		}
		return FALSE;
	}

}
