<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Markup\Matcher;

use ApiGen\Contracts\Markup\Matcher\MatcherInterface;
use Gherkins\RegExpBuilderPHP\RegExpBuilder;


class Matcher implements MatcherInterface
{

	/**
	 * @var string
	 */
	const PATTERN_SURROUNDING_TAG = '/^<%s[^>]*>(.*)<\/%s[^>]*>$/i';


	/**
	 * {@inheritdoc}
	 */
	public function isSurroundedByHtmlTag($text, $tag)
	{
		$surroundingTagWithLineBreaks = (new RegExpBuilder)->startOfInput()
			->find(sprintf('<%s', $tag))
			->anything()
			->min(0)->lineBreaks()
			->find(sprintf('</%s>', $tag))
			->endOfInput()
			->getRegExp();

		return $surroundingTagWithLineBreaks->matches($text);
	}


	/**
	 * {@inheritdoc}
	 */
	public function removeSurroundingTag($text, $tag)
	{
		$tagPattern = sprintf(self::PATTERN_SURROUNDING_TAG, $tag, $tag);
		$text = preg_replace($tagPattern, '$1', $text);
		return trim($text);
	}

}
