<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LogUpdaterCommand extends Command {

	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName('updater:log')

			// the short description shown while running "php bin/console list"
			->setDescription('List updates')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to list all updates')

			->addOption(
				'author',
				null,
				InputOption::VALUE_OPTIONAL,
				'How many times should the message be printed?',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$un_path = "modules/cbupdater/cbupdater.xml";
		$xml_path = __DIR__ . "/../../" . $un_path;

		$cbupdater_xml = file_get_contents($xml_path);
		$xml = simplexml_load_string($cbupdater_xml);
		$author = $input->getOption("author");

		if ($author != null) {
			$xml = $this->getByAuthor($author, $xml);
		}

		$this->log($xml, $output);
	}

	/**
	 * Filter only records with given author
	 *
	 * @param	String $authorname
	 * @param	Array $xml
	 *
	 * @return   Array
	 */
	private function getByAuthor($authorname, $xml) {

		$temp_xml = [];

		foreach ($xml as $key => $changeset) {
			if ($changeset->author == $authorname) {
				$temp_xml[] = $changeset;
			}
		}
		return $temp_xml;
	}

	/**
	 * Display formated results in console
	 *
	 * @param  Array			$xml
	 * @param  OutputInterface  $output
	 */
	private function log($xml, OutputInterface $output) {

		foreach ($xml as $key => $changeset) {
			$output->writeln("<comment>Author : " . $changeset->author . "</comment>");
			$output->writeln("File Path : " . $changeset->filename);
			$output->writeln("Class : " .  $changeset->classname);
			$output->writeln($changeset->description);
			$output->writeln("\n");
		}
	}
}