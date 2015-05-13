<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Console\Helper\ProgressBarFacadeInterface;
use ApiGen\Contracts\Generator\Resolvers\RelativePathResolverInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerator\ConditionalGeneratorInterface;
use ApiGen\Contracts\Markup\PhpCodeHighlighter\PhpCodeHighlighterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;


class SourceCodeGenerator implements ConditionalGeneratorInterface, StepCounterInterface
{

	/**
	 * @var ConfigurationInterface
	 */
	private $configuration;

	/**
	 * @var ElementStorageInterface
	 */
	private $elementStorage;

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;

	/**
	 * @var RelativePathResolverInterface
	 */
	private $relativePathResolver;

	/**
	 * @var PhpCodeHighlighterInterface
	 */
	private $phpCodeHighlighter;

	/**
	 * @var ProgressBarFacadeInterface
	 */
	private $progressBarFacade;


	public function __construct(
		ConfigurationInterface $configuration,
		ElementStorageInterface $elementStorage,
		TemplateFactoryInterface $templateFactory,
		RelativePathResolverInterface $relativePathResolver,
		PhpCodeHighlighterInterface $phpCodeHighlighter,
		ProgressBarFacadeInterface $progressBarFacade
	) {
		$this->configuration = $configuration;
		$this->elementStorage = $elementStorage;
		$this->templateFactory = $templateFactory;
		$this->relativePathResolver = $relativePathResolver;
		$this->phpCodeHighlighter = $phpCodeHighlighter;
		$this->progressBarFacade = $progressBarFacade;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			foreach ($elementList as $element) {
				/** @var ElementReflectionInterface $element */
				if ($element->isTokenized()) {
					$this->generateForElement($element);

					$this->progressBarFacade->advance();
				}
			}
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function getStepCount()
	{
		$tokenizedFilter = function (ClassReflectionInterface $class) {
			return $class->isTokenized();
		};

		$count = count(array_filter($this->elementStorage->getClasses(), $tokenizedFilter))
			+ count(array_filter($this->elementStorage->getInterfaces(), $tokenizedFilter))
			+ count(array_filter($this->elementStorage->getTraits(), $tokenizedFilter))
			+ count(array_filter($this->elementStorage->getExceptions(), $tokenizedFilter))
			+ count($this->elementStorage->getConstants())
			+ count($this->elementStorage->getFunctions());

		return $count;
	}


	/**
	 * {@inheritdoc}
	 */
	public function isAllowed()
	{
		return $this->configuration->shouldGenerateSourceCode();
	}


	private function generateForElement(ElementReflectionInterface $element)
	{
		$template = $this->templateFactory->create(GeneratorType::SOURCE_CODE, $element);
		$template->addParameters([
			'fileName' => $this->relativePathResolver->getRelativePath($element->getFileName()),
			'source' => $this->getHighlightedCodeFromElement($element)
		]);
		$template->save();
	}


	/**
	 * @return string
	 */
	private function getHighlightedCodeFromElement(ElementReflectionInterface $element)
	{
		$content = file_get_contents($element->getFileName());
		return $this->phpCodeHighlighter->highlightAndAddLineNumbers($content);
	}

}
