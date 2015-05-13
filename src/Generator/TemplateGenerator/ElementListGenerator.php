<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerator;

use ApiGen\Contracts\Generator\TemplateGenerator\GeneratorInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Generator\GeneratorType;


class ElementListGenerator implements GeneratorInterface
{

	/**
	 * @var TemplateFactoryInterface
	 */
	private $templateFactory;


	public function __construct(TemplateFactoryInterface $templateFactory)
	{
		$this->templateFactory = $templateFactory;
	}


	/**
	 * {@inheritdoc}
	 */
	public function generate()
	{
		$template = $this->templateFactory->create(GeneratorType::ELEMENT_LIST);
		$template->save();
	}

}
