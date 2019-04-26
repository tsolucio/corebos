<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\ChoiceQuestion;

class createEventHandlerCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('eventhandler:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new Event handler')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create an event handler...')

						// configure an argument
			->addArgument('module', InputArgument::REQUIRED, 'module of the event handler')

			// configure an argument
			->addArgument('classname', InputArgument::REQUIRED, 'name of the class')

						// configure an argument
			->addArgument('author', InputArgument::REQUIRED, 'cbupdater author')

		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$module_name = $input->getArgument("module");
		$classname = $input->getArgument("classname");
		$author = $input->getArgument("author");

		//create custom function
		$class_path = $this->templates_path . "EventHandlerfile.php";
		$class_content = file_get_contents($class_path);

		$eh_path = $this->root_path ."modules/{$module_name}/handlers/";
		$eh_pathcomplete = $eh_path.$classname.'.php';

		$this->replace['CLASSNAME'] = $classname;

		$events = array("vtiger.entity.beforesave","vtiger.entity.beforesave.modifiable","vtiger.entity.beforesave.final","vtiger.entity.aftersave",
				 "vtiger.entity.aftersave.final","vtiger.entity.beforedelete","vtiger.entity.afterdelete","vtiger.entity.afterrestore","vtiger.entity.beforegroupdelete");
		$helper = $this->getHelper('question');
		$question = new ChoiceQuestion(
			'Please select the events ',
			$events,
			0
		);
		$question->setErrorMessage('Events %s is invalid.');

		$event = $helper->ask($input, $output, $question);
		$this->replace['NAME'] = $event;
		$this->replace['//add condition'] = 'if ($eventName == \''.$event.'\') {
                $moduleName = $entityData->getModuleName();
                }';
		$new_content = str_replace(array_keys($this->replace), array_values($this->replace), $class_content);

		if (!is_dir($eh_path)) {
			mkdir($eh_path, 0755);
		}

		file_put_contents($eh_pathcomplete, $new_content);
		//create entity method cbupdater
		$class_pathem = $this->templates_path . "EventHandlercreate.php";
		$class_contentem = file_get_contents($class_pathem);

		$this->replace['PATH'] = "modules/{$module_name}/handlers/{$classname}.php";
		$this->replace['CLASSNAME'] = $classname;

		$new_contentem = str_replace(array_keys($this->replace), array_values($this->replace), $class_contentem);

		$temppathrel = "Smarty/templates_c/$classname.eh.php";
		$temppath = $this->root_path .$temppathrel;
		file_put_contents($temppath, $new_contentem);

		$command = $this->getApplication()->find('updater:create');
		$arguments = array(
			'name' => $classname,
			'author'=>$author,
			'description' => $classname,
			'--file'  => $temppathrel
		);
		$updaterInput = new ArrayInput($arguments);
		$returnCode = $command->run($updaterInput, $output);

		$output->writeln("<info>Created Sucessfuly</info>");
	}
}