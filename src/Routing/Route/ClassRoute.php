<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Contracts\Utils\NormalizerInterface;


class ClassRoute implements RouteInterface
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
		return 'class';
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($element = NULL)
	{
		$name = $element instanceof ClassReflectionInterface ? $element->getName() : $element;
		return sprintf('class-%s.html', $this->normalizer->urlize($name));
	}

}
