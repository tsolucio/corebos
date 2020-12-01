<?php
include_once 'include/integrations/sendgrid/sendgrid.php';
$sd = new corebos_sendgrid();
$templates = $sd->getEmailTemplates();
$templates = json_decode($templates);
$templates = isset($templates->templates) ? $templates->templates: 0;
$templateArr = array();
if ($templates) {
	for ($y=0; $y < count($templates); $y++) {
		$versions = $templates[$y]->versions;
		for ($x=0; $x < count($versions); $x++) {
			if ($versions[$x]->active == 1) {
				$templateId = $versions[$x]->template_id;
				$versionId = $versions[$x]->id;
			}
		}
		$templateArr[] = array(
			'templateName' => $templates[$y]->name,
			'templateId' => $templateId,
			'versionId' => $versionId,
		);
	}
}
echo json_encode($templateArr);