<?php
/**
* OpenDocument_Frame class
*
*  This file is based on the work done by Alexander Pak <irokez@gmail.com>
*  in his OpenDocument Library distributed on PEAR.
*  A special thanks to him for setting the road we followed.
*  This library is governed by the GNU Lesser Public License
*
* The whole library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*
* @category   File Formats
* @package    OpenDocument
* @author     Joe Bordes, JPL TSolucio, S.L. <joe@tsolucio.com>
* Copyright 2009 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
*/

require_once 'Element.php';

/**
* OpenDocument_Frame element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Frame extends OpenDocument_StyledElement {

	/**
	 * Collection of children objects
	 *
	 * @var ArrayIterator
	 * @access read-only
	 */
	public $children;

		// Attribs
	public $anchortype;
	public $width;
	public $height;
	public $zindex;
	public $framename;
	public $x;
	public $y;
	public $anchorpagenumber;

	/**
	 * Node namespace
	 */
	const nodeNS = OpenDocument::NS_DRAW;

	/**
	 * Node amespace
	 */
	const nodePrefix = 'draw';

		/**
	 * Node name
	 */
	const nodeName = 'frame';

		/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'fr';

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document, $text = '', $aanchortype = '', $awidth = '', $aheight = '', $azindex = '', $aframename = '', $ax = '', $ay = '', $aanchorpagenumber = '') {
		parent::__construct($node, $document);
		$this->allowedElements = array(
		   'OpenDocument_FrameTextBox',
		   'OpenDocument_FrameImage',
		   'OpenDocument_DrawObject',
		);
		return true;
		$anchortype = $node->getAttributeNS(OpenDocument::NS_TEXT, 'anchor-type');
		if (empty($anchortype)) {
			$anchortype=$aanchortype;
		}
		if (!empty($anchortype)) {
			$this->node->setAttribute('text:anchor-type', $anchortype);
			$this->anchortype = $anchortype;
		}
		$width = $node->getAttributeNS(OpenDocument::NS_SVG, 'width');
		if (empty($width)) {
			$width=$awidth;
		}
		if (!empty($width)) {
			$this->node->setAttribute('svg:width', $width);
			$this->width = $width;
		}
		$height = $node->getAttributeNS(OpenDocument::NS_SVG, 'height');
		if (empty($height)) {
			$height=$aheight;
		}
		if (!empty($height)) {
			$this->node->setAttribute('svg:height', $height);
			$this->height = $height;
		}
		$zindex = $node->getAttribute('draw:z-index');
		if ($zindex=='') {
			$zindex=$azindex;
		}
		if ($zindex!='') {
			$this->node->setAttribute('draw:z-index', $zindex);
			$this->zindex = $zindex;
		}
		$framename = $node->getAttribute('draw:name');
		if (empty($framename)) {
			$framename=$aframename;
		}
		if (!empty($framename)) {
			$this->node->setAttribute('draw:name', $framename);
			$this->framename = $framename;
		}
		$x = $node->getAttribute('svg:x');
		if ($x=='') {
			$x=$ax;
		}
		if ($x!='') {
			$this->node->setAttribute('svg:x', $x);
			$this->x = $x;
		}
		$y = $node->getAttribute('svg:y');
		if ($y=='') {
			$y=$ay;
		}
		if ($y!='') {
			$this->node->setAttribute('svg:y', $y);
			$this->y = $y;
		}
		$anchorpagenumber = $node->getAttribute('text:anchorpagenumber');
		if ($anchorpagenumber=='') {
			$anchorpagenumber=$aanchorpagenumber;
		}
		if ($anchorpagenumber!='') {
			$this->node->setAttribute('text:anchorpagenumber', $anchorpagenumber);
			$this->anchorpagenumber = $anchorpagenumber;
		}

				$this->allowedElements = array(
		   'OpenDocument_FrameTextBox',
		   'OpenDocument_FrameImage'
		);
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_Frame
	 * @throws OpenDocument_Exception
	 */
	public static function instance($object, $text, $anchortype, $width, $height, $zindex, $framename, $x, $y, $anchorpagenumber) {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

				$element = new OpenDocument_Frame($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $text, $anchortype, $width, $height, $zindex, $framename, $x, $y, $anchorpagenumber);
		$node->appendChild($element->node);

		return $element;
	}

	/**
	 * Get style information
	 *
	 * @param array $properties
	 * @return array
	 */
	public function getStyle($properties) {
		return $this->document->getStyle($this->getStyleName(), $properties);
	}

		/**
	 * Get style name
	 *
	 * @return string
	 */
	public function getStyleName() {
		return $this->node->getAttributeNS(OpenDocument::NS_TEXT, 'style-name');
	}

		/**
	 * Get style name prefix
	 *
	 * @return string
	 */
	public function getStyleNamePrefix() {
		return $this->styleNamePrefix;
	}

		/**
	 * Generate new style name
	 *
	 * @return string $stylename
	 */
	public function generateStyleName() {
		self::$styleNameMaxNumber ++;
		return self::styleNamePrefix . self::$styleNameMaxNumber;
	}

	/**
	 * Apply style information
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function applyStyle($name, $value, $elemtype) {
		$style_name = $this->node->getAttributeNS(OpenDocument::NS_DRAW, 'style-name');
		$style_name = $this->document->applyStyle($style_name, $name, $value, $this, $elemtype);
		$this->node->setAttributeNS(OpenDocument::NS_DRAW, 'style-name', $style_name);
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

	/**
	 * Create OpenDocument_FrameTextBox
	 *
	 * @param string $text optional
	 * @return OpenDocument_FrameTextBox
	 * @access public
	 */
	public function createFrameTextBox($text, $minheight) {
		return OpenDocument_FrameTextBox::instance($this, $text, $minheight);
	}

		/**
	 * Create OpenDocument_FrameImage
	 *
	 * @param string $text optional
	 * @return OpenDocument_FrameImage
	 * @access public
	 */
	public function createFrameImage($text, $href, $type, $show, $actuate) {
		return OpenDocument_FrameImage::instance($this, $text, $href, $type, $show, $actuate);
	}
	public function createDrawObject() {
		return OpenDocument_DrawObject::instance($this);
	}
}
?>
