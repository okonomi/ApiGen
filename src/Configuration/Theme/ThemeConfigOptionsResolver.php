<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Configuration\Theme;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ThemeConfigOptionsResolver
{

	/**
	 * @var array
	 */
	private $defaults = [
		'name' => '',
		'options' => [
			'elementDetailsCollapsed' => TRUE,
			'elementsOrder' => 'natural' # or: alphabetical
		],
		TCO::RESOURCES => [
			'resources' => 'resources'
		],
		TCO::TEMPLATES => [
			TCO::OVERVIEW => [
				'filename' => 'index.html',
				'template' => 'overview.latte'
			],
			TCO::COMBINED => [
				'filename' => 'resources/combined.js',
				'template' => 'combined.js.latte'
			],
			TCO::ELEMENT_LIST => [
				'filename' => 'elementlist.js',
				'template' => 'elementlist.js.latte'
			],
			TCO::PACKAGE => [
				'filename' => 'package-%s.html',
				'template' => 'package.latte'
			],
			TCO::T_NAMESPACE => [
				'filename' => 'namespace-%s.html',
				'template' => 'namespace.latte'
			],
			TCO::T_CLASS => [
				'filename' => 'class-%s.html',
				'template' => 'class.latte'
			],
			TCO::T_CONSTANT => [
				'filename' => 'constant-%s.html',
				'template' => 'constant.latte'
			],
			TCO::T_FUNCTION => [
				'filename' => 'function-%s.html',
				'template' => 'function.latte'
			],
			TCO::ANNOTATION_GROUP => [
				'filename' => 'annotation-group-%s.html',
				'template' => 'annotation-group.latte'
			],
			TCO::SOURCE => [
				'filename' => 'source-%s.html',
				'template' => 'source.latte'
			],
			TCO::TREE => [
				'filename' => 'tree.html',
				'template' => 'tree.latte'
			],
			TCO::OPENSEARCH => [
				'filename' => 'opensearch.xml',
				'template' => 'opensearch.xml.latte'
			]
		],
		TCO::TEMPLATES_PATH => ''
	];

	/**
	 * @var OptionsResolver
	 */
	private $resolver;


	/**
	 * @return array
	 */
	public function resolve(array $options)
	{
		$this->resolver = new OptionsResolver;
		$this->setDefaults();
		$this->setNormalizers();
		return $this->resolver->resolve($options);
	}


	private function setDefaults()
	{
		$this->resolver->setDefaults($this->defaults);
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizer(TCO::RESOURCES, function (Options $options, $resources) {
			$absolutizedResources = [];
			foreach ($resources as $key => $resource) {
				$key = $options['templatesPath'] . '/' . $key;
				$absolutizedResources[$key] = $resource;
			}
			return $absolutizedResources;
		});

		$this->resolver->setNormalizer(TCO::TEMPLATES, function (Options $options, $value) {
			return $this->makeTemplatePathsAbsolute($value, $options);
		});
	}


	/**
	 * @return array
	 */
	private function makeTemplatePathsAbsolute(array $value, Options $options)
	{
		foreach ($value as $type => $settings) {
			$filePath = $options[TCO::TEMPLATES_PATH] . '/' . $settings['template'];
			$value[$type]['template'] = $filePath;
			$this->validateFileExistence($filePath, $type);
		}
		return $value;
	}


	/**
	 * @param string $file
	 * @param string $type
	 */
	private function validateFileExistence($file, $type)
	{
		if ( ! is_file($file)) {
			throw new ConfigurationException(
				sprintf('Template for "%s" was not found in "%s"', $type, $file)
			);
		}
	}

}
