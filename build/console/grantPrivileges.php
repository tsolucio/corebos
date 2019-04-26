<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class grantPrivileges extends Command {

	protected $root_path = __DIR__ . "/../../";

	protected function configure() {

		$this
			->setName('privileges:grant')

			->setDescription('Fix permissions for some files on CRM. Run this command as ROOT')

			->setHelp('For this command to work use sudo');

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		chmod($this->root_path .  "tabdata.php", 0777);
		chmod($this->root_path .  "parent_tabdata.php", 0777);
		chmod($this->root_path .  "cache", 0777);
		chmod($this->root_path .  "storage", 0777);
		chmod($this->root_path .  "user_privileges", 0777);
		chmod($this->root_path .  "Smarty/cache", 0777);
		chmod($this->root_path .  "Smarty/templates_c", 0777);
		chmod($this->root_path .  "test", 0777);

		$output->writeln("<info>Permission Granted</info>");
	}
}