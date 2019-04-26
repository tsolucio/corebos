<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/Viewer.php';

class Vtiger_PDF_FooterViewer extends Vtiger_PDF_Viewer {

	protected $model;

	protected $onEveryPage = true;
	protected $onLastPage = false;

	public function setOnEveryPage() {
		$this->onEveryPage = true;
		$this->onLastPage = false;
	}

	public function onEveryPage() {
		return $this->onEveryPage;
	}

	public function setOnLastPage() {
		$this->onEveryPage = false;
		$this->onLastPage = true;
	}

	public function onLastPage() {
		return $this->onLastPage;
	}

	public function setModel($m) {
		$this->model = $m;
	}

	public function totalHeight($parent) {
		$height = 0.1;

		if ($this->model && $this->onEveryPage()) {
			$pdf = $parent->getPDF();

			$contentText = $this->model->get('content');
			$height = $pdf->GetStringHeight($contentText, $parent->getTotalWidth());
		}

		if ($this->onEveryPage) {
			return $height;
		}
		if ($this->onLastPage && $parent->onLastPage()) {
			return $height;
		}
		return 0;
	}

	public function initDisplay($parent) {
	}

	public function display($parent) {

		$pdf = $parent->getPDF();
		$footerFrame = $parent->getFooterFrame();

		if ($this->model) {
			$targetFooterHeight = ($this->onEveryPage())? $footerFrame->h : 0;

			$pdf->MultiCell($footerFrame->w, $targetFooterHeight, $this->model->get('content'), 1, 'L', 0, 1, $footerFrame->x, $footerFrame->y);
		}
	}
}
