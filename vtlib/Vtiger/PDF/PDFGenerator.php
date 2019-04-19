<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once __DIR__ . '/TCPDF.php';
include_once __DIR__ . '/Frame.php';

class Vtiger_PDF_Generator {
	private $headerViewer = false;
	private $footerViewer = false;
	private $contentViewer = false;
	private $pagerViewer = false;
	private $headerFrame;
	private $footerFrame;
	private $contentFrame;
	private $pdf;
	private $isFirstPage = false;
	private $isLastPage = false;
	private $totalWidth = 0;
	private $totalHeight = 0;

	public function __construct() {
		$this->pdf = new Vtiger_PDF_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT);

		$this->pdf->setPrintHeader(false);
		$this->pdf->setPrintFooter(false);
	}

	public function setHeaderViewer($viewer) {
		$this->headerViewer = $viewer;
	}

	public function setFooterViewer($viewer) {
		$this->footerViewer = $viewer;
	}

	public function setContentViewer($viewer) {
		$this->contentViewer = $viewer;
	}

	public function setPagerViewer($viewer) {
		$this->pagerViewer = $viewer;
	}

	public function getHeaderFrame() {
		return $this->headerFrame;
	}

	public function getFooterFrame() {
		return $this->footerFrame;
	}

	public function getContentFrame() {
		return $this->contentFrame;
	}

	public function getPDF() {
		return $this->pdf;
	}

	public function onLastPage() {
		return $this->isLastPage;
	}

	public function onFirstPage() {
		return $this->isFirstPage;
	}

	public function getTotalWidth() {
		return $this->totalWidth;
	}

	public function getTotalHeight() {
		return $this->totalHeight;
	}

	public function createLastPage() {
		// Detect if there is a last page already.
		if ($this->isLastPage) {
			return false;
		}
		// Check if the last page is required for adding footer
		if (!$this->footerViewer || !$this->footerViewer->onLastPage()) {
			return false;
		}

		$pdf = $this->pdf;

		// Create a new page
		$pdf->AddPage();

		$this->isFirstPage = false;
		$this->isLastPage = true;

		$margins = $pdf->getMargins();
		$totalHeightFooter = $this->footerViewer? $this->footerViewer->totalHeight($this) : 0;

		if ($totalHeightFooter) {
			$this->footerFrame = new Vtiger_PDF_Frame();
			$this->footerFrame->x = $pdf->GetX();
			$this->footerFrame->y = $margins['top'];
			$this->footerFrame->h = $totalHeightFooter;
			$this->footerFrame->w = $this->totalWidth;

			$this->footerViewer->initDisplay($this);
			$this->footerViewer->display($this);
		}
		if ($this->pagerViewer) {
			$this->pagerViewer->display($this);
		}
		return true;
	}

	public function createPage($isLastPage = false) {
		$pdf = $this->pdf;

		// Create a new page
		$pdf->AddPage();

		if ($isLastPage) {
			$this->isFirstPage = false;
			$this->isLastPage = true;
		} else {
			if ($pdf->getPage() > 1) {
				$this->isFirstPage = false;
			} else {
				$this->isFirstPage = true;
			}
		}

		$margins = $pdf->getMargins();

		$this->totalWidth  = $pdf->getPageWidth()-$margins['left']-$margins['right'];

		$this->totalHeight = $totalHeight = $pdf->getPageHeight() - $margins['top'] - $margins['bottom'];
		$totalHeightHeader = $this->headerViewer? $this->headerViewer->totalHeight($this) : 0;
		$totalHeightFooter = $this->footerViewer? $this->footerViewer->totalHeight($this) : 0;

		$totalHeightContent= $this->contentViewer->totalHeight($this);
		if ($totalHeightContent === 0) {
			$totalHeightContent = $totalHeight - $totalHeightHeader - $totalHeightFooter;
		}

		if ($totalHeightHeader) {
			$this->headerFrame = new Vtiger_PDF_Frame();
			$this->headerFrame->x = $pdf->GetX();
			$this->headerFrame->y = $pdf->GetY();
			$this->headerFrame->h = $totalHeightHeader;
			$this->headerFrame->w = $this->totalWidth;

			$this->headerViewer->initDisplay($this);
			$this->headerViewer->display($this);
		}

		// ContentViewer
		$this->contentFrame = new Vtiger_PDF_Frame();
		$this->contentFrame->x = $pdf->GetX();
		$this->contentFrame->y = $pdf->GetY();

		$this->contentFrame->h = $totalHeightContent;
		$this->contentFrame->w = $this->totalWidth;

		$this->contentViewer->initDisplay($this);

		if ($totalHeightFooter) {
			$this->footerFrame = new Vtiger_PDF_Frame();
			$this->footerFrame->x = $pdf->GetX();
			$this->footerFrame->y = $totalHeight+$margins['top']-$totalHeightFooter;
			$this->footerFrame->h = $totalHeightFooter;
			$this->footerFrame->w = $this->totalWidth;

			$this->footerViewer->initDisplay($this);
			$this->footerViewer->display($this);
		}

		if ($this->pagerViewer) {
			$this->pagerViewer->display($this);
		}
	}

	public function generate($name, $outputMode = 'D') {
		$this->contentViewer->display($this);
		return $this->pdf->Output($name, $outputMode);
	}

	public function getImageSize($file) {
		// get image dimensions
		$imsize = @getimagesize($file);
		if ($imsize === false) {
			// encode spaces on filename
			$file = str_replace(' ', '%20', $file);
			$imsize = @getimagesize($file);
			if ($imsize === false) {
				//TODO handle error better.
				//values here are consistent with one that should be max size of logo.
				return array(60, 30, null, null);
			}
		}
		return $imsize;
	}
}
?>
