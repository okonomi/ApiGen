<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Contracts\Utils\NormalizerInterface;
use ApiGen\Generator\GeneratorType;


class FunctionRoute implements RouteInterface
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
		return GeneratorType::FUNCTION_;
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($element = NULL)
	{
		/** @var FunctionReflectionInterface $element  */
		return sprintf('function-%s.html', $this->normalizer->urlize($element->getName()));
	}

}
