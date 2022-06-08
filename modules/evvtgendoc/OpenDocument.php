<?php
/**
* OpenDocument base class
*
* OpenDocument class handles reading and modifying files in OpenDocument format
*
* PHP version 5
*
* LICENSE: This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.1
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*
* Copyright 2009 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
* Author: Joe Bordes
*
*/
global $root_directory;
$ruta = '/usr/lib/pear';
set_include_path(get_include_path() . PATH_SEPARATOR . $root_directory.'vtlib/thirdparty/network');
require_once 'OpenDocument/ZipWrapper.php';
require_once 'OpenDocument/Exception.php';
require_once 'OpenDocument/TextElement.php';
require_once 'OpenDocument/Span.php';
require_once 'OpenDocument/Paragraph.php';
require_once 'OpenDocument/Heading.php';
require_once 'OpenDocument/Tab.php';
require_once 'OpenDocument/LineBreak.php';
require_once 'OpenDocument/SoftPageBreak.php';
require_once 'OpenDocument/Space.php';
require_once 'OpenDocument/OpenDate.php';
require_once 'OpenDocument/OpenTime.php';
require_once 'OpenDocument/PageNumber.php';
require_once 'OpenDocument/PageCount.php';
require_once 'OpenDocument/Footnote.php';
require_once 'OpenDocument/NoteCitation.php';
require_once 'OpenDocument/NoteBody.php';
require_once 'OpenDocument/InfoSubject.php';
require_once 'OpenDocument/InfoTitle.php';
require_once 'OpenDocument/InfoAuthor.php';
require_once 'OpenDocument/BookmarkStart.php';
require_once 'OpenDocument/BookmarkEnd.php';
require_once 'OpenDocument/Table.php';
require_once 'OpenDocument/TableColumn.php';
require_once 'OpenDocument/TableRow.php';
require_once 'OpenDocument/TableHeaderRow.php';
require_once 'OpenDocument/TableCell.php';
require_once 'OpenDocument/TableCoveredCell.php';
require_once 'OpenDocument/List.php';
require_once 'OpenDocument/ListItem.php';
require_once 'OpenDocument/Frame.php';
require_once 'OpenDocument/Image.php';
require_once 'OpenDocument/DrawCustomShape.php';
require_once 'OpenDocument/DrawEGeometry.php';
require_once 'OpenDocument/DrawGraph.php';
require_once 'OpenDocument/DrawLine.php';
require_once 'OpenDocument/DrawConnector.php';
require_once 'OpenDocument/DrawRect.php';
require_once 'OpenDocument/DrawObject.php';
require_once 'OpenDocument/DrawHandle.php';
require_once 'OpenDocument/DrawEquation.php';
require_once 'OpenDocument/TextBox.php';
require_once 'OpenDocument/Bookmark.php';
require_once 'OpenDocument/Hyperlink.php';
require_once 'OpenDocument/ReferenceMark.php';
require_once 'OpenDocument/ReferenceRef.php';
require_once 'OpenDocument/Section.php';
require_once 'compile.php'; // open document class
require_once 'vtlib/Vtiger/Net/Client.php';

// Global array para controlar los bloques {siexiste} y {sinoexiste}
global $siincluir;
$siincluir=array();
// Global var para controlar los bloques {paracada}
global $pcincluir;
$pcincluir=false;
// Global var para controlar tipo bloque ODT a crear e imagenes a cambiar
global $parentArray;
$parentArray = array();
global $changedImage;
$changedImage='';
global $newImageAdded;
$newImageAdded=false;
// Global para controlar las repeticiones de paracada
global $repe;
if (is_null($repe)) {
	$repe = array();
}
// global variable for XML aggregates
$genxmlaggregates = array();

/**
* OpenDocument base class
*
* OpenDocument class handles reading and modifying files in OpenDocument format
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument {

	/**
	 * Debug merge process
	 */
	public static $debug = false;

	/**
	 * Debug output destination
	 * screen
	 * file
	 */
	public static $debug_output = 'screen';

	/**
	 * two letter language code the template directives are in
	 */
	public static $compile_language = 'es';

	/**
	 * Path to opened OpenDocument file
	 *
	 * @var string
	 * @access private
	 */
	private $path;

	/**
	 * DOMNode of current node
	 *
	 * @var DOMNode
	 * @access private
	 */
	private $cursor;

	/**
	 * DOMNode contains style information
	 *
	 * @var DOMNode
	 * @access private
	 */
	private $styles;
	private $styles_array;
	private $originGenDocStyles;

	/**
	 * DOMNode contains fonts declarations
	 *
	 * @var DOMNode
	 * @access private
	 */
	private $fonts;

	/**
	 * Mime type information
	 *
	 * @var string
	 * @access private
	 */
	private $mimetype;

	/**
	 * Flag indicates whether it is a new file
	 *
	 * @var bool
	 * @access private
	 */
	private $create = false;

	/**
	 * DOMDocument for content file
	 *
	 * @var DOMDocument
	 * @access private
	 */
	public $contentDOM;

	/**
	 * DOMXPath object for content file
	 *
	 * @var DOMXPath
	 * @access private
	 */
	private $contentXPath;

	/**
	 * DOMDocument for meta file
	 *
	 * @var DOMDocument
	 * @access private
	 */
	private $metaDOM;

	/**
	 * DOMXPath for meta file
	 *
	 * @var DOMXPath
	 * @access private
	 */
	private $metaXPath;

	/**
	 * DOMDocument for settings file
	 *
	 * @var DOMDocument
	 * @access private
	 */
	private $settingsDOM;

	/**
	 * DOMXPath for setting file
	 *
	 * @var DOMXPath
	 * @access private
	 */
	private $settingsXPath;

	/**
	 * DOMDocument for styles file
	 *
	 * @var DOMDocument
	 * @access private
	 */
	public $stylesDOM;

	/**
	 * DOMXPath for styles file
	 *
	 * @var DOMXPath
	 * @access private
	 */
	private $stylesXPath;

	/**
	 * DOMDocument for styles file
	 *
	 * @var DOMDocument
	 * @access private
	 */
	public $manifestDOM;

	/**
	 * DOMXPath for manifest file
	 *
	 * @var DOMXPath
	 * @access private
	 */
	private $manifestXPath;

	/**
	 * Collection of children objects
	 *
	 * @var ArrayIterator
	 * @access read-only
	 */
	private $children;

	/**
	 * File with document contents
	 */
	const FILE_CONTENT = 'content.xml';

	/**
	 * File with meta information
	 */
	const FILE_META = 'meta.xml';

	/**
	 * File with editor settings
	 */
	const FILE_SETTINGS = 'settings.xml';

	/**
	 * File with document styles
	 */
	const FILE_STYLES = 'styles.xml';

	/**
	 * File with mime type
	 */
	const FILE_MIMETYPE = 'mimetype';

	/**
	 * File with manifest information
	 */
	const FILE_MANIFEST = 'META-INF/manifest.xml';
	const MANIFEST = 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0';

	/**
	 * text namespace URL
	 */
	const NS_TEXT = 'urn:oasis:names:tc:opendocument:xmlns:text:1.0';

	/**
	 * namespace OpenDocument number
	 */
	const NS_NUMBER = 'urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0';

	/**
	 * table namespace URL
	 */
	const NS_TABLE = 'urn:oasis:names:tc:opendocument:xmlns:table:1.0';

	/**
	 * Document cache directory
	 */
	const GENDOCCACHE = 'cache/gendocoutput';

	private $NS_TABLE_attrib=array('align');

	/**
	 * style namespace URL
	 */
	const NS_STYLE = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';

	/**
	 * fo namespace URL
	 */
	const NS_FO = 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0';
	private $NS_FO_attrib=array('color','font-size','font-weight','font-style','background-color','padding','country','language',
								'border-left','border-right','border-top','border-bottom','border','widows','orphans','clip',
								'min-height','margin-left','margin-right','margin-top','margin-bottom','text-align','text-indent',
								'padding-left','padding-right','padding-top','padding-bottom','break-before','line-height',
								'font-variant','text-transform','text-shadow','letter-spacing','break-after','text-align-last'
	);
	private $NS_DRAW_attrib=array('luminance','contrast','red','green','blue','gamma','color-inversion','image-opacity','color-mode','transparency','opacity');
	private $NS_XLINK_attrib=array('type','href','show','actuate'
	);
	private $NS_LISTSTYLE=array('num-format','num-suffix','font-name','num-prefix');
	private $NS_ENTITIES=array('paragraph','table','table-column','table-cell','table-row','graphic','section');
	public static $ReservedStyles=array('Text_20_.*','Heading.*','Standard','Table_20_.*','Caption','Index','Internet_20_.*','First_20_Page');

	public $changedImages=array();
	public $newImages=array();
	public $contextoParacada=array();
	public $contextoActual=0;
	private $saveContextoActual;
	public $xmlout;

	/**
	 * office namespace URL
	 */
	const NS_OFFICE = 'urn:oasis:names:tc:opendocument:xmlns:office:1.0';

	/**
	 * svg namespace URL
	 */
	const NS_SVG = 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0';

	/**
	 * xlink namespace URL
	 */
	const NS_XLINK = 'http://www.w3.org/1999/xlink';
	/**
	 * namespace OpenDocument draw
	 */
	const NS_DRAW = 'urn:oasis:names:tc:opendocument:xmlns:drawing:1.0';

	/**
	 * Constructor
	 *
	 * @param string $filename optional
	 *               specify file name if you want to open existing file
	 *               to create new document pass nothing or empty string
	 * @throws OpenDocument_Exception
	 */
	public function __construct($filename = '') {
		if (!strlen($filename)) {
			$filename = __DIR__ . '/OpenDocument/templates/default.odt';
			$this->create = true;
		}

		if (!is_readable($filename)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::ACCESS_FILE_ERR);
		}
		$this->path = $filename;

		//get mimetype
		if (!$this->mimetype = ZipWrapper::read($filename, self::FILE_MIMETYPE)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_MIMETYPE_ERR);
		}

		//get content
		$this->contentDOM = new DOMDocument();
		if (!$this->contentDOM->loadXML(ZipWrapper::read($filename, self::FILE_CONTENT))) {
			throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_CONTENT_ERR);
		}
		$this->contentXPath = new DOMXPath($this->contentDOM);

		//get meta data
		$this->metaDOM = new DOMDocument();
		if (!$this->metaDOM->loadXML(ZipWrapper::read($filename, self::FILE_META))) {
			throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_META_ERR);
		}

		//get settings
		$this->settingsDOM = new DOMDocument();
		if (!$this->settingsDOM->loadXML(ZipWrapper::read($filename, self::FILE_SETTINGS))) {
			throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_SETTINGS_ERR);
		}

		//get styles
		$this->stylesDOM = new DOMDocument();
		if (!$this->stylesDOM->loadXML(ZipWrapper::read($filename, self::FILE_STYLES))) {
			throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_STYLES_ERR);
		}

		//get manifest information
		$this->manifestDOM = new DOMDocument();
		if (!$this->manifestDOM->loadXML(ZipWrapper::read($filename, self::FILE_MANIFEST))) {
			throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_MANIFEST_ERR);
		}

		//set cursor
		$this->cursor = $this->contentXPath->query('/office:document-content/office:body/office:text')->item(0);
		$this->styles = $this->contentXPath->query('/office:document-content/office:automatic-styles')->item(0);
		$this->fonts  = $this->contentXPath->query('/office:document-content/office:font-face-decls')->item(0);
		$this->contentXPath->registerNamespace('text', self::NS_TEXT);
		$this->styles_array = $this->getStyles();

		$this->listChildren();
		$this->setMax();
	}

	/**
	 * Magic method
	 * Provide read only access to cursor private variable
	 *
	 * @param  string $name
	 * @return mixed
	 */
	public function __get($name) {
		switch ($name) {
			case 'cursor':
				return $this->cursor;
			default:
		}
	}

	/**
	 * Get children list
	 *
	 * @return ArrayIterator
	 * @access public
	 */
	public function getChildren() {
		return $this->children->getIterator();
	}

	/**
	 * Create ArrayObject of document children objects
	 *
	 * @access private
	 */
	private function listChildren() {
		$this->children = new ArrayObject;
		if ($this->cursor instanceof DOMNode) {
			$childrenNodes = $this->cursor->childNodes;
			foreach ($childrenNodes as $child) {
				switch ($child->nodeName) {
					case 'text:p':
						$element = new OpenDocument_Paragraph($child, $this);
						break;
					case 'text:span':
						$element = new OpenDocument_Span($child, $this);
						break;
					case 'text:tab':
						$element = new OpenDocument_TextTab($child, $this);
						break;
					case 'text:s':
						$element = new OpenDocument_TextSpace($child, $this);
						break;
					case 'text:line-break':
						$element = new OpenDocument_TextLineBreak($child, $this);
						break;
					case 'text:soft-page-break':
						$element = new OpenDocument_TextSoftPageBreak($child, $this);
						break;
					case 'text:reference-mark':
						$element = new OpenDocument_ReferenceMark($child, $this, '');
						break;
					case 'text:reference-ref':
						$element = new OpenDocument_ReferenceRef($child, $this, '');
						break;
					case 'text:section':
						$element = new OpenDocument_Section($child, $this);
						break;
					case 'text:note':
						$element = new OpenDocument_Footnote($child, $this, '');
						break;
					case 'text:note-citation':
						$element = new OpenDocument_NoteCitation($child, $this, '');
						break;
					case 'text:note-body':
						$element = new OpenDocument_NoteBody($child, $this);
						break;
					case 'text:bookmark-start':
						$element = new OpenDocument_BookmarkStart($child, $this, '');
						break;
					case 'text:bookmark-end':
						$element = new OpenDocument_BookmarkEnd($child, $this, '');
						break;
					case 'text:date':
						$element = new OpenDocument_TextDate($child, $this, '');
						break;
					case 'text:time':
						$element = new OpenDocument_TextTime($child, $this, '');
						break;
					case 'text:page-number':
						$element = new OpenDocument_PageNumber($child, $this, '');
						break;
					case 'text:page-count':
						$element = new OpenDocument_PageCount($child, $this, '');
						break;
					case 'text:subject':
						$element = new OpenDocument_InfoSubject($child, $this, '');
						break;
					case 'text:title':
						$element = new OpenDocument_InfoTitle($child, $this, '');
						break;
					case 'text:initial-creator':
						$element = new OpenDocument_InfoAuthor($child, $this, '');
						break;
					case 'draw:g':
						$element = new OpenDocument_DrawGraph($child, $this);
						break;
					case 'draw:connector':
						$element = new OpenDocument_DrawConnector($child, $this);
						break;
					case 'draw:rect':
						$element = new OpenDocument_DrawRect($child, $this);
						break;
					case 'draw:line':
						$element = new OpenDocument_DrawLine($child, $this);
						break;
					case 'draw:custom-shape':
						$element = new OpenDocument_DrawCustomShape($child, $this);
						break;
					case 'draw:enhanced-geometry':
						$element = new OpenDocument_DrawEGeometry($child, $this);
						break;
					case 'draw:handle':
						$element = new OpenDocument_DrawHandle($child, $this);
						break;
					case 'draw:equation':
						$element = new OpenDocument_DrawEquation($child, $this);
						break;
					case 'draw:frame':
						$element = new OpenDocument_Frame($child, $this);
						break;
					case 'draw:text-box':
						$element = new OpenDocument_FrameTextBox($child, $this);
						break;
					case 'draw:image':
						$element = new OpenDocument_FrameImage($child, $this);
						break;
					case 'text:list':
						$element=$this->getListBranch($child);
						break;
					case 'table:table':
						$tblelement = new OpenDocument_Table($child, $this);
						$tblelement->children = new ArrayObject;
						$tableNodes= $child->childNodes;
						for ($idx=0; $idx<$tableNodes->length; $idx++) {
							$tblelem=$tableNodes->item($idx);
							switch ($tblelem->nodeName) {
								case 'table:table-column':
									$element = new OpenDocument_TableColumn($tblelem, $this);
									$tblelement->children->append($element);
									break;
								case 'table:table-header-rows':
									$headrow = new OpenDocument_TableHeaderRow($tblelem, $this);
									$headrow->children = new ArrayObject;
									$tableRows= $tblelem->childNodes;
									for ($jdx=0; $jdx<$tableRows->length; $jdx++) {
										$hr = new OpenDocument_TableRow($headrow->getNode(), $this);
										$hr->children = new ArrayObject;
										$hrelems=$tableRows->item($jdx);
										$hrelemsNodes=$hrelems->childNodes;
										for ($ldx=0; $ldx<$hrelemsNodes->length; $ldx++) {
											$tblcell=$hrelemsNodes->item($ldx);
											if ($tblcell->nodeName=="table:covered-table-cell") {
												$tblcellelement = new OpenDocument_TableCoveredCell($tblcell, $this);
											} else {
												$tblcellelement = new OpenDocument_TableCell($tblcell, $this);
											}
											$tblcellelement->children = new ArrayObject;
											$tblcellNodes= $tblcell->childNodes;
											for ($kdx=0; $kdx<$tblcellNodes->length; $kdx++) {
												$tblcellelem=$tblcellNodes->item($kdx);
												switch ($tblcellelem->nodeName) {
													case 'table:table':
														$tcnelement = $this->getTableBranch($tblcellelem);
														break;
													case 'text:span':
														$tcnelement = new OpenDocument_Span($tblcellelem, $this);
														break;
													case 'text:p':
														$tcnelement = new OpenDocument_Paragraph($tblcellelem, $this);
														break;
													case 'text:h':
														$tcnelement = new OpenDocument_Heading($tblcellelem, $this);
														break;
												}
												$tblcellelement->children->append($tcnelement);
											}
											$hr->children->append($tblcellelement);
										}
										$headrow->children->append($hr);
									}
									$tblelement->children->append($headrow);
									break;
								case 'table:table-row':
									$tblrowelement = new OpenDocument_TableRow($tblelem, $this);
									$tblrowelement->children = new ArrayObject;
									$tableRows= $tblelem->childNodes;
									for ($jdx=0; $jdx<$tableRows->length; $jdx++) {
										$tblcell=$tableRows->item($jdx);
										if ($tblcell->nodeName=="table:covered-table-cell") {
											$tblcellelement = new OpenDocument_TableCoveredCell($tblcell, $this);
										} else {
											$tblcellelement = new OpenDocument_TableCell($tblcell, $this);
										}
										$tblcellelement->children = new ArrayObject;
										$tblcellNodes= $tblcell->childNodes;
										for ($kdx=0; $kdx<$tblcellNodes->length; $kdx++) {
											$tblcellelem=$tblcellNodes->item($kdx);
											switch ($tblcellelem->nodeName) {
												case 'table:table':
													$tcnelement = $this->getTableBranch($tblcellelem);
													break;
												case 'text:span':
													$tcnelement = new OpenDocument_Span($tblcellelem, $this);
													break;
												case 'text:p':
													$tcnelement = new OpenDocument_Paragraph($tblcellelem, $this);
													break;
												case 'text:h':
													$tcnelement = new OpenDocument_Heading($tblcellelem, $this);
													break;
											}
											$tblcellelement->children->append($tcnelement);
										}
										$tblrowelement->children->append($tblcellelement);
									}
									$tblelement->children->append($tblrowelement);
									break;
							}
						}
						$this->children->append($tblelement);
						$element=false;
						break;
					case 'text:h':
						$element = new OpenDocument_Heading($child, $this);
						break;
					default:
						$element = $child;  // Si no sabemos lo que es lo pasamos tal cual
				}
				if ($element) {
					$this->children->append($element);
				}
			}
		}
	}

	/**
	 * Create ArrayObject of document children objects
	 *
	 * @access private
	 */
	private function getTableBranch($parent) {
		$subtblelement = new OpenDocument_Table($parent, $this, 'true');
		$subtblelement->children = new ArrayObject;
		$childrenNodes = $parent->childNodes;
		for ($idx=0; $idx<$childrenNodes->length; $idx++) {
			$child=$childrenNodes->item($idx);
			switch ($child->nodeName) {
				case 'table:table-column':
					$element = new OpenDocument_TableColumn($child, $this);
					break;
				case 'table:table-row':
					$tblrowelement = new OpenDocument_TableRow($child, $this);
					$tblrowelement->children = new ArrayObject;
					$tableRows= $child->childNodes;
					for ($jdx=0; $jdx<$tableRows->length; $jdx++) {
						$tblcell=$tableRows->item($jdx);
						if ($tblcell->nodeName=="table:covered-table-cell") {
							$tblcellelement = new OpenDocument_TableCoveredCell($tblcell, $this);
						} else {
							$tblcellelement = new OpenDocument_TableCell($tblcell, $this);
						}
						$tblcellelement->children = new ArrayObject;
						$tblcellNodes= $tblcell->childNodes;
						for ($kdx=0; $kdx<$tblcellNodes->length; $kdx++) {
							$tblcellelem=$tblcellNodes->item($kdx);
							switch ($tblcellelem->nodeName) {
								case 'table:table':
									$tcnelement = $this->getTableBranch($tblcellelem);
									break;
								case 'text:p':
									$tcnelement = new OpenDocument_Paragraph($tblcellelem, $this);
									break;
								case 'text:span':
									$tcnelement = new OpenDocument_Span($tblcellelem, $this);
									break;
								case 'text:h':
									$tcnelement = new OpenDocument_Heading($tblcellelem, $this);
									break;
							}
							$tblcellelement->children->append($tcnelement);
						}
						$tblrowelement->children->append($tblcellelement);
					}
					$element=$tblrowelement;
					break;
				default:
					$element = $child;  // Si no sabemos lo que es lo pasamos tal cual
			}
			if ($element) {
				$subtblelement->children->append($element);
			}
		}
		return $subtblelement;
	}

	/**
	 * Create ArrayObject of document children objects
	 *
	 * @access private
	 */
	private function getListBranch($parent) {
		$listelement = new OpenDocument_List($parent, $this);
		$listelement->children = new ArrayObject;
		$childrenNodes = $parent->childNodes;
		for ($idx=0; $idx<$childrenNodes->length; $idx++) {
			$child=$childrenNodes->item($idx);
			$listitem= new OpenDocument_ListItem($child, $this);
			$listitem->children = new ArrayObject;
			$listRows= $child->childNodes;
			for ($jdx=0; $jdx<$listRows->length; $jdx++) {
				$listelem=$listRows->item($jdx);
				switch ($listelem->nodeName) {
					case 'text:list':
						$tcnelement = $this->getListBranch($listelem);
						break;
					case 'text:p':
						$tcnelement = new OpenDocument_Paragraph($listelem, $this);
						break;
					case 'text:span':
						$tcnelement = new OpenDocument_Span($listelem, $this);
						break;
					case 'text:h':
						$tcnelement = new OpenDocument_Heading($listelem, $this);
						break;
				}
				$listitem->children->append($tcnelement);
			}
			$listelement->children->append($listitem);
		}
		return $listelement;
	}

	public function processInclude() {
		global $current_user;
		require_once 'modules/Documents/Documents.php';
		if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
			include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
		} else {
			include 'modules/evvtgendoc/commands_en.php';
		}
		$strlen_includeGD = strlen($includeGD);
		//foreach ($this->contentXPath->evaluate('//*[count(*) = 0]') as $pnode) {
		foreach ($this->contentXPath->evaluate('//text:p[contains(text(), "'.$includeGD.'")]') as $pnode) {
			$texto_p='';
			foreach ($pnode->childNodes as $pnc) {
				$texto_p = (empty($pnc->wholeText) ? (empty($pnc->nodeValue) ? '' : $pnc->nodeValue) : $pnc->wholeText);
				if (!empty($texto_p) && is_string($texto_p)) {
					break; // utilizo el primer texto no vacío que encuentro
				}
			}
			$docno = substr($texto_p, $strlen_includeGD);
			$docno = trim($docno, ' }');
			if ($docno!='Documents') {
				$pth = Documents::getAttachmentPath($docno);
				if ($pth!='') {
					$inccontentDOM = new DOMDocument('1.0', 'UTF-8');
					if ($inccontentDOM->loadXML(ZipWrapper::read($pth, self::FILE_CONTENT))) {
						OpenDocument::debugmsg('INCLUDING FILE: '.$pth);
						// include content
						$innodelist = $inccontentDOM->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:office:1.0', 'body');
						$xmlText = simplexml_import_dom($innodelist->item(0)->firstChild);
						$nxml = $xmlText->asXML();
						unset($xmlText);
						$nxml = preg_replace('/<office:text.*>/U', '<text:section>', $nxml);
						$nxml = str_replace('</office:text>', '</text:section>', $nxml);
						$nxml = '<?xml version="1.0" encoding="UTF-8"?>
						<office:document-content
							xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
							xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
							xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
							xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
							xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
							xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
							xmlns:xlink="http://www.w3.org/1999/xlink"
							xmlns:dc="http://purl.org/dc/elements/1.1/"
							xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
							xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
							xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
							xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
							xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
							xmlns:math="http://www.w3.org/1998/Math/MathML"
							xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
							xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
							xmlns:ooo="http://openoffice.org/2004/office"
							xmlns:ooow="http://openoffice.org/2004/writer"
							xmlns:oooc="http://openoffice.org/2004/calc"
							xmlns:dom="http://www.w3.org/2001/xml-events"
							xmlns:xforms="http://www.w3.org/2002/xforms"
							xmlns:xsd="http://www.w3.org/2001/XMLSchema"
							xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
							xmlns:rpt="http://openoffice.org/2005/report"
							xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
							xmlns:xhtml="http://www.w3.org/1999/xhtml"
							xmlns:grddl="http://www.w3.org/2003/g/data-view#"
							xmlns:officeooo="http://openoffice.org/2009/office"
							xmlns:tableooo="http://openoffice.org/2009/table"
							xmlns:drawooo="http://openoffice.org/2010/draw"
							xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
							xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"
							xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
							xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
							xmlns:css3t="http://www.w3.org/TR/css3-text/"
							office:version="1.2">'.$nxml.'</office:document-content>';
						$newdoc = new DOMDocument('1.0', 'UTF-8');
						$newdoc->loadXML($nxml);
						$nwnodelist = $newdoc->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:text:1.0', 'section');
						$newdoc = $this->contentDOM->importNode($nwnodelist->item(0), true);
						$pnode->parentNode->replaceChild($newdoc, $pnode);
						unset($newdoc);
						// include styles
						//$docstyles = $this->contentDOM->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:office:1.0', 'automatic-styles')->item(0);
						$innodelist = $inccontentDOM->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:office:1.0', 'automatic-styles')->item(0);
						foreach ($innodelist->childNodes as $incnode) {
							$newnode = $this->contentDOM->importNode($incnode, true);
							$this->styles->appendChild($newnode);
						}
						// include images
						$innodelist = $inccontentDOM->getElementsByTagNameNS('urn:oasis:names:tc:opendocument:xmlns:drawing:1.0', 'image');
						if ($innodelist->length>0) {
							$tmpdir = 'cache/gendocoutput/img'.$current_user->id;
							@mkdir($tmpdir);
							ZipWrapper::unlinkRecursive($tmpdir, false);
							$zipinc = new ZipArchive;
							$zipinc->open(realpath($pth));
							$zipinc->extractTo($tmpdir);
							$zipinc->close();
							foreach ($innodelist as $incnode) {
								$nifname = $incnode->getAttribute('xlink:href');
								$this->newImages[] = $tmpdir.'/'.$nifname;
								$mtype = $incnode->getAttribute('loext:mime-type');
								$this->makeFileEntryElement($nifname, $mtype);
							}
						}
						// $xp = new DOMXPath($inccontentDOM);
						// foreach ($xp->evaluate('//*[count(*) = 0]') as $incnode) {
						// 	$incitem = $this->contentDOM->importNode($incnode, true);
						// 	//$this->contentDOM->documentElement->appendChild($incitem);
						// 	// $incitem = $this->contentDOM->importNode($incnode->item($incns), true);
						// 	$this->contentDOM->documentElement->insertBefore($incitem, $pnode);
						// }
					}
				}
			}
		}
	}

	public function postprocessing($filename, $pFilename = null) {
		global $root_directory;

		if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
			include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
		} else {
			include 'modules/evvtgendoc/commands_en.php';
		}

		$xp = new DOMXPath($this->contentDOM);
		$search = $xp->evaluate('//text:p[contains(text(), "'.$insertindexGD.'")]');
		if ($search && $search->length>0) {
			$properties = array(
				'insertindexGD' => $insertindexGD,
			);
			if (empty($pFilename)) {
				$pFilename = tempnam('/tmp', 'gendoc-');
			}
			$handle = fopen($pFilename, 'w');
			foreach ($properties as $key => $value) {
				fwrite($handle, "{$key} = {$value}\n");
			}
			fclose($handle);
			// Process and save
			$filename = escapeshellarg('file://'.$filename);
			$command = "{$root_directory}modules/evvtgendoc/unoservice.sh {$pFilename} {$filename} {$filename}";
			$status = exec($command);
			$this->debugmsg('Post processing: '.json_encode(array($command, $status)));
			// Remove temp files
			unlink($pFilename);
		} else {
			$this->debugmsg('Post processing No Index');
		}
	}

	public static function debugmsg($msg, $isbranch = false) {
		if (OpenDocument::$debug) {
			if ($isbranch) {
				$msg = OpenDocument::showrama($msg);
			}
			if (is_array($msg)) {
				$msg = var_export($msg, true);
			}
			switch (OpenDocument::$debug_output) {
				case 'file':
					error_log($msg."\n", 3, OpenDocument::GENDOCCACHE.'/gddebug.log');
					break;
				case 'screen':
				default:
					echo $msg.'<br>';
			}
		}
	}

	public static function showrama($obj) {
		if (get_class($obj)=='ArrayObject') {
			$iterat=$obj;
		} else {
			$iterat=$obj->getChildren();
		}
		$output = '';
		foreach ($iterat as $child) {
			//if (is_array($child)) {
			if (get_class($child) == 'OpenDocument_TextElement') {
				$output .= $child->text.'<br>';
			} elseif (get_class($child) == 'OpenDocument_Paragraph' || get_class($child) == 'OpenDocument_Span') {
				$pnode=$child->getNode();
				$pnodecld=$pnode->childNodes;
				$texto_p='';
				foreach ($pnodecld as $pnc) {
					$texto_p = (empty($pnc->wholeText) ? (empty($pnc->nodeValue) ? '' : $pnc->nodeValue) : $pnc->wholeText);
					if (!empty($texto_p) && is_string($texto_p)) {
						break; // utilizo el primer texto no vacío que encuentro
					}
				}
				$output .= $texto_p.'<br>';
			}
		}
		return $output;
	}

	/**
	 * Generate OpenDocument combined document
	 *
	 * @param OpenDocument/ArrayObject $obj
	 * @access public
	 */
	public function GenDoc($originFile, $id, $module, $root_module = null, $documentid = null) {
		global $parentArray, $iter_modules, $rootmod, $template_id;
		if (!is_null($root_module)) {
			$rootmod = $root_module[0];
			$iter_modules[$root_module[0]] = array($root_module[1]);
		}
		if (!is_null($documentid)) {
			$template_id = $documentid;
		}
		$startcompile = microtime(true);
		$this->debugmsg($originFile.' Init Compile');
		$this->debugmsg('Parameters');
		$this->debugmsg(array(
			'ID' => $id,
			'MODULE' => $module,
			'ROOT' => $root_module,
			'DOCID' => $documentid
		));
		$obj = new OpenDocument($originFile);
		$this->changedImages=array();
		$this->newImages=array();
		$this->originGenDocStyles = $obj->getStyles();
		$styleXML = $obj->stylesDOM->saveXML();
		$endcompile = microtime(true)-$startcompile;
		$this->debugmsg($originFile." START Compile $endcompile");
		$styleXML = compile($styleXML, $id, $module, true);
		$endcompile = microtime(true)-$startcompile;
		$this->debugmsg($originFile." START Styles $endcompile");
		$this->stylesDOM->loadXML($styleXML);
		$this->metaDOM = $obj->metaDOM;
		$this->settingsDOM = $obj->settingsDOM;
		$this->manifestDOM = $obj->manifestDOM;
		$fonts_info= $obj->getFontsInfo();
		$this->addFonts($fonts_info);
		$this->copyStyles($obj->styles);
		array_push($parentArray, $this);
		$endcompile = microtime(true)-$startcompile;
		$this->debugmsg($originFile." START toGenDoc $endcompile");
		set_time_limit(0);
		$this->toGenDoc($obj, $id, $module, $root_module);
		$this->processInclude();
		$endcompile = microtime(true)-$startcompile;
		$this->debugmsg("END GenDOC $endcompile s");
	}

	/**
	 * Generate OpenDocument combined document
	 *
	 * @param OpenDocument/ArrayObject $obj
	 * @access public
	 */
	public function toGenDoc($obj, $id, $module, $root_module = null) {
		global $siincluir, $changedImage, $newImageAdded, $includeOriginalDirective, $repe;
		global $ramaparacada, $pcincluir, $tempincluir, $iter_modules, $rootmod, $parentArray;
		//commands
		if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
			include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
		} else {
			include 'modules/evvtgendoc/commands_en.php';
		}
		$lenforeachGD=strlen($foreachGD);
		$lenforeachEndGD=strlen($foreachEndGD);
		$lenimageGD=strlen($imageGD);
		$lenincludeGD=strlen($includeGD);
		$lenifexistsGD=strlen($ifexistsGD);
		$lenifnotexistsGD=strlen($ifnotexistsGD);
		$lenifexistsEndGD=strlen($ifexistsEndGD);
		$lenifnotexistsEndGD=strlen($ifnotexistsEndGD);
		if (get_class($obj)=='ArrayObject') {
			$iterat=$obj;
		} elseif (!is_null($obj)) {
			$iterat=$obj->getChildren();
		}
		$includeOriginalDirective = isset($includeOriginalDirective) ? $includeOriginalDirective : '';
		foreach ($iterat as $child) {
			if ($pcincluir) {
				$pnode=$child->getNode();
				$pnodecld=$pnode->childNodes;
				$texto_p='';
				foreach ($pnodecld as $pnc) {
					$texto_p = (empty($pnc->wholeText) ? (empty($pnc->nodeValue) ? '' : $pnc->nodeValue) : $pnc->wholeText);
					if (!empty($texto_p) && is_string($texto_p)) {
						break; // utilizo el primer texto no vacío que encuentro
					}
				}
				$texto_plow = strtolower($texto_p);
				if (substr($texto_plow, 0, $lenforeachGD)==$foreachGD) {
					$spn=$child->createSpan('putforeachhere'); // Marcamos el inicio paracada para saber donde poner el siguiente una vez compilado
					$ramaparacada->append($spn);
					$this->contextoParacada[$this->contextoActual]['ramaparacada']=$ramaparacada; // guardo contexto modulos encontrados
					$this->contextoActual++;
					$ramaparacada= new ArrayObject;
					// obtener condición
					$condicionparacada=rtrim(trim(substr($texto_p, $lenforeachGD)), '}');
					$module_pcada = getModuleFromCondition($this->contextoParacada[$this->contextoActual-1]['condicion']);
					$this->contextoParacada[$this->contextoActual]=array(  // guardo contexto modulos encontrados
						'condicion'=>$condicionparacada,
						'module'=>($module_pcada=='Organization' ? 'cbCompany' : $module_pcada),
					);
				} elseif (substr($texto_plow, 0, $lenforeachEndGD)==$foreachEndGD) {
					$this->contextoParacada[$this->contextoActual]['ramaparacada']=$ramaparacada; // guardo contexto modulos encontrados
					if ($this->contextoActual>0) {
						$this->contextoActual--;
						$ramaparacada=$this->contextoParacada[$this->contextoActual]['ramaparacada'];
					} else {
						$pcincluir=false;
						// procesar paracada
						$this->contextoActual=0;
						$num_iter =iterations();
						$repe[] = 0;
						$last_repe = count($repe)-1;
						for ($repe[$last_repe]=1; $repe[$last_repe]<=$num_iter; $repe[$last_repe]++) {
							$ramaparacada=$this->contextoParacada[0]['ramaparacada'];
							$this->contextoParacada[0]['repe'] = $repe[$last_repe];
							$this->toGenDoc($ramaparacada, $id, $module);
							pop_iter_modules();
						}
						array_pop($repe);
						$this->contextoParacada=array();
						$includeOriginalDirective = '';
					}
				} else {
					$ramaparacada->append($child);
				}
				continue;
			}
			$hayqueincluir=(empty($siincluir) || $siincluir[count($siincluir)-1]);
			$topofarray=$parentArray[count($parentArray)-1];

			switch (get_class($child)) {
				case 'OpenDocument_Paragraph':
				case 'OpenDocument_Span':
					$pnode=$child->getNode();
					$pnodecld=$pnode->childNodes;
					$texto_p = '';
					foreach ($pnodecld as $pnc) {
						$texto_p = (empty($pnc->wholeText) ? (empty($pnc->nodeValue) ? '' : $pnc->nodeValue) : $pnc->wholeText);
						if (!empty($texto_p) && is_string($texto_p)) {
							break; // utilizo el primer texto no vacío que encuentro
						}
					}
					$texto_plow = strtolower($texto_p);
					if (substr($texto_plow, 0, $lenifexistsGD)==$ifexistsGD) {
						// obtener condición
						$condicion=rtrim(trim(substr($texto_p, $lenifexistsGD)), '}');
						// evaluar condición
						$cumple_cond = eval_existe($condicion, $id, $module);
						if ($cumple_cond && $hayqueincluir) {
							array_push($siincluir, true);
						} else {
							array_push($siincluir, false);
						}
						continue 2;
					}
					if (substr($texto_plow, 0, $lenifnotexistsGD)==$ifnotexistsGD) {
						// obtener condición
						$condicion=rtrim(trim(substr($texto_p, $lenifnotexistsGD)), '}');
						// evaluar condición
						$cumple_cond = eval_noexiste($condicion, $id, $module);
						if ($cumple_cond && $hayqueincluir) {
							array_push($siincluir, true);
						} else {
							array_push($siincluir, false);
						}
						continue 2;
					}
					if (substr($texto_plow, 0, $lenifexistsEndGD)==$ifexistsEndGD) {
						array_pop($siincluir);
						continue 2;
					}
					if (substr($texto_plow, 0, $lenifnotexistsEndGD)==$ifnotexistsEndGD) {
						array_pop($siincluir);
						continue 2;
					}
					if (substr($texto_plow, 0, $lenforeachGD)==$foreachGD) {
						$includeOriginalDirective = '';
						$pcincluir=true;
						$ramaparacada= new ArrayObject;
						// obtener condición
						$condicionparacada=rtrim(trim(substr($texto_p, $lenforeachGD)), '}');
						eval_paracada($condicionparacada, $id, $module);
						$this->contextoActual=0;
						$this->contextoParacada[0]=array(  // guardo contexto modulos encontrados
							'iter_modules'=>$iter_modules,
							'condicion'=>$condicionparacada,
							'module'=>($module == 'Organization' ? 'cbCompany' : $module),
							'moduleid'=>$id
						);
						continue 2;
					}
					if (substr($texto_plow, 0, 14)=='putforeachhere') {
						$this->saveContextoActual['ramaparacada']=$ramaparacada;
						// obtener contexto
						$this->contextoActual++;
						$ramaparacada=$this->contextoParacada[$this->contextoActual]['ramaparacada'];
						$condicionparacada=$this->contextoParacada[$this->contextoActual]['condicion'];
						$ctxmodule=$this->contextoParacada[$this->contextoActual]['module'];
						$ctxmodule = trim(preg_replace('/\*(\w|\s)+\*/', '', $ctxmodule));
						$modid=$iter_modules[$ctxmodule][0];
						eval_paracada($condicionparacada, $modid, $ctxmodule);
						$num_iter =iterations();
						$repe[] = 0;
						$last_repe = count($repe)-1;
						for ($repe[$last_repe]=1; $repe[$last_repe]<=$num_iter; $repe[$last_repe]++) {
							$this->toGenDoc($ramaparacada, $modid, $ctxmodule);
							pop_iter_modules();
						}
						array_pop($repe);
						$ramaparacada=$this->saveContextoActual['ramaparacada'];
						$this->contextoActual--;
						$includeOriginalDirective = '';
						continue 2;
					}
					if (substr($texto_plow, 0, $lenimageGD)==$imageGD) {
						$entidadimagen=rtrim(trim(substr($texto_p, $lenimageGD)), '}');
						$this->newImages[]=eval_imagen($entidadimagen, $id, $module);
						$newImageAdded=true;
						continue 2;
					}
					if (substr($texto_plow, 0, $lenincludeGD)==$includeGD) {
						$entidadincluir = rtrim(trim(substr($texto_p, $lenincludeGD)), '}');
						if ($includeOriginalDirective=='' && $entidadincluir=='Documents') {
							$includeOriginalDirective = 'Documents';
						}
						if ($includeOriginalDirective!='') {
							$entidadincluir = $includeOriginalDirective;
						}
						$reemp = $includeGD.eval_incluir($entidadincluir, $id, $module).'}';
						$pnc->replaceData(0, strlen($pnc->wholeText), $reemp);
					}
					if ($hayqueincluir) {
						if ((get_class($child))=='OpenDocument_Span') {
							$pgsp = $topofarray->createSpan(compile($child->text, $id, $module));
						} else {
							$pgsp = $topofarray->createParagraph(compile($child->text, $id, $module));
						}
						OpenDocument::copyAttributes($child, $pgsp);
						array_push($parentArray, $pgsp);
						$this->toGenDoc($child, $id, $module);
						array_pop($parentArray);
					}
					break;
				case 'OpenDocument_TextElement':
				case 'OpenDocument_TextTab':
				case 'OpenDocument_TextSpace':
				case 'OpenDocument_Footnote':
				case 'OpenDocument_DrawEGeometry':
				case 'OpenDocument_DrawCustomShape':
				case 'OpenDocument_DrawGraph':
				case 'OpenDocument_DrawConnector':
				case 'OpenDocument_DrawLine':
				case 'OpenDocument_DrawRect':
				case 'OpenDocument_DrawObject':
				case 'OpenDocument_DrawHandle':
				case 'OpenDocument_DrawEquation':
				case 'OpenDocument_BookmarkStart':
				case 'OpenDocument_BookmarkEnd':
				case 'OpenDocument_ReferenceMark':
				case 'OpenDocument_ReferenceRef':
				case 'OpenDocument_NoteBody':
				case 'OpenDocument_NoteCitation':
				case 'OpenDocument_TextDate':
				case 'OpenDocument_TextTime':
				case 'OpenDocument_PageNumber':
				case 'OpenDocument_PageCount':
				case 'OpenDocument_InfoSubject':
				case 'OpenDocument_InfoTitle':
				case 'OpenDocument_InfoAuthor':
				case 'OpenDocument_TextLineBreak':
				case 'OpenDocument_Frame':
				case 'OpenDocument_FrameTextBox':
				case 'OpenDocument_FrameImage':
				case 'OpenDocument_List':
				case 'OpenDocument_ListItem':
				case 'OpenDocument_Section':
				case 'OpenDocument_Table':
				case 'OpenDocument_TableColumn':
				case 'OpenDocument_TableRow':
				case 'OpenDocument_TableHeaderRow':
				case 'OpenDocument_TableCell':
				case 'OpenDocument_TableCoveredCell':
				case 'OpenDocument_Heading':
				case 'OpenDocument_Hyperlink':
					if ($hayqueincluir) {
						$this->compiletoDoc($child, $id, $module);
					}
					break;
				case 'OpenDocument_TextSoftPageBreak':
					continue 2; // Me lo cargo y punto
				break;
				default:
					$domNode = $this->contentDOM->importNode($child, true);
					if (!is_null($domNode) && get_class($domNode)=='DOMElement') {
						$this->cursor->appendChild($domNode);
					}
					break;
			}
		}
	}

	/**
	 * Generate OpenDocument combined document
	 *
	 * @param OpenDocument/ArrayObject $obj
	 * @access public
	 */
	public function GenHTML($originFile, $id, $module) {
		global $parentArray;
		$obj = new OpenDocument($originFile);
		$this->originGenDocStyles = $obj->getStyles();
		$this->stylesDOM=$obj->stylesDOM;
		array_push($parentArray, $this);
		return $this->toGenHTML($obj, $id, $module);
	}

	public function toGenHTML($obj, $id, $module) {
		global $iter_modules, $siincluir, $ramaparacada, $pcincluir, $repe;
		$html = '';
		//commands
		if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
			include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
		} else {
			include 'modules/evvtgendoc/commands_en.php';
		}
		$lenforeachGD=strlen($foreachGD);
		$lenforeachEndGD=strlen($foreachEndGD);
		$lenifexistsGD=strlen($ifexistsGD);
		$lenifnotexistsGD=strlen($ifnotexistsGD);
		$lenifexistsEndGD=strlen($ifexistsEndGD);
		$lenifnotexistsEndGD=strlen($ifnotexistsEndGD);
		if (get_class($obj)=='ArrayObject') {
			$iterat=$obj;
		} else {
			$iterat=$obj->getChildren();
		}
		foreach ($iterat as $child) {
			if ($pcincluir) {
				$pnode=$child->getNode();
				$pnodecld=$pnode->childNodes;
				$texto_p=($pnodecld->length==0 ? '' : $pnodecld->item(0)->wholeText);
				if (strtolower(substr($texto_p, 0, $lenforeachEndGD))==$foreachEndGD) {
					$pcincluir=false;
					// procesar paracada
					$num_iter = iterations();
					for ($repe=1; $repe<=$num_iter; $repe++) {
						$html .= $this->toGenHTML($ramaparacada, $id, $module);
						pop_iter_modules();
					}
					unset($ramaparacada);
				} else {
					$ramaparacada->append($child);
				}
				continue;
			}
			$hayqueincluir=(empty($siincluir) || $siincluir[count($siincluir)-1]);
			switch (get_class($child)) {
				case 'OpenDocument_TextElement':
					if ($hayqueincluir) {
						$html .= compile($child->text, $id, $module);
					}
					break;
				case 'OpenDocument_Table':
					if ($hayqueincluir) {
						$html .= "<table border=1>";
						$html .= $this->toGenHTML($child, $id, $module);
						$html .= "</table>";
					}
					break;
				case 'OpenDocument_TableRow':
					if ($hayqueincluir) {
						$html .= "<tr>";
						$html .= $this->toGenHTML($child, $id, $module);
						$html .= "</tr>";
					}
					break;
				case 'OpenDocument_TableCell':
					if ($hayqueincluir) {
						$html .= "<td>";
						$html .= $this->toGenHTML($child, $id, $module);
						$html .= "</td>";
					}
					break;
				case 'OpenDocument_Paragraph':
					$pnode=$child->getNode();
					$pnodecld=$pnode->childNodes;
					$texto_p=($pnodecld->length==0 ? '' : $pnodecld->item(0)->wholeText);
					$texto_plow = strtolower($texto_p);
					if (substr($texto_plow, 0, $lenifexistsGD)==$ifexistsGD) {
						// obtener condición
						$condicion=rtrim(substr($texto_p, $lenifexistsGD), '}');
						// evaluar condición
						$cumple_cond = eval_existe($condicion, $id, $module);
						if ($cumple_cond && $hayqueincluir) {
							array_push($siincluir, true);
						} else {
							array_push($siincluir, false);
						}
						continue 2;
					}
					if (substr($texto_plow, 0, $lenifnotexistsGD)==$ifnotexistsGD) {
						// obtener condición
						$condicion=rtrim(substr($texto_p, $lenifnotexistsGD), '}');
						// evaluar condición
						$cumple_cond = eval_noexiste($condicion, $id, $module);
						if ($cumple_cond && $hayqueincluir) {
							array_push($siincluir, true);
						} else {
							array_push($siincluir, false);
						}
						continue 2;
					}
					if (substr($texto_plow, 0, $lenifexistsEndGD)==$ifexistsEndGD) {
						array_pop($siincluir);
						continue 2;
					}
					if (substr($texto_plow, 0, $lenifnotexistsEndGD)==$ifnotexistsEndGD) {
						array_pop($siincluir);
						continue 2;
					}
					if (substr($texto_plow, 0, $lenforeachGD)==$foreachGD) {
						$pcincluir=true;
						$ramaparacada= new ArrayObject;
						// obtener condición
						$condicionparacada=rtrim(substr($texto_p, $lenforeachGD), '}');
						eval_paracada($condicionparacada, $id, $module);
						continue 2;
					}
					$html .= '<p>';
					$html .= $this->toGenHTML($child, $id, $module);
					$html .= '</p>';
					break;
				case 'OpenDocument_Span':
					if ($hayqueincluir) {
						$html .= '<span>';
						$html .= $this->toGenHTML($child, $id, $module);
						$html .= '</span>';
					}
					break;
				case 'OpenDocument_Heading':
					if ($hayqueincluir) {
						$html .= '<h' . $child->level . '>';
						$html .= $this->toGenHTML($child, $id, $module);
						$html .= '</h' . $child->level . '>';
					}
					break;
				case 'OpenDocument_Hyperlink':
					if ($hayqueincluir) {
						$html .= '<a href="' . compile($child->location, $id, $module) . '" target="' . compile($child->target, $id, $module) . '">';
						$html .= $this->toGenHTML($child, $id, $module);
						$html .= '</a>';
					}
					break;
			}
		}
		return $html;
	}
	/**
	 * Delete document child element
	 *
	 * @param OpenDocument_Element $element
	 * @access public
	 */
	public function deleteElement(OpenDocument_Element $element) {
		$this->cursor->removeChild($element->getNode());
		unset($element);
	}

	/**
	 * Set maximum values of style name suffixes
	 *
	 * @access private
	 */
	private function setMax() {
		$classes = array('OpenDocument_Paragraph', 'OpenDocument_Heading', 'OpenDocument_Hyperlink');
		$max = array();
		if ($this->cursor instanceof DOMNode) {
			$nodes = $this->cursor->getElementsByTagName('*');
			foreach ($nodes as $node) {
				if ($node->hasAttributeNS(self::NS_TEXT, 'style-name')) {
					$style_name = $node->getAttributeNS(self::NS_TEXT, 'style-name');
					foreach ($classes as $class) {
						$reflection = new ReflectionClass($class);
						$prefix = $reflection->getConstant('styleNamePrefix');
						if (preg_match("/^$prefix(\d)+$/", $style_name, $m) && (!isset($max[$class]) || $max[$class] < $m[1])) {
							$max[$class] = $m[1];
						}
					}
				}
			}
		}
		foreach ($classes as $class) {
			$method = new ReflectionMethod($class, 'setStyleNameMaxNumber');
			if (!isset($max[$class])) {
				$max[$class] = 0;
			}
			$method->invoke(null, $max[$class]);
		}
	}

	/************************* Elements **************************/

	/**
	 * Create OpenDocument_Paragraph
	 *
	 * @param string $text optional
	 * @return OpenDocument_Paragraph
	 * @access public
	 */
	public function createParagraph($text = '') {
		return OpenDocument_Paragraph::instance($this, $text);
	}

	public function createSpan($text) {
		return OpenDocument_Span::instance($this, $text);
	}
	public function createFrame($text, $anchortype, $width, $height, $zindex, $framename, $x, $y, $anchorpagenumber) {
		return OpenDocument_Frame::instance($this, $text, $anchortype, $width, $height, $zindex, $framename, $x, $y, $anchorpagenumber);
	}

	/**
	 * Create Open_document_Heading
	 *
	 * @param string $text
	 * @param integer $level
	 * @return OpenDocument_Heading
	 * @access public
	 */
	public function createHeading($text = '', $level = 1) {
		return OpenDocument_Heading::instance($this, $text, $level);
	}

	/**
	 * Create OpenDocument_Bookmark
	 *
	 * @param string $name
	 * @param string $type
	 * @return OpenDocument_Bookmark
	 * @access public
	 * @todo finish method
	 */
	public function createBookmark($name, $type = 'start') {
		if (!in_array($type, array('start', 'end'))) {
			$type = 'start';
		}
		$bookmark = new OpenDocument_Bookmark($this->contentDOM->createElementNS(self::NS_TEXT, 'bookmark-' . $type), $this, $name, $type);
		$this->cursor->appendChild($bookmark->getNode());
		$bookmark->getNode()->setAttributeNS(self::NS_TEXT, 'name', $name);
		return $bookmark;
	}


	/**
	 * Create OpenDocument_Table
	 *
	 * @param string $text optional
	 * @return OpenDocument_Table
	 * @access public
	 */
	public function createTable($subtable = '') {
		return OpenDocument_Table::instance($this, $subtable);
	}
	public function createSection($subtable = '') {
		return OpenDocument_Section::instance($this);
	}
	public function createList($contnum = '') {
		return OpenDocument_List::instance($this, $contnum);
	}
	public function createListItem() {
		return OpenDocument_ListItem::instance($this);
	}

	/********************* Styles ****************************/

	/**
	 * Apply style information to object
	 * If object has no style information yet, then create new style node
	 * If object style information is similar to other object's style info, then apply the same style name
	 *     And if object old style information was not shared with other objects then delete old style info
	 *     Else leave old style info
	 * Else just add new style description
	 *
	 * @param string $style_name
	 * @param string $name
	 * @param mixed $value
	 * @param OpenDocument_StyledElement $object
	 * @return string $style_name
	 */
	public function applyStyle($style_name, $name, $value, OpenDocument_StyledElement $object, $elemtype = '') {
		//check if other nodes have the same style name
		$nodes = $this->cursor->getElementsByTagName('*');
		$style=null;
		foreach ($nodes as $node) {
			if ($node->hasAttributeNS(self::NS_TEXT, 'style-name') && $node->getAttributeNS(self::NS_TEXT, 'style-name') == $style_name) {
				$style=$node;
				break;
			}
		}

		$generate = false;

		if (empty($style)) {
			if (empty($style_name)) {
				$style_name = $object->generateStyleName();
			}
			$style = $this->contentDOM->createElementNS(self::NS_STYLE, 'style');
			$style->setAttributeNS(self::NS_STYLE, 'name', $style_name);
			$style->setAttributeNS(self::NS_STYLE, 'family', ($name=='family' ? $value : 'paragraph'));
			$this->styles->appendChild($style);
			return $style->getAttributeNS(self::NS_STYLE, 'name');
		} else {
			$style = $this->getStyleNode($style_name)->cloneNode(true);
			$this->styles->appendChild($style);
			$generate = true;
			$style_name = $object->generateStyleName();
			$style->setAttributeNS(self::NS_STYLE, 'name', $style_name);
		}

		if ($name=='list-style-name') {
			$style->setAttributeNS(self::NS_STYLE, 'list-style-name', $value);
			return $style->getAttributeNS(self::NS_STYLE, 'name');
		} elseif ($name=='parent-style-name') {
			$style->setAttributeNS(self::NS_STYLE, 'parent-style-name', $value);
			return $style->getAttributeNS(self::NS_STYLE, 'name');
		}

		if (empty($elemtype)) {
			$elemtype='text';
		}
		$nodes = $style->getElementsByTagNameNS(self::NS_STYLE, (strpos($elemtype, 'properties') ? $elemtype : $elemtype.'-properties'));
		if ($nodes->length) {
			$text_properties = $nodes->item(0);
		} else {
			$text_properties = $this->contentDOM->createElementNS(self::NS_STYLE, (strpos($elemtype, 'properties') ? $elemtype : $elemtype.'-properties'));
			$style->appendChild($text_properties);
		}
		if (in_array($name, $this->NS_FO_attrib)) {
			$text_properties->setAttribute('fo:'.$name, $value);
		} elseif (in_array($name, $this->NS_TABLE_attrib)) {
			$text_properties->setAttribute('table:'.$name, $value);
		} elseif (in_array($name, $this->NS_DRAW_attrib)) {
			$text_properties->setAttribute('draw:'.$name, $value);
		} elseif (!is_array($value)) {
			$text_properties->setAttribute('style:'.$name, $value);
		}

		//find alike style
		$nodes = $this->styles->getElementsByTagNameNS(self::NS_STYLE, 'style');
		foreach ($nodes as $node) {
			if (!$style->isSameNode($node) && $this->compareChildNodes($style, $node)) {
				$style->parentNode->removeChild($style);
				return $node->getAttributeNS(self::NS_STYLE, 'name');
			}
		}

		if ($generate) {
			$style_name = $object->generateStyleName();
			$style->setAttributeNS(self::NS_STYLE, 'name', $style_name);
		}
		return $style->getAttributeNS(self::NS_STYLE, 'name');
	}

	/**
	 * Add array of style values
	 *
	 * @param string $style_name
	 * @param array $properties
	 * @return array
	 */
	public function addStyles($node, $elem, $elemtype, $keepname = false) {
		$style_name = $this->getStyleName($node);
		$reservedstyl=implode('|', OpenDocument::$reservedstyl);
		if (preg_match("[$reservedstyl]", $style_name)) {
			$elem->getNode()->setAttributeNS(OpenDocument::NS_TEXT, 'style-name', $style_name);
			return 0;
		}
		if ($this instanceof OpenDocument) {
			$document = $this;
		} elseif ($this instanceof OpenDocument_Element) {
			$document = $this->getDocument();
		}
		if (empty($document->originGenDocStyles)) {
			return 0;
		}
		if (!array_key_exists($style_name, $document->originGenDocStyles)) {
			return 0;
		}
		$stylebranch=$document->originGenDocStyles[$style_name];
		if (!is_array($stylebranch)) {
			return 0;
		}
		while ($level1style=current($stylebranch)) {
			if (is_array($level1style)) {
				$level1et = key($stylebranch);
				$level1stbranch=$level1style;
				while ($level2style=current($level1stbranch)) {
					if (is_array($level2style)) {
						$level2et=key($level1stbranch);
						$level2stbranch=$level2style;
						while ($level3style=current($level2stbranch)) {
							if (key($level2stbranch)!='name' || (key($level2stbranch)=='name' && $keepname)) {
								$elem->applyStyle(key($level2stbranch), $level3style, $level2et);
							}
							next($level2stbranch);
						}
					} else {
						if (key($level1stbranch)!='name' || (key($level1stbranch)=='name' && $keepname)) {
							$elem->applyStyle(key($level1stbranch), $level2style, $level1et);
						}
					}
					next($level1stbranch);
				}
			} else {
				if (key($stylebranch)!='name' || (key($stylebranch)=='name' && $keepname)) {
					$elem->applyStyle(key($stylebranch), $level1style, $elemtype);
				}
			}
			next($stylebranch);
		}
	}

	/**
	 * Add array of List style values
	 *
	 * @param string $style_name
	 * @param array $properties
	 * @return array
	 */
	public function addListStyles($node) {
		$stylename=$this->getStyleName($node);
		if (empty($stylename)) {
			return;
		}
		if ($this->hasListStyleNode($stylename)) {
			return; // Si ya lo tenemos, no lo repetimos
		}

				$style = $this->contentDOM->createElement('text:list-style');
		$style->setAttribute('style:name', $stylename);
		$stylebranch=$this->originGenDocStyles[$stylename];
		while ($level1style=current($stylebranch)) {
			$level1name=substr(key($stylebranch), 0, strpos(key($stylebranch), '$$$'));
			if (is_array($level1style)) {
				$substyle = $this->contentDOM->createElement('text:'.$level1name);
				$level1stbranch=$level1style;
				while ($level2style=current($level1stbranch)) {
					$level2name=key($level1stbranch);
					if (is_array($level2style)) {
						$substyle2 = $this->contentDOM->createElement('style:'.$level2name);
						$level2stbranch=$level2style;
						while ($level3style=current($level2stbranch)) {
							if (in_array(key($level2stbranch), $this->NS_LISTSTYLE)) {
								$substyle2->setAttribute('style:'.key($level2stbranch), $level3style);
							} else {
								$substyle2->setAttribute('text:'.key($level2stbranch), $level3style);
							}
							next($level2stbranch);
						}
						$substyle->appendChild($substyle2);
					} else {
						if (in_array($level2name, $this->NS_LISTSTYLE)) {
							$substyle->setAttribute('style:'.$level2name, $level2style);
						} else {
							$substyle->setAttribute('text:'.$level2name, $level2style);
						}
					}
					next($level1stbranch);
				}
				$style->appendChild($substyle);
			} else {
				if (in_array($level1name, $this->NS_LISTSTYLE)) {
					$style->setAttribute('style:'.$level1name, $level1style);
				} else {
					$style->setAttribute('text:'.$level1name, $level1style);
				}
			}
			next($stylebranch);
		}
		$this->styles->appendChild($style);
	}

	/**
	 * Add array of Date style values
	 *
	 */
	public function addDateTimeStyles($odoc, $isdatestyle = 'true') {
		$stylename=$odoc->getStyleName();
		if (empty($stylename)) {
			return;
		}
		if ($this->hasListStyleNode($stylename)) {
			return; // Si ya lo tenemos, no lo repetimos
		}

		$style = $this->contentDOM->createElement(($isdatestyle ? 'number:date-style' : 'number:time-style'));
		$style->setAttribute('style:name', $stylename);
		$stylebranch=$this->originGenDocStyles[$stylename];
		foreach ($stylebranch as $level1name => $datestyle) {
			if (is_array($datestyle)) {
				$substyle = $this->contentDOM->createElement('number:'.$level1name);
				$level1stbranch=$datestyle;
				while ($level2style=current($level1stbranch)) {
					$level2name=key($level1stbranch);
					if ($level2name=='name') {
						$substyle->setAttribute('style:'.$level2name, $level2style);
					} else {
						$substyle->setAttribute('number:'.$level2name, $level2style);
					}
					next($level1stbranch);
				}
				$style->appendChild($substyle);
			} else {
				if ($level1name=='name') {
					$style->setAttribute('style:'.$level1name, $datestyle);
				} elseif (substr($level1name, 0, 4)=='text') {
					$substyle = $this->contentDOM->createElement('number:text', $datestyle);
					$style->appendChild($substyle);
				} else {
					$style->setAttribute('number:'.$level1name, $datestyle);
				}
			}
		}
		$this->styles->appendChild($style);
	}

	/**
	 * Get array of style values
	 *
	 * @param string $style_name
	 * @param array $properties
	 * @return array
	 */
	public function getStyle($style_name, $properties) {
		$style = array();
		if ($node = $this->getStyleNode($style_name)) {
			$nodes = $node->getElementsByTagNameNS(self::NS_STYLE, 'text-properties');
			if ($nodes->length) {
				$text_properties = $nodes->item(0);
				foreach ($properties as $property) {
					list($prefix, $name) = explode(':', $property);
					$ns = $text_properties->lookupNamespaceURI($prefix);
					$style[$property] = $text_properties->getAttributeNS($ns, $name);
				}
			}
		}
		return $style;
	}

	/**
	 * Get style node
	 *
	 * @param string $style_name
	 * @return DOMNode
	 */
	private function getStyleNode($style_name) {
		$nodes = $this->styles->getElementsByTagNameNS(self::NS_STYLE, 'style');
		foreach ($nodes as $node) {
			if ($node->getAttributeNS(self::NS_STYLE, 'name') == $style_name) {
				return $node;
			}
		}
		return false;
	}

	/**
	 * Get style node
	 *
	 * @param string $style_name
	 * @return DOMNode
	 */
	private function hasListStyleNode($style_name) {
		$nodes = $this->styles->getElementsByTagName('text:list-style');
		foreach ($nodes as $node) {
			$attributes = $node->attributes;
			for ($i = 0; $i < $attributes->length; $i ++) {
				$name = $attributes->item($i)->name;
				$value = $attributes->item($i)->value;
				if ($name=='style:name' && $value==$style_name) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Get styles
	 *
	 */
	public function getStyles() {
		$nodes = $this->styles->getElementsByTagNameNS(self::NS_STYLE, 'style');
		$styles_arr = array();
		foreach ($nodes as $node) {
			$stylename = $node->getAttributeNS(self::NS_STYLE, 'name');
			$styles_arr[$stylename] = $this->getStyleInfo($node);
		}
		$nodes = $this->styles->getElementsByTagNameNS(self::NS_TEXT, 'list-style');
		foreach ($nodes as $node) {
			$stylename = $node->getAttributeNS(self::NS_STYLE, 'name');
			$styles_arr[$stylename] = $this->getListStyleInfo($node);
		}
		$nodes = $this->styles->getElementsByTagNameNS(OpenDocument::NS_NUMBER, 'date-style');
		foreach ($nodes as $node) {
			$stylename = $node->getAttributeNS(OpenDocument::NS_STYLE, 'name');
			$styles_arr[$stylename] = $this->getStyleInfo($node, true);
		}
		$nodes = $this->styles->getElementsByTagNameNS(OpenDocument::NS_NUMBER, 'time-style');
		foreach ($nodes as $node) {
			$stylename = $node->getAttributeNS(OpenDocument::NS_STYLE, 'name');
			$styles_arr[$stylename] = $this->getStyleInfo($node, true);
		}
		return $styles_arr;
	}

	/**
	 * Copy Attributes
	 *
	 */
	public function copyAttributes($orgNode, $destNode, $changedImage = '') {
		$attributes = $orgNode->getNode()->attributes;
		if (!is_null($attributes)) {
			foreach ($attributes as $attrib) {
				$aname = $attrib->prefix.':'.$attrib->name;
				$value = $attrib->value;
				//echo "$aname = $value<br>";
				if ($attrib->name=='href' && $changedImage!='') { // tenemos que cambiarlo, sustitución imagen paracada
					$value='Pictures/'.$changedImage;
				}
				if ($attrib->name=='name') { // en las imágenes, falla cuando el frame tiene el mismo nombre, añadimos una cadena única en todos los atributos name
					$value=$attrib->value.uniqid();
				}
				if (!$destNode->getNode()->hasAttribute($aname)) {
					$destNode->getNode()->setAttribute($aname, $value);
				}
			}
		}
		return true;
	}

	/**
	 * get ImageNode Name
	 *
	 */
	public function getImageNodeName($imgNode) {
		$attributes = $imgNode->getNode()->attributes;
		foreach ($attributes as $attrib) {
			if ($attrib->name=='href') {
				return $attrib->value;
			}
		}
		return '';
	}

	/**
	 * Copy styles
	 *
	 */
	public function copyStyles($orgStyles) {
		foreach ($orgStyles->childNodes as $domElement) {
			$domNode = $this->contentDOM->importNode($domElement, true);
			$this->styles->appendChild($domNode);
		}
		return true;
	}

	/**
	 * Get style info
	 *
	 */
	public function getStyleInfo($node, $isdatetime = 'false') {
		$key = $node->ownerDocument->saveXML($node);
		list($cachedresult,$found) = VTCacheUtils::lookupCachedInformation('getStyleInfo::'.$key);
		if ($found) {
			return $cachedresult;
		}
		$ret=array();
		$attributes = $node->attributes;
		for ($i = 0; $i < $attributes->length; $i ++) {
			$name = $attributes->item($i)->name;
			$value = $attributes->item($i)->value;
			$ret[$name] = $value;
		}
		//if (method_exists($node,'getElementsByTagNameNS')) {
		//  $morenodes = $node->getElementsByTagNameNS(self::NS_STYLE, '*');
		if (method_exists($node, 'getElementsByTagName')) {
			$morenodes = $node->getElementsByTagName('*');
		} else {
			$morenodes = array();
		}
		$cnt=0;
		foreach ($morenodes as $n) {
			list($s,$tname)=explode(':', $n->tagName);
			if ($isdatetime && $tname=='text') {
				$ret[$tname.$cnt++] = $n->nodeValue;
			} else {
				$ret[$tname] = $this->getStyleInfo($n);
			}
		}
		VTCacheUtils::updateCachedInformation('getStyleInfo::'.$key, $ret);
		return $ret;
	}

	/**
	 * Get style info
	 *
	 */
	public function getListStyleInfo($node) {
		if (method_exists($node, 'getElementsByTagNameNS')) {
			$morenodes = $node->getElementsByTagNameNS(self::NS_TEXT, 'list-level-style-bullet');
			if ($morenodes->length==0) {
				$morenodes = $node->getElementsByTagNameNS(self::NS_TEXT, 'list-level-style-number');
			}
		} else {
			$morenodes = array();
		}
		$level=1;
		foreach ($morenodes as $n) {
			list($s,$tname)=explode(':', $n->tagName);
			$ret[$tname.'$$$'.$level] = $this->getStyleInfo($n);
			$level++;
		}
		return $ret;
	}

	/**
	 * Get style name
	 *
	 */
	public function getStyleName($node) {
		$ret='';
		if (empty($node->attributes)) {
			return $ret;
		}
		$attributes = $node->attributes;
		for ($i = 0; $i < $attributes->length; $i ++) {
			$name = $attributes->item($i)->name;
			$value = $attributes->item($i)->value;
			if ($name == 'style-name' || $name == 'data-style-name') {
				return $value;
			}
		}
		return $ret;
	}

	/**
	 * Check if two style info are similar
	 *
	 * @param string $style_name1
	 * @param string $style_name2
	 * @return bool
	 */
	private function compareStyles($style_name1, $style_name2) {
		$style_node1 = $this->getStyleNode($style_name1);
		$style_node2 = $this->getStyleNode($style_name2);
		return $this->compareNodes($style_node1, $style_node2);
	}

	/********************* Fonts ****************************/

	/**
	 * Get array of declared font names
	 *
	 * @return array
	 */
	public function getFonts() {
		$nodes = $this->fonts->getElementsByTagNameNS(self::NS_STYLE, 'font-face');
		$fonts_info = array();
		foreach ($nodes as $node) {
			$fonts_info[] = $node->getAttributeNS(self::NS_STYLE, 'name');
		}
		return $fonts_info;
	}

	/**
	 * Get array of declared font info
	 *
	 * @return array
	 */
	public function getFontsInfo() {
		$nodes = $this->fonts->getElementsByTagNameNS(self::NS_STYLE, 'font-face');
		$fonts_info = array();
		foreach ($nodes as $node) {
			$fontname = $node->getAttributeNS(self::NS_STYLE, 'name');
			$attributes = $node->attributes;
			for ($i = 0; $i < $attributes->length; $i ++) {
				$name = $attributes->item($i)->name;
				$value = $attributes->item($i)->value;
				$fonts_info[$fontname][$name] = $value;
			}
		}
		return $fonts_info;
	}

	/**
	 * Add new font declaration
	 *
	 * @param string $font_name
	 * @param string $font_family optional
	 */
	public function addFont($font_name, $font_family = '') {
		if (!in_array($font_name, $this->getFonts())) {
			$node = $this->contentDOM->createElementNS(self::NS_STYLE, 'font-face');
			$this->fonts->appendChild($node);
			$node->setAttributeNS(self::NS_STYLE, 'name', $font_name);
			if (!strlen($font_family)) {
				$font_family = $font_name;
			}
			$node->setAttributeNS(self::NS_SVG, 'font-family', $font_family);
		}
	}

	/**
	 * Add array of new fonts declaration
	 *
	 * @param string $font_array
	 */
	public function addFonts($fonts_info) {
		while ($fprops=current($fonts_info)) {
			$fname=key($fonts_info);
			if (!in_array($fname, $this->getFonts())) {
				$node = $this->contentDOM->createElementNS(self::NS_STYLE, 'font-face');
				$this->fonts->appendChild($node);
				while ($props=current($fprops)) {
					$prop=key($fprops);
					if ($prop=='font-family') {
						$node->setAttributeNS(self::NS_SVG, $prop, $props);
					} else {
						$node->setAttributeNS(self::NS_STYLE, $prop, $props);
					}
					next($fprops);
				}
			}
			next($fonts_info);
		}
	}

	/**
	 * Compare two DOMNode nodes
	 *
	 * @param mixed $node1
	 * @param mixed $node2
	 * @return bool
	 */
	public function compareNodes($node1, $node2) {
		if (!($node1 instanceof DOMNode) || !($node2 instanceof DOMNode)) {
			return false;
		}
		$attributes = $node1->attributes;
		if ($attributes->length == $node2->attributes->length) {
			for ($i = 0; $i < $attributes->length; $i ++) {
				$name = $attributes->item($i)->name;
				$value = $attributes->item($i)->value;
				if (!$node2->hasAttribute($name) || $node2->getAttribute($name) != $value) {
					return false;
				}
			}
		} else {
			return false;
		}

		$child = $node1->childNodes;
		if ($child->length == $node2->childNodes->length) {
			for ($i = 0; $i < $child->length; $i ++) {
				$node = $child->item($i);
				$matches = $this->getChildrenByName($node2, $node->nodeName);
				$test = false;
				foreach ($matches as $match) {
					if ($this->compareNodes($node, $match)) {
						$test = true;
						break;
					}
				}
				if (!$test) {
					return false;
				}
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Compare DOMNode children
	 *
	 * @param DOMNode $node1
	 * @param DOMNode $node2
	 * @return bool
	 */
	private function compareChildNodes(DOMNode $node1, DOMNode $node2) {
		$child = $node1->childNodes;
		if ($child->length == $node2->childNodes->length) {
			for ($i = 0; $i < $child->length; $i ++) {
				$node = $child->item($i);
				$matches = $this->getChildrenByName($node2, $node->nodeName);
				$test = false;
				foreach ($matches as $match) {
					if ($this->compareNodes($node, $match)) {
						$test = true;
						break;
					}
				}
				if (!$test) {
					return false;
				}
			}
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Get DOMNode children by name
	 *
	 * @param DOMNode $node
	 * @param string $name
	 * @return array
	 */
	private function getChildrenByName(DOMNode $node, $name) {
		$nodes = array();
		foreach ($node->childNodes as $node) {
			if ($node->nodeName == $name) {
				array_push($nodes, $node);
			}
		}
		return $nodes;
	}

	/**
	 * Test function
	 * @todo remove or finish function
	 */
	public function output() {
		$this->contentXPath->query('/office:document-content/office:font-face-decls/style:font-face');
		return $this->contentDOM->saveXML();
	}

	/**
	 * Save changes in document or save as a new document / under another name
	 *
	 * @param string $filename optional
	 * @throws OpenDocument_Exception
	 */
	public function save($filename = '') {
		if (strlen($filename)) {
			$this->path = $filename;
		}

		// Remove file and create a new document to avoid problems
		if (file_exists($this->path)) {
			unlink($this->path);
		}

		//write mimetype
		if (!ZipWrapper::write($this->path, self::FILE_MIMETYPE, $this->mimetype)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_MIMETYPE_ERR);
		}

		//write content
		$xml = str_replace("'", '&apos;', $this->contentDOM->saveXML());
		if (!ZipWrapper::write($this->path, self::FILE_CONTENT, $xml)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_CONTENT_ERR);
		}

		//write meta
		$xml = str_replace("'", '&apos;', $this->metaDOM->saveXML());
		if (!ZipWrapper::write($this->path, self::FILE_META, $xml)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_META_ERR);
		}

		//write settings
		$xml = str_replace("'", '&apos;', $this->settingsDOM->saveXML());
		if (!ZipWrapper::write($this->path, self::FILE_SETTINGS, $xml)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_SETTINGS_ERR);
		}

		//write styles
		$xml = str_replace("'", '&apos;', $this->stylesDOM->saveXML());
		if (!ZipWrapper::write($this->path, self::FILE_STYLES, $xml)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_STYLES_ERR);
		}

		//write manifest
		$xml = str_replace("'", '&apos;', $this->manifestDOM->saveXML());
		if (!ZipWrapper::write($this->path, self::FILE_MANIFEST, $xml)) {
			throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_MANIFEST_ERR);
		}
	}

	/**
	 * @param record
	 * @param module
	 * @param format
	 * @param mergeTemplateName
	 * @param fullfilename
	 * @param name
	 * @return int documentid
	 */
	public static function saveAsDocument($record, $module, $format, $mergeTemplateName, $fullfilename, $name) {
		global $adb, $current_user;
		$holdRequest = $_REQUEST;
		if (substr($mergeTemplateName, -4)=='.odt' || substr($mergeTemplateName, -4)=='.pdf') {
			$mergeTemplateName = substr($mergeTemplateName, 0, strlen($mergeTemplateName)-4);
		}
		$einfo = getEntityName($module, $record);
		$doc = CRMEntity::getInstance('Documents');
		$doc->column_fields['notes_title'] = getTranslatedString($module, $module).' '.$einfo[$record].' '.$mergeTemplateName;
		$doc->column_fields['notecontent'] = '';
		$doc->column_fields['fileversion'] = 1;
		$doc->column_fields['docyear'] = date('Y');
		$doc->column_fields['template'] = 0;
		$doc->column_fields['filelocationtype'] = 'I';
		$gdfolder = GlobalVariable::getVariable('GenDoc_Save_Document_Folder', '', $module);
		if ($gdfolder!='') {
			$res = $adb->pquery('select folderid from vtiger_attachmentsfolder where foldername=?', array($gdfolder));
			if ($adb->num_rows($res)==0) {
				$res = $adb->pquery('select folderid from vtiger_attachmentsfolder order by foldername', array());
			}
		} else {
			$res = $adb->pquery('select folderid from vtiger_attachmentsfolder order by foldername', array());
		}
		$doc->column_fields['folderid'] = $adb->query_result($res, 0, 'folderid');
		$doc->column_fields['filestatus'] = '1';
		$doc->date_due_flag = 'off';
		$_REQUEST['assigntype'] = 'U';
		$doc->column_fields['assigned_user_id'] = $current_user->id;
		unset($_FILES);
		if (substr($name, -4)=='.odt' || substr($name, -4)=='.pdf') {
			$name = substr($name, 0, strlen($name)-4);
		}
		$name .= '_'.str_replace(' ', '_', $mergeTemplateName);
		$f=array(
			'name'=>$name.($format=='pdf' ? '.pdf' : '.odt'),
			'type'=> ($format=='pdf' ? 'application/pdf' : 'application/vnd.oasis.opendocument.text'),
			'tmp_name'=> $fullfilename,
			'error'=>0,
			'size'=>filesize($fullfilename)
		);
		$_FILES['file0'] = $f;
		$doc->column_fields['filename'] = $f['name'];
		$doc->column_fields['filesize'] = $f['size'];
		$doc->column_fields['filetype'] = $f['type'];
		$_REQUEST['createmode'] = 'link';
		$_REQUEST['return_module'] = $module;
		$_REQUEST['return_id'] = $record;
		$doc->save('Documents');
		unset($_FILES);
		$_REQUEST = $holdRequest;
		return $doc->id;
	}

	/**
	 * @param record
	 * @param mergeTemplateID
	 * @param format
	 * @return string filename
	 */
	public static function doGenDocMerge($record, $templateid, $format = 'odt') {
		global $adb, $root_directory, $current_language, $default_charset;
		$module = getSalesEntityType($record);
		$fullfilename = $root_directory .  OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
		$fullpdfname = $root_directory . OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
		$filename = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.odt';
		$pdfname = OpenDocument::GENDOCCACHE . '/' . $module . '/odtout' . $record . '.pdf';
		if (!is_dir(OpenDocument::GENDOCCACHE . '/' . $module)) {
			mkdir(OpenDocument::GENDOCCACHE . '/' . $module, 0777, true);
		}
		$odtout = new OpenDocument();
		OpenDocument::$compile_language = GlobalVariable::getVariable('GenDoc_Default_Compile_Language', substr($current_language, 0, 2), $module);
		if (file_exists('modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php')) {
			include 'modules/evvtgendoc/commands_'. OpenDocument::$compile_language . '.php';
		} else {
			include 'modules/evvtgendoc/commands_en.php';
		}
		$orgfile = $adb->pquery(
			"Select CONCAT(a.path,'',a.attachmentsid,'_',a.name) as filepath, a.name
				from vtiger_notes n
				join vtiger_seattachmentsrel sa on sa.crmid=n.notesid
				join vtiger_attachments a on a.attachmentsid=sa.attachmentsid
				where n.notesid=?",
			array($templateid)
		);
		$mergeTemplatePath=html_entity_decode($adb->query_result($orgfile, 0, 'filepath'), ENT_QUOTES, $default_charset);
		if (file_exists($fullfilename)) {
			unlink($fullfilename);
		}
		if (file_exists($fullpdfname)) {
			unlink($fullpdfname);
		}
		$odtout->GenDoc($mergeTemplatePath, $record, $module);
		$odtout->save($filename);
		ZipWrapper::copyPictures($mergeTemplatePath, $filename, $odtout->changedImages, $odtout->newImages);
		$odtout->postprocessing($fullfilename);
		if ($format=='pdf') {
			$odtout->convert($filename, $pdfname);
		}
		return ($format=='pdf' ? $fullpdfname : $fullfilename);
	}

	/**
	 * Add a full path to the file entry list in this manifest document.
	 * Creates a DOM element with the tag 'manifest:file-entry' and
	 * adds 'manifest:full-path', and 'manifest:media-type' attributes to it.
	 *
	 * @return		DOMElement A DOM element which contains a single file entry.
	 * @param 		string fullpath The full path of the file that has been added.
	 * @param 		string mimetype The mime type of this file
	 * @access		private
	 */
	private function makeFileEntryElement($fullpath, $mtype) {
		/*
		 * Create a new file-entry element ...
		 */
		$node = $this->manifestDOM->createElement('manifest:file-entry', null);
		/*
		 * ... add the fullpath of the file to full-path attribute, ...
		 */
		$node->setAttributeNS(self :: MANIFEST, 'manifest:full-path', 'Pictures/'.basename($fullpath));
		/*
		 * ... add mime type of the file into media-type attribute.
		 */
		$node->setAttributeNS(self :: MANIFEST, 'manifest:media-type', $mtype);
		$this->manifestDOM->firstChild->appendChild($node);
		return $node;
	}

	public function processHTML($compiledtext, $child) {
		global $parentArray;
		$topofarray=$parentArray[count($parentArray)-1];
		if (stripos($compiledtext, '<b>')!==false
			|| stripos($compiledtext, '<i>')!==false
			|| stripos($compiledtext, '<u>')!==false
			|| stripos($compiledtext, '<style')!==false
			|| stripos($compiledtext, '<strong')!==false
			|| stripos($compiledtext, '<em')!==false
			|| stripos($compiledtext, '<gendocstyle')!==false
			|| stripos($compiledtext, '<br')!==false
			|| stripos($compiledtext, "\n")!==false
			|| stripos($compiledtext, '<div')!==false
		) { // hay html, traducir a odt
			$dochtml = new DOMDocument();
			$compiledtext = mb_convert_encoding($compiledtext, 'HTML-ENTITIES', 'UTF-8');
			$compiledtext = str_replace('<div', '<span', $compiledtext);
			$compiledtext = str_replace('</div>', '</span>', $compiledtext);
			$compiledtext = str_replace("\n", '<br>', $compiledtext);
			$compiledtext = str_replace('<br/>', '<br>', $compiledtext);
			$compiledtext = str_replace('<br />', '<br>', $compiledtext);
			$compiledtext = str_replace('<ul>', '<br>', $compiledtext);
			$compiledtext = str_replace('</ul>', '', $compiledtext);
			$compiledtext = str_replace('<ol>', '<br>', $compiledtext);
			$compiledtext = str_replace('</ol>', '', $compiledtext);
			$compiledtext = str_replace('<li>', '    - ', $compiledtext);
			$compiledtext = str_replace('</li>', '<br>', $compiledtext);
			$compiledtext = strip_tags($compiledtext, '<span><b><strong><i><em><u><style><gendocstyle><br>');
			@$dochtml->loadHTML('<p>'.$compiledtext.'</p>');
			$nlhtml=$dochtml->getElementsByTagName('p');
			foreach ($nlhtml as $phtml) {
				foreach ($phtml->childNodes as $childhtml) {
					$topofarray=$parentArray[count($parentArray)-1];
					switch ($childhtml->nodeName) {
						case 'b':
						case 'strong':
							$elem=$topofarray->createSpan(html_entity_decode($childhtml->nodeValue, ENT_NOQUOTES, 'UTF-8'));
							$elem->getNode()->setAttribute('text:style-name', 'SIGPAC_BOLD');
							break;
						case 'i':
						case 'em':
							$elem=$topofarray->createSpan(html_entity_decode($childhtml->nodeValue, ENT_NOQUOTES, 'UTF-8'));
							$elem->getNode()->setAttribute('text:style-name', 'SIGPAC_ITALIC');
							break;
						case 'u':
							$elem=$topofarray->createSpan(html_entity_decode($childhtml->nodeValue, ENT_NOQUOTES, 'UTF-8'));
							$elem->getNode()->setAttribute('text:style-name', 'SIGPAC_UNDERLINE');
							break;
						case 'span':
							array_pop($parentArray);
							$topofarray=$parentArray[count($parentArray)-1];
							if ($childhtml->haschildNodes()) {
								$elem=$topofarray->createParagraph();
								$spanstyle = $childhtml->getAttribute('style');
								if ($spanstyle) {
									$spanstyle = str_replace(' ', '', $spanstyle);
									$sselem = explode(':', $spanstyle);
									if ($sselem[0]=='text-align') {
										$justify = strtoupper(trim($sselem[1], ';'));
										$elem->getNode()->setAttribute('text:style-name', "SIGPAC_JUSTIFY_$justify");
									}
								}
								$spanstyle = $childhtml->getAttribute('estilo');
								if ($spanstyle) {
									$elem->getNode()->setAttribute('text:style-name', $spanstyle);
								}
									array_push($parentArray, $elem);
								foreach ($childhtml->childNodes as $subchildhtml) {
									//$this->processHTML($dochtml->saveHTML($subchildhtml), $child); PHP VERSION >= 5.3.6
									$this->processHTML(html_entity_decode($subchildhtml->C14N(), ENT_NOQUOTES, 'UTF-8'), $child);
								}
							} else {
								$elem=$topofarray->createParagraph(html_entity_decode($childhtml->nodeValue, ENT_NOQUOTES, 'UTF-8'));
								$spanstyle = $childhtml->getAttribute('style');
								if ($spanstyle) {
									$spanstyle = str_replace(' ', '', $spanstyle);
									$sselem = explode(':', $spanstyle);
									if ($sselem[0]=='text-align') {
										$justify = strtoupper(trim($sselem[1], ';'));
										$elem->getNode()->setAttribute('text:style-name', "SIGPAC_JUSTIFY_$justify");
									}
								}
								$spanstyle = $childhtml->getAttribute('estilo');
								if ($spanstyle) {
									$elem->getNode()->setAttribute('text:style-name', $spanstyle);
								}
								array_push($parentArray, $elem);
							}
								array_pop($parentArray);
								$topofarray=$parentArray[count($parentArray)-1];
								$elem=$topofarray->createParagraph();
								array_push($parentArray, $elem);
							break;
						case 'style':
						case 'gendocstyle':
							if ($childhtml->haschildNodes()) {
								foreach ($childhtml->childNodes as $subchildhtml) {
									//$this->processHTML($dochtml->saveHTML($subchildhtml), $child); PHP VERSION >= 5.3.6
									$this->processHTML(html_entity_decode($subchildhtml->C14N(), ENT_NOQUOTES, 'UTF-8'), $child);
								}
							} else {
								$elem=$topofarray->createSpan(html_entity_decode($childhtml->nodeValue, ENT_NOQUOTES, 'UTF-8'));
								$elem->getNode()->setAttribute('text:style-name', $childhtml->getAttribute('estilo'));
							}
							break;
						case 'br':
							$elem=$topofarray->createTextTab();  // para que la justificación completa funcione correctamente
							$elem=$topofarray->createTextLineBreak();
							break;
						default: //case '#text':
							$elem=$topofarray->createTextElement(html_entity_decode($childhtml->nodeValue, ENT_NOQUOTES, 'UTF-8'));
							OpenDocument::copyAttributes($child, $elem);
							break;
					}
				}
			}
		} else {
			$topofarray=$parentArray[count($parentArray)-1];
			$elem=$topofarray->createTextElement(html_entity_decode($compiledtext, ENT_NOQUOTES, 'UTF-8'));
			OpenDocument::copyAttributes($child, $elem);
		}
	}

	public function compiletoDoc($child, $id, $module) {
		global $changedImage,$newImageAdded,$parentArray,$rec;
		$rec = 0;
		$topofarray=$parentArray[count($parentArray)-1];
		$popcopy=false;
		switch (get_class($child)) {
			case 'OpenDocument_TextElement':
				$compiledtext=compile($child->text, $id, $module);
				$this->processHTML($compiledtext, $child);
				break;
			case 'OpenDocument_TextTab':
				$elem=$topofarray->createTextTab();
				break;
			case 'OpenDocument_TextSpace':
				$elem=$topofarray->createTextSpace($child->numspaces);
				break;
			case 'OpenDocument_Footnote':
				array_push($parentArray, $topofarray->createFootnote($child->text, $child->id, $child->noteclass));
				$popcopy=true;
				break;
			case 'OpenDocument_DrawEGeometry':
				array_push($parentArray, $topofarray->createDrawEGeometry());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawCustomShape':
				array_push($parentArray, $topofarray->createDrawCustomShape());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawGraph':
				array_push($parentArray, $topofarray->createDrawGraph());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawConnector':
				array_push($parentArray, $topofarray->createDrawConnector());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawLine':
				array_push($parentArray, $topofarray->createDrawLine());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawRect':
				array_push($parentArray, $topofarray->createDrawRect());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawObject':
				array_push($parentArray, $topofarray->createDrawObject());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawEquation':
				array_push($parentArray, $topofarray->createDrawEquation());
				$popcopy=true;
				break;
			case 'OpenDocument_DrawHandle':
				array_push($parentArray, $topofarray->createDrawHandle());
				$popcopy=true;
				break;
			case 'OpenDocument_BookmarkStart':
				array_push($parentArray, $topofarray->createBookmarkStart());
				$popcopy=true;
				break;
			case 'OpenDocument_BookmarkEnd':
				array_push($parentArray, $topofarray->createBookmarkEnd());
				$popcopy=true;
				break;
			case 'OpenDocument_NoteBody':
				array_push($parentArray, $topofarray->createNoteBody());
				$popcopy=true;
				break;
			case 'OpenDocument_ReferenceMark':
				array_push($parentArray, $topofarray->createReferenceMark($child->text));
				$popcopy=true;
				break;
			case 'OpenDocument_ReferenceRef':
				array_push($parentArray, $topofarray->createReferenceRef($child->text));
				$popcopy=true;
				break;
			case 'OpenDocument_Section':
				array_push($parentArray, $topofarray->createSection($child->text));
				$popcopy=true;
				break;
			case 'OpenDocument_NoteCitation':
				array_push($parentArray, $topofarray->createNoteCitation($child->text));
				$popcopy=true;
				break;
			case 'OpenDocument_TextDate':
				$tdt = $topofarray->createTextDate($child->text, $child->datevalue, $child->fixed, $child->getStyleName());
				OpenDocument::copyAttributes($child, $tdt);
				array_push($parentArray, $tdt);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_TextTime':
				$tdt = $topofarray->createTextTime($child->text, $child->timevalue, $child->fixed, $child->getStyleName());
				OpenDocument::copyAttributes($child, $tdt);
				array_push($parentArray, $tdt);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_PageNumber':
				array_push($parentArray, $topofarray->createPageNumber($child->text, $child->select));
				$popcopy=true;
				break;
			case 'OpenDocument_PageCount':
				array_push($parentArray, $topofarray->createPageCount($child->text));
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_InfoSubject':
				array_push($parentArray, $topofarray->createInfoSubject($child->text));
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_InfoTitle':
				array_push($parentArray, $topofarray->createInfoTitle($child->text));
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_InfoAuthor':
				array_push($parentArray, $topofarray->createInfoAuthor($child->text));
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_TextLineBreak':
				$elem=$topofarray->createTextLineBreak();
				break;
			case 'OpenDocument_TextSoftPageBreak': // Me lo cargo y punto
				break;
			case 'OpenDocument_Frame':
				$frm=$topofarray->createFrame($child->text, $child->anchortype, $child->width, $child->height, $child->zindex, $child->framename, $child->x, $child->y, $child->anchorpagenumber);
				OpenDocument::copyAttributes($child, $frm);
				array_push($parentArray, $frm);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_FrameTextBox':
				$ftb = $topofarray->createFrameTextBox($child->text, $child->minheight);
				OpenDocument::copyAttributes($child, $ftb);
				array_push($parentArray, $ftb);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_FrameImage':
				$elem = $topofarray->createFrameImage($child->text, $child->href, $child->type, $child->show, $child->actuate);
				if (!empty($changedImage)) { // hay que cambiar esta imagen
					$ImageNodeName = $this->getImageNodeName($child);
					if (!empty($ImageNodeName)) {
						$this->changedImages[$ImageNodeName] = $changedImage;
					}
					OpenDocument::copyAttributes($child, $elem);
					$changedImage = '';
				} elseif ($newImageAdded) { // hay que añadir esta imagen
					$nifname = $this->newImages[count($this->newImages)-1];
					/* get mime-type for a specific file */
					$finfo = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
					$mtype = finfo_file($finfo, $nifname);
					if ($nifname != 'not_show_image') {
						$this->makeFileEntryElement($nifname, $mtype);
						OpenDocument::copyAttributes($child, $elem, basename($nifname));
					}
					$newImageAdded=false;
				} else {
					OpenDocument::copyAttributes($child, $elem);
				}
				break;
			case 'OpenDocument_List':
				$lst = $topofarray->createList($child->continuenum);
				OpenDocument::copyAttributes($child, $lst);
				array_push($parentArray, $lst);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_ListItem':
				$lst = $topofarray->createListItem($child->text);
				OpenDocument::copyAttributes($child, $lst);
				array_push($parentArray, $lst);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_Table':
				$tbl = $topofarray->createTable($child->issubtable);
				OpenDocument::copyAttributes($child, $tbl);
				array_push($parentArray, $tbl);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_TableColumn':
				$elem=$topofarray->createTableColumn($child->numcolsrepeated);
				OpenDocument::copyAttributes($child, $elem);
				break;
			case 'OpenDocument_TableRow':
				$tr = $topofarray->createTableRow();
				OpenDocument::copyAttributes($child, $tr);
				array_push($parentArray, $tr);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_TableHeaderRow':
				$thr = $topofarray->createTableHeaderRow();
				OpenDocument::copyAttributes($child, $thr);
				array_push($parentArray, $thr);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_TableCell':
				$tcell = $topofarray->createTableCell($child->text, $child->numbercolumnsspanned, $child->numberrowsspanned);
				OpenDocument::copyAttributes($child, $tcell);
				array_push($parentArray, $tcell);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_TableCoveredCell':
				array_push($parentArray, $topofarray->createTableCoveredCell());
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_Heading':
				$hdg = $topofarray->createHeading(compile($child->text, $id, $module), $child->level);
				OpenDocument::copyAttributes($child, $hdg);
				array_push($parentArray, $hdg);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			case 'OpenDocument_Hyperlink':
				$alink=$topofarray->createHyperlink(compile($child->text, $id, $module), compile($child->location, $id, $module), compile($child->target, $id, $module));
				OpenDocument::copyAttributes($child, $alink);
				array_push($parentArray, $alink);
				$this->toGenDoc($child, $id, $module);
				$elem=array_pop($parentArray);
				break;
			default:
				$domNode = $this->contentDOM->importNode($child, true);
				if (!is_null($domNode) && get_class($domNode)=='DOMElement') {
					$this->cursor->appendChild($domNode);
				}
				break;
		} // Switch
		if ($popcopy) {
			$this->toGenDoc($child, $id, $module);
			$elem=array_pop($parentArray);
			OpenDocument::copyAttributes($child, $elem);
		}
	}

	public static function PDFConversionActive() {
		$GenDocPDF = (coreBOS_Settings::getSetting('cbgendoc_server', '')!='' || GlobalVariable::getVariable('GenDoc_Convert_URL', '', 'evvtgendoc')!='');
		if (!$GenDocPDF) {
			$rdo = shell_exec('which unoconv > /dev/null; echo $?');
			$GenDocPDF = ($rdo==0);
		}
		return $GenDocPDF;
	}

	public function convert($frompath, $topath, $format = 'pdf') {
		$gendoc_active = coreBOS_Settings::getSetting('cbgendoc_active', 0);
		if ($gendoc_active == 1) {
			include_once 'include/wsClient/WSClient.php';
			$srv = coreBOS_Settings::getSetting('cbgendoc_server', '');
			$usr = coreBOS_Settings::getSetting('cbgendoc_user', '');
			$key = coreBOS_Settings::getSetting('cbgendoc_accesskey', '');
			$url = $srv.'/webservice.php';
			$wsClient = new Vtiger_WSClient($url);
			// Login
			if (!$wsClient->doLogin($usr, $key)) {
				die('Login error.');
			}
			$finfo = finfo_open(FILEINFO_MIME);
			$nameparts = explode('/', $frompath);
			$fname = $nameparts[count($nameparts)-1];
			$filename = $frompath;
			$mtype = finfo_file($finfo, $filename);  // posiblemente haya que instalar librerias PECL adicionales a PHP
			$model_filename=array(
				'name'=>$fname,
				'size'=>filesize($filename),
				'type'=>$mtype,
				'content'=>base64_encode(file_get_contents($filename))
			);

			$data = array('file' => json_encode($model_filename), 'convert_format' => $format);

			$response = $wsClient->doInvoke('gendoc_convert', $data);
			if ($response['result'] == 'success') {
				file_put_contents($topath, base64_decode($response['file']['content']));
			}
		} elseif (GlobalVariable::getVariable('GenDoc_Convert_URL', '', 'evvtgendoc')!='') {
			$client = new Vtiger_Net_Client(GlobalVariable::getVariable('GenDoc_Convert_URL', '', 'evvtgendoc').'/unoconv/'.$format);
			$client->setFileUpload('file', $frompath, 'file');
			$retries = GlobalVariable::getVariable('GenDoc_PDFConversion_Retries', 1, 'evvtgendoc');
			for ($x = 1; $x <= $retries; $x++) {
				$post = $client->doPost(array());
				$rsp = json_decode($post, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					break;
				}
			}
			file_put_contents($topath, $post);
		} else {
			$cmd = 'unoconv -v -f '.escapeshellarg($format) . ' ' . escapeshellarg($frompath) . ' 2>&1';
			exec($cmd, $out);
			$ret = print_r($out, true);
			foreach ($out as $line) {
				$find = false;
				$file_conv = '';
				if (strstr($line, 'Output file: ')) {
					$find = true;
					$file_conv = str_replace('Output file: ', '', $line);
					break;
				}
			}
			if ($find) {
				$cmd = "mv $file_conv " . escapeshellarg($topath);
				exec($cmd, $out);
			}
			$ret .= "\n\n".print_r($out, true);
			return $ret;
		}
	}

	/**
	 * Generate XML combined document
	 *
	 * @param XML file
	 * @access public
	 */
	public function GenXML($originFile, $id, $module, $root_module = null) {
		global $iter_modules;
		if (!is_null($root_module)) {
			$iter_modules[$root_module[0]] = array($root_module[1]);
		}
		if (!is_dir('cache/genxml')) {
			mkdir('cache/genxml');
		}
		$cacheFile = 'cache/genxml/xmlgen'.$id.'.xml';
		if (file_exists($cacheFile)) {
			unlink($cacheFile);
		}
		$obj = new DOMDocument();
		$obj->load($originFile);
		$this->xmlout = new XMLWriter();
		$this->xmlout->openURI($cacheFile);
		$this->xmlout->setIndent(true);
		$this->xmlout->startDocument('1.0', 'UTF-8');
		$xmlbrs = $obj->getElementsByTagName('*');
		if ($xmlbrs->length>0) {
			$this->processBranch($obj, $id, $module);
		}
		return $this->xmlout->flush();
	}

	public function processBranch($doc, $crmid, $module) {
		global $iter_modules,$repe;
		foreach ($doc->childNodes as $node) {
			if (empty($node->tagName)) {
				continue;
			}
			$entidad=trim($node->getAttribute('entity'));
			$condicion=trim($node->getAttribute('condition'));
			$condition_pair = explode('=', $condicion);
			if (count($condition_pair) != 2) {
				$condition_pair = explode(' en ', $condicion);
				if (count($condition_pair) != 2) {
					$condition_pair = explode(' !en ', $condicion);
				}
			}
			if (count($condition_pair) == 2 && strpos($condition_pair[0], '.')===false) {
				$condicion=$entidad.'.'.$condicion;  // si el campo de la condición no tiene la entidad, la añadimos
			}
			if (empty($condicion)) {
				$condicion = $entidad;
			}
			switch ($node->tagName) {
				case 'genxmlforeach':
					eval_paracada($condicion, $crmid, $module);
					$num_iter =iterations();
					$repe[] = 0;
					$last_repe = count($repe)-1;
					for ($repe[$last_repe]=1; $repe[$last_repe]<=$num_iter; $repe[$last_repe]++) {
						$this->processBranch($node, $iter_modules[$entidad][0], $entidad);
						$this->accumAggregate($iter_modules[$entidad][0], $entidad);
						pop_iter_modules();
					}
					array_pop($repe);
					continue 2;
					break;
				case 'genxmlifexist':
					$cumple_cond = eval_existe($condicion, $crmid, $module);
					if ($cumple_cond && $this->hasChild($node)) {
						eval_paracada($condicion, $crmid, $module);
						$this->processBranch($node, $iter_modules[$entidad][0], $entidad);
					}
					continue 2;
					break;
				case 'genxmlifnotexist':
					$cumple_cond = eval_existe($condicion, $crmid, $module);
					if (!$cumple_cond && $this->hasChild($node)) {
						eval_paracada($condicion, $crmid, $module);
						$this->processBranch($node, $iter_modules[$entidad][0], $entidad);
					}
					continue 2;
					break;
				case 'genxmlsum':
					$this->startAggregate('sum', $node->getAttribute('entity'), $node->getAttribute('field'));
					continue 2;
					break;
				case 'genxmlcount':
					$this->startAggregate('cnt', $node->getAttribute('entity'));
					continue 2;
					break;
			}
			$this->xmlout->startElement($node->tagName);
			foreach ($node->attributes as $attrName => $attrNode) {
				$this->xmlout->writeAttribute($attrName, compile($attrNode->value, $crmid, $module));
			}
			if ($this->hasChild($node)) {
				$this->processBranch($node, $crmid, $module);
			} else {
				$this->xmlout->text(compile($node->nodeValue, $crmid, $module));
			}
			$this->xmlout->endElement(); //end field
		}
	}

	public function hasChild($p) {
		if ($p->hasChildNodes()) {
			foreach ($p->childNodes as $c) {
				if ($c->nodeType == XML_ELEMENT_NODE) {
					return true;
				}
			}
		}
		return false;
	}

	public function startAggregate($action, $entity, $field = '') {
		global $genxmlaggregates;
		if (empty($field)) {
			$field="genxml$action";
		}
		$genxmlaggregates[$action][$entity][$field] = 0;
	}

	public function accumAggregate($crmid, $module) {
		global $genxmlaggregates;
		foreach ($genxmlaggregates as $action => $modarr) {
			foreach ($modarr as $entity => $field) {
				if ($entity!=$module) {
					continue;
				}
				foreach ($field as $fld => $val) {
					switch ($action) {
						case 'cnt':
							$genxmlaggregates[$action][$entity][$fld]++;
							break;
						case 'sum':
							$val = retrieve_from_db("$entity.$fld", $crmid, $module, false);
							$genxmlaggregates[$action][$entity][$fld] = $genxmlaggregates[$action][$entity][$fld] + $val;
							break;
					}
				}
			};
		}
	}
}
?>
