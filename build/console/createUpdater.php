<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateUpdaterCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";

	protected $root_path = __DIR__ . "/../../";

	protected $changesets_file = "";

	protected $replace = [];

	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName('updater:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new cbUpdater.')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create a cbupdater...')

			// configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'a name to identify the update')

			// configure an argument
			->addArgument('author', InputArgument::REQUIRED, 'Author of the update')

			// configure an argument
			->addArgument('description', InputArgument::REQUIRED, 'Description of the update')

			->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'apply code from file to cbupdater class', null)

			->addOption('undofile', 'u', InputOption::VALUE_OPTIONAL, 'undo changes from file to cbupdater class', null)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$name = $input->getArgument("name");

		$this->changesets_file  =  "build/changeSets/" . date("Y") . "/" . $name . ".php";

		$output->writeln("<info>Changeset created successfully</info>");
		$output->writeln("<comment>Files to check :</comment>");

		$this->updateManifest($input, $output);
		$this->createClass($input, $output);
	}

	/**
	 * Insert a chengeset on cbupdate manifest
	 *
	 * @param  InputInterface  $input
	 * @param  OutputInterface $output
	 */
	private function updateManifest(InputInterface $input, OutputInterface $output) {

		$un_path = "modules/cbupdater/cbupdater.xml";
		$xml_path = $this->root_path . $un_path;
		$cbupdater_xml = file_get_contents($xml_path);
		$is_system_update = is_null($input->getOption("undofile"));

		$catch = "</updatesChangeLog>";
		$replace = "<changeSet>" . "\n" .
		"	<author>". $input->getArgument('author') ."</author>" . "\n" .
		"	<description>" . $input->getArgument('description')."</description>" . "\n" .
		"	<filename>" . $this->changesets_file . "</filename>" . "\n" .
		"	<classname>" . ucfirst($input->getArgument("name")) . "</classname>" . "\n" .
		"	<systemupdate>". $is_system_update ."</systemupdate>" . "\n" .
		"</changeSet>" . "\n" .
		"</updatesChangeLog>";

		$new_xml = str_replace($catch, $replace, $cbupdater_xml);
		file_put_contents($xml_path, $new_xml);

		$output->writeln("<info>" . $un_path . "</info>");
	}

	private function createCLass(InputInterface $input, OutputInterface $output) {

		$apply_changes_file = $input->getOption("file");
		$undo_changes_file 	= $input->getOption("undofile");

		$class_path = $this->templates_path . "cbUpdater.php";
		$class_content = file_get_contents($class_path);
		$name = $input->getArgument("name");

		$this->replace['DummyClass'] = ucfirst($name);

		if ($apply_changes_file != null) {
			$this->replaceFromFile("// apply magic", $apply_changes_file);
		}

		if ($undo_changes_file != null) {
			$this->replaceFromFile("// undo magic", $undo_changes_file);
		}

		$new_file_path = $this->root_path . $this->changesets_file;

		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $class_content);
		file_put_contents($new_file_path, $new_content);

		$output->writeln("<info>" . $this->changesets_file . "</info>");
	}

	private function replaceFromFile($str, $file) {
		$inject_code = str_replace(["<?php\n","\n\n","\n"], ["","\n","\n\t\t\t"], file_get_contents($file));
		if ($inject_code != '') {
			$this->replace[$str] = $inject_code;
		}
		unlink($file);
	}
}