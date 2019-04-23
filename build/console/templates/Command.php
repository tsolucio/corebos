<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DummyCommand extends Command {

	protected $root_path = __DIR__ . "/../../";

	protected function configure() {

		$this
			->setName('[command_name]')

			->setDescription('[command_description]')

			->setHelp('[command_help]')

			// Simple Argument
			->addArgument('[name]', InputArgument::REQUIRED, '[argument_description]')

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$label 	= $input->getArgument("[name]");

		// Your Logic here

		$output->writeln("<info>Executed Sucessfuly</info>");
	}
}