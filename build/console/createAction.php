<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CreateActionCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('action:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new action.')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create a cbupdater...')

			// configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'name of the action')

			// configure an argument
			->addArgument('module', InputArgument::REQUIRED, 'module name')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$action_name = $input->getArgument("name");
		$module_name = $input->getArgument("module");

		$class_path = $this->templates_path . "Action.php";
		$class_content = file_get_contents($class_path);

		$action_path = $this->root_path . "modules/{$module_name}/actions/";
		$action_file_path = $action_path . "{$action_name}.php";

		$this->replace['DummyClass'] = $action_name;
		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $class_content);

		if (!is_dir($action_path)) {
			mkdir($action_path, 0755);
		}

		file_put_contents($action_file_path, $new_content);
		$output->writeln("<info>Created Sucessfuly</info>");
	}
}