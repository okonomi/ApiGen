<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Theme;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Theme\Configuration\ThemeConfigurationInterface;
use ApiGen\Contracts\Theme\ThemeLoaderInterface;
use ApiGen\ThemeInstaller\Configuration;
use TokenReflection\Broker;
use TokenReflection\Broker\Backend\Memory;


class ThemeLoader implements ThemeLoaderInterface
{

	/**
	 * @var ThemeConfigurationInterface[]
	 */
	private $themes;


	/**
	 * {@inheritdoc}
	 */
	public function load()
	{
		if ($this->themes === NULL) {
			$this->themes = $this->findAndCreateThemeConfigurations($this->getThemesPath());
		}
		return $this->themes;
	}


	/**
	 * @return string
	 */
	private function getThemesPath()
	{
		$themesPath = __DIR__ . '/../../vendor' . Configuration::THEMES_RELATIVE_PATH;
		return realpath($themesPath);
	}


	/**
	 * @param string $path
	 * @return ThemeConfigurationInterface[]
	 */
	private function findAndCreateThemeConfigurations($path)
	{
		$parser = new Broker(new Memory, FALSE);
		$parser->processDirectory($path);

		$themeConfigurationClasses = [];
		foreach ($parser->getClasses() as $classReflection) {
			/** @var ClassReflectionInterface $classReflection */
			if ($classReflection->implementsInterface(ThemeConfigurationInterface::class)) {
				$className = $classReflection->getName();
				/** @var ThemeConfigurationInterface $themeConfiguration */
				$themeConfiguration = new $className;
				$themeConfigurationClasses[$themeConfiguration->getName()] = $themeConfiguration;
			}
		}

		return $themeConfigurationClasses;
	}

}
