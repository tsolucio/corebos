<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

class createRelatedListCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('relatedlist:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new Related List')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create a related list...')

						 // configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'name of related list')

			// configure an argument
			->addArgument('module1', InputArgument::REQUIRED, 'module of the module containing the related list')

						// configure an argument
			->addArgument('module2', InputArgument::REQUIRED, 'module connected to the first one')

						// configure an argument
			->addArgument('label', InputArgument::REQUIRED, 'label of the field')

						 // configure an argument
			->addArgument('author', InputArgument::REQUIRED, 'author of the script')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$module1 = $input->getArgument("module1");
		$module2 = $input->getArgument("module2");
		$label = $input->getArgument("label");
		$name = $input->getArgument("name");
		$author = $input->getArgument("author");
		//create custom function
		$class_path = $this->templates_path . "RelatedList.php";
		$class_content = file_get_contents($class_path);

		$temppathrel = "Smarty/templates_c/$name.rl.php";
		$temppath = $this->root_path .$temppathrel;

		$this->replace['LABEL'] = $label;
		$this->replace['MODULE1'] = $module1;
		$this->replace['MODULE2'] = $module2;

		$func = array("get_related_list","get_dependents_list","Other");
		$helper = $this->getHelper('question');
		$question = new ChoiceQuestion(
			'Please select related list function ',
			$func,
			0
		);
		$question->setErrorMessage('Function %s is invalid.');

		$function = $helper->ask($input, $output, $question);

		if ($function == 'Other') {
			$question1 = new Question('Please select the custom function name');
			$cust = $helper->ask($input, $output, $question1);
			$this->replace['FUNCTION'] = $cust;
		} else {
			$this->replace['FUNCTION'] = $function;
		}

		$btype = array("ADD","SELECT","ADD,SELECT","");
		$question2 = new ChoiceQuestion(
			'Please select the add or select type of the button',
			$btype,
			0
		);
		$question2->setErrorMessage('Type of data %s is invalid.');
		$type = $helper->ask($input, $output, $question2);
		if ($type == 'ADD,SELECT') {
			$type = '"ADD","SELECT"';
		} else {
			$type = "'".$type."'";
		}
		$this->replace['TYPE'] = $type;

		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $class_content);
		file_put_contents($temppath, $new_content);
		$command = $this->getApplication()->find('updater:create');
		$arguments = array(
			'name' => $name,
			'author'=>$author,
			'description' => $name,
			'--file'  => $temppathrel
			);
		$updaterInput = new ArrayInput($arguments);
		$returnCode = $command->run($updaterInput, $output);
		$output->writeln("<info>Created Sucessfuly</info>");
	}
}