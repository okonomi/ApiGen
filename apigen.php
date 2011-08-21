#!/usr/bin/env php
<?php

/**
 * ApiGen 2.1 dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use Nette\Diagnostics\Debugger;

// Set dirs
define('PEAR_PHP_DIR', '@php_dir@');
define('PEAR_DATA_DIR', '@data_dir@');

if (false === strpos(PEAR_PHP_DIR, '@php_dir')) {
	// PEAR package
	define('ROOT_DIR', PEAR_PHP_DIR);
	define('TEMPLATE_DIR', PEAR_DATA_DIR . '/ApiGen/templates');

	require ROOT_DIR . '/Nette/loader.php';
	require PEAR_PHP_DIR . '/ApiGen/libs/Texy/texy.min.php';
} else {
	// Downloaded package
	define('ROOT_DIR', __DIR__);
	define('TEMPLATE_DIR', ROOT_DIR . '/templates');

	require ROOT_DIR . '/libs/Nette/nette.min.php';
	require ROOT_DIR . '/libs/FSHL/fshl.min.php';
	require ROOT_DIR . '/libs/Texy/texy.min.php';
	require ROOT_DIR . '/libs/TokenReflection/tokenreflection.min.php';
}

// Autoload
spl_autoload_register(function($class) {
	require_once sprintf('%s%s%s.php', ROOT_DIR, DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $class));
});

try {

	Debugger::$strictMode = true;
	Debugger::enable();
	Debugger::timer();

	$config = new Config();
	$generator = new Generator($config);

	// Help
	if ($config->isHelpRequested()) {
		echo $generator->colorize($generator->getHeader());
		echo $generator->colorize($config->getHelp());
		die();
	}

	// Start
	$config->parse();

	if ($config->debug) {
		Debugger::enable(Debugger::DEVELOPMENT);
	}

	$generator->output($generator->getHeader());

	// Scan
	if (count($config->source) > 1) {
		$generator->output(sprintf("Scanning\n @value@%s@c\n", implode("\n ", $config->source)));
	} else {
		$generator->output(sprintf("Scanning @value@%s@c\n", $config->source[0]));
	}
	if (count($config->exclude) > 1) {
		$generator->output(sprintf("Excluding\n @value@%s@c\n", implode("\n ", $config->exclude)));
	} elseif (!empty($config->exclude)) {
		$generator->output(sprintf("Excluding @value@%s@c\n", $config->exclude[0]));
	}
	$parsed = $generator->parse();
	$generator->output(vsprintf("Found @count@%d@c classes, @count@%d@c constants, @count@%d@c functions and other @count@%d@c used PHP internal classes\n", array_slice($parsed, 0, 4)));
	$generator->output(vsprintf("Documentation for @count@%d@c classes, @count@%d@c constants, @count@%d@c functions and other @count@%d@c used PHP internal classes will be generated\n", array_slice($parsed, 4, 4)));

	// Generating
	$generator->output(sprintf("Using template config file @value@%s@c\n", $config->templateConfig));

	if ($config->wipeout && is_dir($config->destination)) {
		$generator->output("Wiping out destination directory\n");
		if (!$generator->wipeOutDestination()) {
			throw new Exception('Cannot wipe out destination directory');
		}
	}

	$generator->output(sprintf("Generating to directory @value@%s@c\n", $config->destination));
	$skipping = array_merge($config->skipDocPath, $config->skipDocPrefix);
	if (count($skipping) > 1) {
		$generator->output(sprintf("Will not generate documentation for\n @value@%s@c\n", implode("\n ", $skipping)));
	} elseif (!empty($skipping)) {
		$generator->output(sprintf("Will not generate documentation for @value@%s@c\n", $skipping[0]));
	}
	$generator->generate();

	// End
	$generator->output(sprintf("Done. Total time: @count@%d@c seconds, used: @count@%d@c MB RAM\n", Debugger::timer(), round(memory_get_peak_usage(true) / 1024 / 1024)));

} catch (\Exception $e) {
	$invalidConfig = $e instanceof Exception && Exception::INVALID_CONFIG === $e->getCode();
	if ($invalidConfig) {
		echo $generator->colorize($generator->getHeader());
	}

	if (!empty($config) && $config->debug) {
		do {
			echo $generator->colorize(sprintf("\n@error@%s@c", $e->getMessage()));
			$trace = $e->getTraceAsString();
		} while ($e = $e->getPrevious());

		printf("\n\n%s\n\n", $trace);
	} else {
		echo $generator->colorize(sprintf("\n@error@%s@c\n\n", $e->getMessage()));
	}

	// Help only for invalid configuration
	if ($invalidConfig) {
		echo $generator->colorize($config->getHelp());
	}

	die(1);
}