<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Contracts\Templating\TemplateFileManagerInterface;
use ApiGen\Contracts\Templating\TemplateFile\TemplateFileInterface;
use Exception;


class TemplateFileManager implements TemplateFileManagerInterface
{

	/**
	 * @var TemplateFileInterface[]
	 */
	private $templateFiles;


	/**
	 * {@inheritdoc}
	 */
	public function addTemplateFile(TemplateFileInterface $templateFile)
	{
		$this->templateFiles[$templateFile->getKey()] = $templateFile;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFile($key)
	{
		if (isset($this->templateFiles[$key])) {
			return $this->templateFiles[$key]->getFile();
		}

		throw new Exception(
			sprintf('File for "%s" was not found.', $key)
		);
	}

}
