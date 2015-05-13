<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Markup\Markdown;

use ApiGen\Contracts\Markup\Markdown\MarkdownParserInterface;
use ApiGen\Contracts\Markup\PhpCodeHighlighter\PhpCodeHighlighterInterface;
use ApiGen\Markup\Matcher\Matcher;
use League\CommonMark\CommonMarkConverter;


class CommonMarkParser implements MarkdownParserInterface
{

	/**
	 * @var string
	 */
	const PHP_CODE_PATTERN = '~<(code|pre)>(.+?)</\1>|```php\s(.+?)\n```~s';

	/**
	 * @var CommonMarkConverter
	 */
	private $commonMarkConverter;

	/**
	 * @var PhpCodeHighlighterInterface
	 */
	private $phpCodeHighlighter;


	/**
	 * @var Matcher
	 */
	private $matcher;


	public function __construct(
		CommonMarkConverter $commonMarkConverter,
		PhpCodeHighlighterInterface $phpCodeHighlighter,
		Matcher $matcher
	) {
		$this->phpCodeHighlighter = $phpCodeHighlighter;
		$this->commonMarkConverter = $commonMarkConverter;
		$this->matcher = $matcher;
	}


	/**
	 * {@inheritdoc}
	 */
	public function parse($content)
	{
		$html = $this->commonMarkConverter->convertToHtml($content);
		$html = $this->highlightCode($html);
		$html = $this->removeSurroundingParagraphByMarkdown($html, $content);
		return $html;
	}


	/**
	 * @param string $content
	 * @return string
	 */
	private function highlightCode($content)
	{
		preg_replace_callback(self::PHP_CODE_PATTERN, function ($match) {
			$highlightedContent = $this->phpCodeHighlighter->highlight(trim(isset($match[3]) ? $match[3] : $match[2]));
			if ($this->matcher->isSurroundedByHtmlTag($highlightedContent, 'code') === FALSE) {
				$highlightedContent = sprintf('<code>%s</code>', $highlightedContent);
			}
			return $highlightedContent;
		}, $content);
		return $content;
	}


	/**
	 * @param string $html
	 * @param string $content
	 * @return string
	 */
	private function removeSurroundingParagraphByMarkdown($html, $content)
	{
		if ($this->matcher->isSurroundedByHtmlTag($content, 'p') === FALSE) {
			$html = $this->matcher->removeSurroundingTag($html, 'p');
		}
		$html = trim($html);
		return $html;
	}

}
