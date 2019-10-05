<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

class createVtlibFieldCommand extends Command {


	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			// the name of the command (the part after "bin/console")
			->setName('vtlibfield:create')

			// the short description shown while running "php bin/console list"
			->setDescription('Creates a new Vtlib Field')

			// the full command description shown when running the command with
			// the "--help" option
			->setHelp('This command allows you to create a vtlib field...')

			// configure an argument
			->addArgument('module', InputArgument::REQUIRED, 'module name')

						// configure an argument
			->addArgument('name', InputArgument::REQUIRED, 'name of the field')

						// configure an argument
			->addArgument('label', InputArgument::REQUIRED, 'label of the field')

						// configure an argument
			->addArgument('uitype', InputArgument::REQUIRED, 'label of the field')

						 // configure an argument
			->addArgument('author', InputArgument::REQUIRED, 'author of cbupdater')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $adb;
		$name = $input->getArgument("name");
		$module_name = $input->getArgument("module");
		$label = $input->getArgument("label");
		$uitype = $input->getArgument("uitype");
		$author = $input->getArgument("author");

		//create custom function
		$class_path = $this->templates_path . "VtlibField.php";
		$class_content = file_get_contents($class_path);

		$temppathrel = "Smarty/templates_c/$name.field.php";
		$temppath = $this->root_path .$temppathrel;

		$this->replace['NAME'] = $name;
		$this->replace['LABEL'] = $label;
		$this->replace['MODULE'] = $module_name;
		$this->replace['UITYPE'] = $uitype;
		$query = $adb->pquery("select GROUP_CONCAT(blocklabel) as blocks,tablename from vtiger_blocks join vtiger_entityname"
				. " on vtiger_blocks.tabid=vtiger_entityname.tabid join vtiger_tab on vtiger_tab.tabid=vtiger_entityname.tabid where name=?", array($module_name));

		if ($adb->num_rows($query)>0) {
			$table = $adb->query_result($query, 0, 'tablename');
			$blocks = explode(",", $adb->query_result($query, 0, 'blocks'));
			$this->replace['TABLE'] = $table;
			$helper = $this->getHelper('question');
			$question = new ChoiceQuestion(
				'Please select one block for module '.$module_name,
				$blocks,
				0
			);
			$question->setErrorMessage('Block %s is invalid.');

			$block = $helper->ask($input, $output, $question);
			$this->replace['BLOCK'] = $block;
			$typeofdata = array("1,2,4,15,16,19,20,56"=>array("VARCHAR","TEXT"),"5,6,23"=>array("DATE","DATETIME"),"7"=>array("DECIMAL","INT"),"10"=>array("VARCHAR","INT"));
			foreach ($typeofdata as $key => $value) {
				$keys = explode(",", $key);
				if (in_array($uitype, $keys)) {
					$tdata = $value;
					continue;
				}
			}
			$question2 = new ChoiceQuestion(
				'Please select the column type of the field ',
				$tdata,
				0
			);
			$question2->setErrorMessage('Type of data %s is invalid.');
			$tdt = $helper->ask($input, $output, $question2);

			$question3 = new Question("Add the size of the $tdt field ");
			$size = $helper->ask($input, $output, $question3);
			if ($size == '' && $tdt == 'DECIMAL') {
				$size = '10,2';
			} elseif ($size == '' && $tdt == 'INT') {
				$size = '9';
			} else {
				$size = '250';
			}

			$this->replace['TYPE'] = $tdt.'('.$size.')';

			$question4 = new ChoiceQuestion(
				"Is the field optional or mandatory ",
				array("O","M"),
				0
			);
			$question4->setErrorMessage('Type of data %s is invalid.');
			$opt = $helper->ask($input, $output, $question4);

			switch ($tdt) {
				case 'VARCHAR':
					$typeof = 'V';
					break;
				case 'TEXT':
					$typeof = 'V';
					break;
				case 'DATE':
					$typeof = 'D';
					break;
				case 'DATETIME':
					$typeof = 'D';
					break;
				case 'INT':
					$typeof = 'I';
					break;
				case 'DECIMAL':
					$typeof = 'N';
					break;
				default:
					$typeof = 'V';
			}
			$typeofcombination = $typeof.'~'.$opt.'~'.$size;
			$this->replace['TOFDATA'] = $typeofcombination;
			if ($uitype == 15) {
				$question5 = new Question("Add the picklist options of the field, comma separated ");
				$poptions = "'".str_replace(",", "','", $helper->ask($input, $output, $question5)). "'";
				$this->replace['//other options'] = '$field->setPicklistValues(Array('.$poptions.'));';
			}
			if ($uitype == 10) {
				$question6 = new Question("Add the related modules of the uitype 10, comma separated ");
				$relmodules = "'".str_replace(",", "','", $helper->ask($input, $output, $question6)). "'";
				$this->replace['//other options'] = '$field->setRelatedModules(Array('.$relmodules.'));';
			}
		} else {
			return;
		}
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