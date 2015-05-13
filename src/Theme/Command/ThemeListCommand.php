<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Theme\Command;

use ApiGen\Theme\ThemeLoader;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ThemeListCommand extends Command
{

	/**
	 * @var ThemeLoader
	 */
	private $themeLoader;


	public function __construct(ThemeLoader $themeLoader)
	{
		parent::__construct();
		$this->themeLoader = $themeLoader;
	}


	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this->setName('theme-list')
			->setAliases(['themelist'])
			->setDescription('Shows list of all available themes.');
	}


	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$themes = $this->themeLoader->load();
		try {
			$output->writeln('<comment>Available themes:</comment>');
			foreach ($themes as $key => $theme) {
				$output->writeln(' ' . $theme->getName());
			}
			return 0;

		} catch (Exception $exception) {
			$output->writeln(
				sprintf(PHP_EOL . '<error>%s</error>', $exception->getMessage())
			);
			return 1;
		}
	}



}
