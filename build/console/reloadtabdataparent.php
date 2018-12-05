<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ReloadTabdataCommand extends Command {

	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			->setName('tabdata:reload')

			->setDescription('Reload tabdata file')

			->setHelp('This command allows you to reload tabdata file with write permissions for modules in the CRM')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		
                create_tab_data_file();

		$output->writeln("<info>Tabdata Parentabdata Reloaded</info>");
	}
}