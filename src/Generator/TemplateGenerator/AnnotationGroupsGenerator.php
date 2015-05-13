<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Templating\Template\TemplateInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;


class AnnotationGroupsGenerator implements GeneratorInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;

	/**
	 * @var ElementExtractorInterface
	 */
	private $elementExtractor;


	public function __construct(
		ConfigurationInterface $configuration,
		TemplateFactoryInterface $templateFactory,
		ElementExtractorInterface $elementExtractor
	) {
		$this->configuration = $configuration;
		$this->templateFactory = $templateFactory;
		$this->elementExtractor = $elementExtractor;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->configuration->getAnnotationGroups() as $annotation) {
			$template = $this->templateFactory->create(GeneratorType::ANNOTATION_GROUPS, $annotation);
			$template = $this->setElementsWithAnnotationToTemplate($template, $annotation);
			$template->save();
		}
	}


	/**
	 * @param TemplateInterface $template
	 * @param string $annotation
	 * @return TemplateInterface
	 */
	private function setElementsWithAnnotationToTemplate(TemplateInterface $template, $annotation)
	{
		$elements = $this->elementExtractor->extractElementsByAnnotation($annotation);

		$template->addParameters([
			'annotation' => $annotation,
			'hasElements' => (bool) count(array_filter($elements, 'count')),
			'annotationClasses' => $elements[ElementsInterface::CLASSES],
			'annotationInterfaces' => $elements[ElementsInterface::INTERFACES],
			'annotationTraits' => $elements[ElementsInterface::TRAITS],
			'annotationExceptions' => $elements[ElementsInterface::EXCEPTIONS],
			'annotationConstants' => $elements[ElementsInterface::CONSTANTS],
			'annotationMethods' => $elements[ElementsInterface::METHODS],
			'annotationFunctions' => $elements[ElementsInterface::FUNCTIONS],
			'annotationProperties' => $elements[ElementsInterface::PROPERTIES]
		]);

		return $template;
	}

}
