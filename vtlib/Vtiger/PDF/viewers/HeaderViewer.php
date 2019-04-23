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

class Vtiger_PDF_HeaderViewer extends Vtiger_PDF_Viewer {

	protected $model;

	protected $onEveryPage = true;
	protected $onFirstPage = false;

	public function setOnEveryPage() {
		$this->onEveryPage = true;
		$this->onLastPage = false;
	}

	public function onEveryPage() {
		$this->onEveryPage = true;
		$this->onLastPage = false;
	}

	public function setOnFirstPage() {
		$this->onEveryPage = false;
		$this->onLastPage = true;
	}

	public function onFirstPage() {
		$this->onEveryPage = false;
		$this->onLastPage = true;
	}

	public function setModel($m) {
		$this->model = $m;
	}

	public function totalHeight($parent) {
		$height = 10;

		if ($this->model && $this->onEveryPage()) {
			$pdf = $parent->getPDF();

			$contentText = $this->model->get('content');
			$height = $pdf->GetStringHeight($contentText, $parent->getTotalWidth());
		}

		if ($this->onEveryPage) {
			return $height;
		}
		if ($this->onFirstPage && $parent->onFirstPage()) {
			$height;
		}
		return 0;
	}

	public function initDisplay($parent) {
		$pdf = $parent->getPDF();
		$headerFrame = $parent->getHeaderFrame();
	}

	public function display($parent) {
		$pdf = $parent->getPDF();
		$headerFrame = $parent->getHeaderFrame();

		if ($this->model) {
			$pdf->MultiCell($headerFrame->w, $headerFrame->h, $this->model->get('content'), 1, 'L', 0, 1, $headerFrame->x, $headerFrame->y);
		}
	}
}
