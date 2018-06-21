<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command {

	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			->setName('command:create')

			->setDescription('Create a new Command')

			->setHelp('Create a new Command directly from the terminal')

			->addArgument('name', InputArgument::REQUIRED, 'command name')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$command_template = $this->templates_path . "Command.php";
		$command_content = file_get_contents($command_template);

		$command_name = $input->getArgument('name');

		$this->replace["DummyCommand"] = $command_name . "Command";

		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $command_content);
		file_put_contents(__DIR__ . "/" . $command_name . ".php", $new_content);
		$output->writeln("<info>Command ". $command_name . " created successfuly</info>");
	}
}