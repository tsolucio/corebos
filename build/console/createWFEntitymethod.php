<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateWFEntitymethodCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('entitymethod:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new WF Entity method')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create a workflow custom function...')

			// configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'name of the method')

			// configure an argument
			->addArgument('module', InputArgument::REQUIRED, 'module name')

						// configure an argument
			->addArgument('function_name', InputArgument::REQUIRED, 'name of the function')

						// configure an argument
			->addArgument('author', InputArgument::REQUIRED, 'cbupdater author')

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$name = $input->getArgument("name");
		$module_name = $input->getArgument("module");
		$function_name = $input->getArgument("function_name");
		$author = $input->getArgument("author");

		//create custom function
		$class_path = $this->templates_path . "WFCustomFunction.php";
		$class_content = file_get_contents($class_path);

		$wf_path = $this->root_path ."modules/{$module_name}/workflows/";
		$wf_pathcomplete = $wf_path.$function_name.'.php';

		$this->replace['dummyfunction'] = $function_name;
		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $class_content);

		if (!is_dir($wf_path)) {
			mkdir($wf_path, 0755);
		}

		file_put_contents($wf_pathcomplete, $new_content);
		//create entity method cbupdater
		$class_pathem = $this->templates_path . "WFEntityMethod.php";
		$class_contentem = file_get_contents($class_pathem);

		$this->replace['FUNCTION_NAME'] = $function_name;
		$this->replace['PATH'] = "modules/{$module_name}/workflows/{$function_name}.php";

		$helper = $this->getHelper('question');
		$question = new ConfirmationQuestion('Do you want to create the workflow?', false);

		if ($helper->ask($input, $output, $question)) {
			$wfcreate = $this->templates_path . "WFCreate.php";
			$wfcreate_content = file_get_contents($wfcreate);

			$this->replace['//CREATE WF'] = $wfcreate_content;
		}

			$this->replace['MODULE'] = $module_name;
			$this->replace['DESC'] = $name;
			$this->replace['NAME'] = $function_name;

		$new_contentem = str_replace(array_keys($this->replace), array_values($this->replace), $class_contentem);

		$temppathrel = "Smarty/templates_c/$function_name.em.php";
		$temppath = $this->root_path .$temppathrel;
		file_put_contents($temppath, $new_contentem);

		$command = $this->getApplication()->find('updater:create');
		$arguments = array(
			'name' => $function_name,
			'author'=>$author,
			'description' => $name,
			'--file'  => $temppathrel
		);
		$updaterInput = new ArrayInput($arguments);
		$returnCode = $command->run($updaterInput, $output);

		$output->writeln("<info>Created Sucessfuly</info>");
	}
}