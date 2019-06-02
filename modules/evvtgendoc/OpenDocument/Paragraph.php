<?php
/**
* OpenDocument_Paragraph class
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
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*
* Copyright 2009 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
* Author: Joe Bordes
*/

require_once 'StyledElement.php';

/**
* OpenDocument_Paragraph element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Paragraph extends OpenDocument_StyledElement {

	/**
	 * Node namespace
	 */
	const nodeNS = OpenDocument::NS_TEXT;

	/**
	 * Node namespace
	 */
	const nodePrefix = 'text';

	/**
	 * Node name
	 */
	const nodeName = 'p';

	/**
	 * Element style name prefix
	 *
	 */
	const styleNamePrefix = 'P';

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document) {
		parent::__construct($node, $document);
		$this->allowedElements = array(
			'OpenDocument_Span',
			'OpenDocument_Hyperlink',
			'OpenDocument_Frame',
			'OpenDocument_TextTab',
			'OpenDocument_TextSpace',
			'OpenDocument_TextLineBreak',
			'OpenDocument_TextSoftPageBreak',
			'OpenDocument_TextDate',
			'OpenDocument_TextTime',
			'OpenDocument_PageNumber',
			'OpenDocument_PageCount',
			'OpenDocument_InfoSubject',
			'OpenDocument_InfoTitle',
			'OpenDocument_InfoAuthor',
			'OpenDocument_BookmarkStart',
			'OpenDocument_BookmarkEnd',
			'OpenDocument_DrawGraph',
			'OpenDocument_Footnote',
			'OpenDocument_ReferenceMark',
			'OpenDocument_ReferenceRef',
			'OpenDocument_DrawLine',
			'OpenDocument_DrawCustomShape',
		);
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_Paragraph
	 * @throws OpenDocument_Exception
	 */
	public static function instance($object, $content) {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_Paragraph($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document);
		$node->appendChild($element->node);

		if (is_scalar($content)) {
			$element->createTextElement($content);
		}
		return $element;
	}

	/**
	 * Generate element new style name
	 *
	 * @return string
	 */
	public function generateStyleName() {
		self::$styleNameMaxNumber++;
		return self::styleNamePrefix . self::$styleNameMaxNumber;
	}

	/************** Elements ****************/

	/**
	 * Create OpenDocument_TextElement
	 *
	 * @param string $text
	 * @return OpenDocument_TextElement
	 */
	public function createTextElement($text) {
		return OpenDocument_TextElement::instance($this, $text);
	}

	public function createFrame($text, $anchortype, $width, $height, $zindex, $framename, $x, $y, $anchorpagenumber) {
		return OpenDocument_Frame::instance($this, $text, $anchortype, $width, $height, $zindex, $framename, $x, $y, $anchorpagenumber);
	}

	/**
	 * Create OpenDocument_Hyperlink
	 *
	 * @param string $text
	 * @param string $location
	 * @param string $type optional
	 * @param string $target optional
	 * @param string $name optional
	 * @return OpenDocument_Hyperlink
	 */
	public function createHyperlink($text, $location, $type = 'simple', $target = '', $name = '') {
		return OpenDocument_Hyperlink::instance($this, $text, $location, $type, $target, $name);
	}

	/**
	 * Create OpenDocument_Span element
	 *
	 * @param string $text
	 * @return OpenDocument_Span
	 */
	public function createSpan($text) {
		return OpenDocument_Span::instance($this, $text);
	}
	public function createTextSpace($numspc) {
		return OpenDocument_TextSpace::instance($this, $numspc);
	}
	public function createTextTab() {
		return OpenDocument_TextTab::instance($this);
	}
	public function createTextLineBreak() {
		return OpenDocument_TextLineBreak::instance($this);
	}
	public function createTextSoftPageBreak() {
		return OpenDocument_TextSoftPageBreak::instance($this);
	}
	public function createTextDate($content, $dval, $fval, $sname) {
		return OpenDocument_TextDate::instance($this, $content, $dval, $fval, $sname);
	}
	public function createTextTime($content, $tval, $fval, $sname) {
		return OpenDocument_TextTime::instance($this, $content, $tval, $fval, $sname);
	}
	public function createPageNumber($content, $sel) {
		return OpenDocument_PageNumber::instance($this, $content, $sel);
	}
	public function createPageCount($content) {
		return OpenDocument_PageCount::instance($this, $content);
	}
	public function createInfoSubject($content) {
		return OpenDocument_InfoSubject::instance($this, $content);
	}
	public function createInfoTitle($content) {
		return OpenDocument_InfoTitle::instance($this, $content);
	}
	public function createInfoAuthor($content) {
		return OpenDocument_InfoAuthor::instance($this, $content);
	}
	public function createBookmarkStart($content = '') {
		return OpenDocument_BookmarkStart::instance($this, $content);
	}
	public function createBookmarkEnd($content = '') {
		return OpenDocument_BookmarkEnd::instance($this, $content);
	}
	public function createFootnote($content, $id, $ncval = 'footnote') {
		return OpenDocument_Footnote::instance($this, $content, $id, $ncval);
	}
	public function createReferenceMark($text) {
		return OpenDocument_ReferenceMark::instance($this, $text);
	}
	public function createReferenceRef($text) {
		return OpenDocument_ReferenceRef::instance($this, $text);
	}
	public function createDrawGraph() {
		return OpenDocument_DrawGraph::instance($this);
	}
	public function createDrawLine() {
		return OpenDocument_DrawLine::instance($this);
	}
	public function createDrawCustomShape() {
		return OpenDocument_DrawCustomShape::instance($this);
	}
}
?>
