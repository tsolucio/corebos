<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

class createWSCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('webservice:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new Webservice method')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create a webservice method...')

			// configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'name of the method')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$name = $input->getArgument("name");

		//create changeset
		$class_path = $this->templates_path . "wschangeset.php";
		$class_content = file_get_contents($class_path);

		$ch_path = $this->root_path ."build/wsChanges/";
		$ch_pathcomplete = $ch_path.$name.'.php';

		$this->replace['WSNAME'] = $name;

		$params = 0;
		$parameters = array();
		$pnames = array();

		while ($params == 0) {
			$helper = $this->getHelper('question');
			$question = new Question('Add parameter name (leave empty to end the list) ');

			$paramname = $helper->ask($input, $output, $question);
			if ($paramname == '') {
				$params = 1;
			} else {
				$question2 = new ChoiceQuestion(
					"Type of parameter ",
					array("String","Encoded","DateTime"),
					0
				);
				$question2->setErrorMessage('Type of data %s is invalid.');
				$paramtype = $helper->ask($input, $output, $question2);
				$parameters[] = 'array("name" => "'.$paramname.'","type" => "'.$paramtype.'")';
				$pnames[] = "$$paramname";
			}
		}
		if (count($parameters)>0) {
			$this->replace['PARAMS'] = implode(",", $parameters);
		}
		$question3 = new ChoiceQuestion(
			"Type of method ",
			array("POST","GET"),
			0
		);
		$question3->setErrorMessage('Type of data %s is invalid.');
		$methodtype = $helper->ask($input, $output, $question3);
		$this->replace['METHODTYPE'] = $methodtype;

		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $class_content);
		file_put_contents($ch_pathcomplete, $new_content);

		//create ws script
		$class_pathws = $this->templates_path . "wsscript.php";
		$class_contentws = file_get_contents($class_pathws);

		$ws_path = $this->root_path ."include/Webservices/";
		$ws_pathcomplete = $ws_path.$name.'.php';

		$this->replace['NAME'] = $name;
		$this->replace['$INPUT'] = implode(",", $pnames);

		$new_contentws = str_replace(array_keys($this->replace), array_values($this->replace), $class_contentws);
		file_put_contents($ws_pathcomplete, $new_contentws);
		$output->writeln("<info>Created Sucessfuly</info>");
	}
}