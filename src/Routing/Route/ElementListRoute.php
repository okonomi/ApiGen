<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\Route;

use ApiGen\Contracts\Routing\RouteInterface;
use ApiGen\Generator\GeneratorType;


class ElementListRoute implements RouteInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function getKey()
	{
		return GeneratorType::ELEMENT_LIST;
	}


	/**
	 * {@inheritdoc}
	 */
	public function constructUrl($element = NULL)
	{
		return 'elementlist.js';
	}

}