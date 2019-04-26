<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class CreateCronJobCommand extends Command {

	protected $root_path = __DIR__ . "/../../";
	protected $templates_path = __DIR__ . "/templates/";
	protected $replace = [];

	protected function configure() {

		$this
			->setName('cronjob:create')

			->setDescription('creates a scheduler script')

			->setHelp('')

			->addArgument('name', InputArgument::REQUIRED, 'module for which this cron will be executed')

			->addArgument('module', InputArgument::REQUIRED, 'module for which this cron will be executed')

			->addArgument('frequency', InputArgument::REQUIRED, 'add execution frequency in seconds')

			->addArgument('script_name', InputArgument::REQUIRED, 'cron script name')

			->addArgument('author', InputArgument::REQUIRED, 'author for cbupdater')

			->addArgument('description', InputArgument::REQUIRED, 'cron description')

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$name 			= $input->getArgument("name");
		$description 	= $input->getArgument("description");
		$module 		= $input->getArgument("module");
		$script_name 	= $input->getArgument("script_name");
		$author			= $input->getArgument("author");
		$frequency		= $input->getArgument("frequency");

		$cron_path = $this->root_path . "cron/modules/" . $module;

		if (!is_dir($cron_path)) {
			mkdir($cron_path, 0755);
		}

		$script_name = str_replace(' ', '', $script_name) . ".service";
		copy($this->templates_path . "/Cron.php", $cron_path . "/" . $script_name);

		// Register Cron
		$this->replace['NAME'] = $name;
		$this->replace["PATH"] =   "cron/modules/" . $module . "/" . $script_name;
		$this->replace["MODULE"] = $module;
		$this->replace["DESCRIPTION"] = $description;
		$this->replace["TIME"] = $frequency;

		//create entity method cbupdater
		$register_cron_file = $this->templates_path . "RegisterCron.php";
		$register_cron_content = file_get_contents($register_cron_file);
		$new_contentem = str_replace(array_keys($this->replace), array_values($this->replace), $register_cron_content);

		$abs_temp_path =  "Smarty/templates_c/$script_name.eh.php";
		$temp_path = $this->root_path  . $abs_temp_path;
		file_put_contents($temp_path, $new_contentem);

		// Create CbUpdater
		$command = $this->getApplication()->find('updater:create');
		$arguments = array(
			'name' => str_replace(' ', '', $name),
			'author'=>$author,
			'description' => "Cron : " . $description,
			'--file'  => $abs_temp_path
		);
		$updaterInput = new ArrayInput($arguments);
		$returnCode = $command->run($updaterInput, $output);

		$output->writeln("<info>cron/modules/$module/$script_name</info>");

		$output->writeln("<info>Created Sucessfuly</info>");
	}
}