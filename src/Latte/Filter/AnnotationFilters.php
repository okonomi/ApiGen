<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Latte\Filter;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\Decorator\AnnotationDecoratorInterface;
use ApiGen\Contracts\Latte\DI\FilterProviderInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use Nette\Utils\Strings;


class AnnotationFilters implements FilterProviderInterface
{

	/**
	 * @var array
	 */
	private $rename = [
		'usedby' => 'used by'
	];

	/**
	 * @var string[]
	 */
	private $remove = [
		'package', 'subpackage', 'property', 'property-read', 'property-write', 'method', 'abstract', 'access',
		'final', 'filesource', 'global', 'name', 'static', 'staticvar'
	];

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var AnnotationDecoratorInterface
	 */
	private $annotationDecorator;


	public function __construct(
		ConfigurationInterface $configuration,
		AnnotationDecoratorInterface $annotationDecorator
	) {
		$this->configuration = $configuration;
		$this->annotationDecorator = $annotationDecorator;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return [
			'annotation' => function ($value, $name, ElementReflectionInterface $reflectionElement) {
				return $this->annotation($value, $name, $reflectionElement);
			},
			'annotationBeautify' => function ($name) {
				return $this->annotationBeautify($name);
			},
			'annotationFilter' => function (array $annotations, array $customToRemove = []) {
				return $this->annotationFilter($annotations, $customToRemove);
			}
		];
	}


	/**
	 * @param string $content
	 * @param string $name
	 * @param ElementReflectionInterface $elementReflection
	 * @return string
	 */
	private function annotation($content, $name, ElementReflectionInterface $elementReflection)
	{
		return $this->annotationDecorator->decorate($name, $content, $elementReflection);
	}


	/**
	 * @param string $name
	 * @return string
	 */
	private function annotationBeautify($name)
	{
		if (isset($this->rename[$name])) {
			$name = $this->rename[$name];
		}
		return Strings::firstUpper($name);
	}


	/**
	 * @return array
	 */
	private function annotationFilter(array $annotations, array $customToRemove = [])
	{
		$remove = array_merge($this->remove, $customToRemove);
		$annotations = $this->filterOut($annotations, $remove);

		if ( ! $this->configuration->isInternalDocumented()) {
			unset($annotations['internal']);
		}

		if (in_array('todo', $this->configuration->getAnnotationGroups())) {
			unset($annotations['todo']);
		}

		return $annotations;
	}


	/**
	 * @return array
	 */
	private function filterOut(array $annotations, array $toRemove)
	{
		foreach ($toRemove as $annotation) {
			unset($annotations[$annotation]);
		}
		return $annotations;
	}

}
