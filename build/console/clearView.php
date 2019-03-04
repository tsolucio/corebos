<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearViewCommand extends Command {

	protected $templates_path = __DIR__ . "/../../Smarty/templates_c";

	protected function configure() {

		$this
			->setName('smarty:clear')

			->setDescription('Clear cached smarty templates')

			->setHelp('This command allows you delete all cached templates under Smarty/templates_c');
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		if (!is_writable($this->templates_path)) {
			$output->writeln("<error>Smarty/templates_c is not writable</error>");
			return;
		}

		if (!is_dir($this->templates_path . "/")) {
			$output->writeln("<error>Smarty/templates_c does not exist</error>");
			return;
		}

		$files = glob($this->templates_path . '/*');

		if (count($files) == 0) {
			$output->writeln("<comment>Smarty cache is empty</comment>");
			return;
		}
	}
}