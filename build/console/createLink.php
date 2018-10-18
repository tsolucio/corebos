<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateLinkCommand extends Command {

	protected $templates_path = __DIR__ . "/templates/";
	protected $root_path = __DIR__ . "/../../";
	protected $replace = [];

	protected function configure() {

		$this
			->setName('link:create')

			->setDescription('Create a new link')

			->setHelp('This command allows you to create a link')

			->addArgument('label', InputArgument::REQUIRED, 'label of the link')

			->addArgument('linktype', InputArgument::REQUIRED, 'Type odf the link')

			->addArgument('module', InputArgument::REQUIRED, 'module name')

			->addArgument('url', InputArgument::REQUIRED, 'url you want to call')

			->addArgument('author', InputArgument::REQUIRED, 'cbupdater author')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$label 		= $input->getArgument("label");
		$module 	= $input->getArgument("module");
		$linktype 	= $input->getArgument("linktype");
		$url 		= $input->getArgument("url");
		$author 	= $input->getArgument("author");

		$this->replace['LABEL'] 	= $label;
		$this->replace['MODULE'] 	= $module;
		$this->replace['LINKTYPE'] 	= $linktype;
		$this->replace['URL'] 		= $url;

		// Add Link Content
		$add_link_file 		= $this->templates_path . "LinkAdd.php";
		$add_link_content 	= file_get_contents($add_link_file);

		$new_add_content 	= str_replace(array_keys($this->replace), array_values($this->replace), $add_link_content);
		$abs_add_path 		= "Smarty/templates_c/AddLink.em.php";
		$add_link_path 		= $this->root_path .$abs_add_path;
		file_put_contents($add_link_path, $new_add_content);

		// Delete link contenr
		$del_link_file 		= $this->templates_path . "LinkDelete.php";
		$del_link_content 	= file_get_contents($del_link_file);

		$new_del_content 	= str_replace(array_keys($this->replace), array_values($this->replace), $del_link_content);
		$abs_del_path 		= "Smarty/templates_c/DelLink.em.php";
		$del_link_path 		= $this->root_path .$abs_del_path;
		file_put_contents($del_link_path, $new_del_content);

		$command = $this->getApplication()->find('updater:create');
		$arguments = array(
			'name' => $label,
			'author' => $author,
			'description' => "Create Link " . $label,
			'--file' => $abs_add_path,
			'--undofile' => $abs_del_path
		);
		$updaterInput = new ArrayInput($arguments);
		$returnCode = $command->run($updaterInput, $output);

		$output->writeln("<info>Created Sucessfuly</info>");
	}
}