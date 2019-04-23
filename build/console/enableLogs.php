<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class EnableLogsCommand extends Command {

	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			->setName('logs:enable')

			->setDescription('Enable logs')

			->setHelp('This command allows you to enable logs on the CRM')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$performance_file = $this->root_path . "config.performance.php";
		$logs_path = $this->root_path . "logs";

		$log_contents = file_get_contents($performance_file);
		$this->replace["'LOG4PHP_DEBUG' => false"] = "'LOG4PHP_DEBUG' => true";

		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $log_contents);

		file_put_contents($performance_file, $new_content);

		if (!is_writable($logs_path)) {
			chmod($logs_path, 0777);
		}

		$output->writeln("<info>Logs Enabled</info>");
	}
}