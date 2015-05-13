<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Markup\PhpCodeHighlighter;

use ApiGen\Contracts\Markup\PhpCodeHighlighter\PhpCodeHighlighterInterface;
use FSHL\Highlighter;


class FshlSourceCodeHighlighter implements PhpCodeHighlighterInterface
{

	/**
	 * @var Highlighter
	 */
	private $highlighter;


	public function __construct(Highlighter $highlighter)
	{
		$this->highlighter = $highlighter;
	}


	/**
	 * {@inheritdoc}
	 */
	public function highlight($sourceCode)
	{
		$this->highlighter->setOptions(Highlighter::OPTION_TAB_INDENT);
		return $this->highlighter->highlight($sourceCode);
	}


	/**
	 * {@inheritdoc}
	 */
	public function highlightAndAddLineNumbers($sourceCode)
	{
		$this->highlighter->setOptions(Highlighter::OPTION_TAB_INDENT | Highlighter::OPTION_LINE_COUNTER);
		return $this->highlighter->highlight($sourceCode);
	}

}
