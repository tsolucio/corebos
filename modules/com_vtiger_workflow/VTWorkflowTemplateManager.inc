<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

class VTWorkflowTemplateManager {
	public function __construct($adb) {
		$this->adb = $adb;
	}

	/**
	 * Create anew template instance from a workflow
	 *
	 * This template instance will not be saved. The save
	 * will have to be done explicitly.
	 *
	 * @param $title string title of the template
	 * @param $workflow object workflow instance.
	 */
	public function newTemplate($title, $workflow) {
		$adb = $this->adb;
		$wms = new VTWorkflowManager($adb);
		$str = $wms->serializeWorkflow($workflow);
		$template = new VTWorkflowTemplate();
		$template->title = $title;
		$template->moduleName = $workflow->moduleName;
		$template->template = $str;
		return $template;
	}

	/**
	 * Retrieve a template given it's id
	 *
	 * @param $templateId integer id of the template
	 * @return object template object
	 */
	public function retrieveTemplate($templateId) {
		$adb = $this->adb;
		$result = $adb->pquery('select * from com_vtiger_workflowtemplates where template_id=?', array($templateId));
		$it = new SqlResultIterator($adb, $result);
		$data = $it->current();
		$template = new VTWorkflowTemplate();
		$template->id = $templateId;
		$template->title = $data->title;
		$template->moduleName = $data->module_name;
		$template->template = $data->template;
		return $template;
	}

	/**
	 * Create a workflow from a template
	 *
	 * The new workflow will also be added to the database.
	 *
	 * @param $template object template to use
	 * @return object workflow object.
	 */
	public function createWorkflow($template) {
		$wfm = new VTWorkflowManager($this->adb);
		return $wfm->deserializeWorkflow($template->template);
	}

	/**
	 * Get template objects for a particular module.
	 *
	 * @param $moduleName string name of the module
	 * @return array containing template objects
	 */
	public function getTemplatesForModule($moduleName) {
		$result = $this->adb->pquery('select * from com_vtiger_workflowtemplates where module_name=?', array($moduleName));
		return $this->getTemplatesForResult($result);
	}

	/**
	 * Get all templates
	 *
	 * Get all the templates as an array
	 *
	 * @return array containing template objects.
	 */
	public function getTemplates() {
		$result = $this->adb->query('select * from com_vtiger_workflowtemplates');
		return $this->getTemplatesForResult($result);
	}

	/**
	 * Save a template
	 *
	 * If the object is a newly created template it
	 * will be added to the database and a field id containing
	 * the new id will be added to the object.
	 *
	 * @param $template object template object to save.
	 */
	public function saveTemplate($template) {
		if (!empty($template->id)) {
			$templateId = $template->id;
			$this->adb->pquery(
				'update com_vtiger_workflowtemplates set title=?, module_name=?, template=? where template_id=?',
				array($template->title, $template->moduleName, $template->template, $templateId)
			);
		} else {
			$templateId = $this->adb->getUniqueID("com_vtiger_workflowtemplates");
			$template->id = $templateId;
			$this->adb->pquery(
				'insert into com_vtiger_workflowtemplates (template_id, title, module_name, template) values (?, ?, ?, ?)',
				array($templateId, $template->title, $template->moduleName, $template->template)
			);
		}
		return $templateId;
	}

	/**
	 * Delete a template
	 *
	 * $templateId The id of the template to delete.
	 */
	public function deleteTemplate($templateId) {
		$this->adb->pquery('delete from com_vtiger_workflowtemplates where template_id=?', array($templateId));
	}

	/**
	 * Dump all the templates in vtiger into a string
	 *
	 * This can be used for exporting templates from one
	 * machine to another
	 *
	 * @return string dump of the templates.
	 */
	public function dumpAllTemplates() {
		$result = $this->adb->query('select * from com_vtiger_workflowtemplates');
		$it = new SqlResultIterator($this->adb, $result);
		$arr = array();
		foreach ($it as $row) {
			$el = array(
				'moduleName'=>$row->module_name,
				'title'=>$row->title,
				'template'=>$row->template
			);
			$arr[] = $el;
		}
		return json_encode($arr);
	}

	/**
	 * Load templates form a dumped string
	 *
	 * @param $str string dump generated from dumpAllTemplates
	 */
	public function loadTemplates($str) {
		$arr = json_decode($str, true);
		foreach ($arr as $el) {
			$template = new VTWorkflowTemplate();
			$template->moduleName = $el['moduleName'];
			$template->title = $el['title'];
			$template->template = $el['template'];
			$this->save($template);
			$this->createWorkflow($template);
		}
	}

	private function getTemplatesForResult($result) {
		$templates = array();
		while ($row = $this->adb->fetch_array($result)) {
			$template = new VTWorkflowTemplate();
			$template->id = $row['template_id'];
			$template->title = $row['title'];
			$template->moduleName = $row['module_name'];
			$template->template = $row['template'];
			$templates[] = $template;
		}
		return $templates;
	}
}

class VTWorkflowTemplate {

}
?>