<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Routing\LinkBuilder;

use ApiGen\Contracts\Routing\LinkBuilder\LinkBuilderInterface;
use Latte\Runtime\Filters;
use Nette\Utils\Html;


class LinkBuilder implements LinkBuilderInterface
{

	/**
	 * {@inheritdoc}
	 */
	public function build($url, $text, $escape = TRUE, array $classes = [])
	{
		return Html::el('a')->href($url)
			->setHtml($escape ? Filters::escapeHtml($text) : $text)
			->addAttributes(['class' => $classes])
			->render();
	}

}
