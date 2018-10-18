<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

class validatecodeCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('validatecode:show')

			// the short description shown while running "php bin/console list"
			->setDescription('Validate code')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to validate your code formatting..')

						 // configure an argument
			->addArgument('file', InputArgument::REQUIRED, 'path of the file')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$path = $input->getArgument("file");
		if (file_exists($path)) {
			$fileext = explode('.', strrev($path), 2);
			if (strrev($fileext[0]) == 'php') {
				$val = shell_exec("phpcs --standard=build/cbSR $path");
				echo $val;
			} elseif (strrev($fileext[0]) == 'js') {
				$val = shell_exec("eslint -c build/cbSR/eslintrc.js $path");
				echo $val;
			} else {
				$output->writeln("<info>Can't Validate. Not Valid Extension</info>");
			}
		} else {
			$output->writeln("<info>File does not exist</info>");
		}
	}
}