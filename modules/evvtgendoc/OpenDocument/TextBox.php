<?php
/**
* OpenDocument_FrameTextBox class
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
* OpenDocument_FrameTextBox element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_FrameTextBox extends OpenDocument_StyledElement {

	/**
	 * Collection of children objects
	 *
	 * @var ArrayIterator
	 * @access read-only
	 */
	public $children;

	// Atributos
	public $minheight;

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
	const nodeName = 'text-box';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'frt';

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document, $text = '', $aminheight = '') {
		parent::__construct($node, $document);
		$this->allowedElements = array(
			'OpenDocument_Paragraph',
			'OpenDocument_Span',
			'OpenDocument_Hyperlink',
		);
		return true;
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_FrameTextBox
	 * @throws OpenDocument_Exception
	 */
	public static function instance($object, $text, $minheight) {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_FrameTextBox($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $text, $minheight);
		$node->appendChild($element->node);

		return $element;
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
		$style_name = $this->document->applyGraphStyle($style_name, $name, $value, $this, $elemtype);
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
	 * Create OpenDocument_Paragraph
	 *
	 * @param string $text optional
	 * @return OpenDocument_Paragraph
	 * @access public
	 */
	public function createParagraph($text = '') {
		return OpenDocument_Paragraph::instance($this, $text);
	}
	public function createList() {
		return OpenDocument_List::instance($this);
	}
}
?>
