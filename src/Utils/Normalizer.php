<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Utils;

use ApiGen\Contracts\Utils\NormalizerInterface;


class Normalizer implements NormalizerInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function urlize($string)
	{
		return preg_replace('~[^\w]~', '.', $string);
	}


	/**
	 * {@inheritdoc}
	 */
	public function normalizerType($name, $trimNamespaceSeparator = TRUE)
	{
		$names = [
			'int' => 'integer',
			'bool' => 'boolean',
			'double' => 'float',
			'void' => '',
			'FALSE' => 'false',
			'TRUE' => 'true',
			'NULL' => 'null',
			'callback' => 'callable'
		];

		// Simple type
		if (isset($names[$name])) {
			return $names[$name];
		}

		// Class, constant or function
		return $trimNamespaceSeparator ? ltrim($name, '\\') : $name;
	}

}
