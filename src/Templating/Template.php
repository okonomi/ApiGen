<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Contracts\Templating\Template\TemplateInterface;
use Latte\Engine;


class Template implements TemplateInterface
{

	/**
	 * @var Engine
	 */
	private $latteEngine;

	/**
	 * @var string
	 */
	private $file;

	/**
	 * @var mixed[]
	 */
	private $parameters = [];

	/**
	 * @var string
	 */
	private $savePath;


	public function __construct(Engine $latteEngine)
	{
		$this->latteEngine = $latteEngine;
	}


	/**
	 * {@inheritdoc}
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * {@inheritdoc}
	 */
	public function addParameters(array $parameters)
	{
		$this->parameters = $parameters + $this->parameters;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getParameter($name)
	{
		if (isset($this->parameters[$name])) {
			return $this->parameters[$name];
		}

		throw new \Exception(
			sprintf('Parameter "%s is not set in template.', $name)
		);
	}


	/**
	 * {@inheritdoc}
	 */
	public function setSavePath($path)
	{
		$this->savePath = $path;
	}


	/**
	 * {@inheritdoc}
	 */
	public function save()
	{
		$dir = dirname($this->savePath);
		if ( ! is_dir($dir)) {
			mkdir($dir, 0755, TRUE);
		}

		$content = $this->latteEngine->renderToString($this->file, $this->parameters);
		file_put_contents($this->savePath, $content);
	}

}
