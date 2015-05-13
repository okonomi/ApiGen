<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use ApiGen\Contracts\VersionInterface;


class Version implements VersionInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function getVersion()
	{
		return '4.2.0-dev';
	}

}
