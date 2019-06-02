<?php
/**
* OpenDocument_ReferenceRef class
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
* Copyright 2017 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
*/

require_once 'Element.php';

/**
* OpenDocument_ReferenceRef element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_ReferenceRef extends OpenDocument_StyledElement {
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
	const nodeName = 'reference-ref';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'N';

	public $refname;
	public $refformat;

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document, $content) {
		parent::__construct($node, $document);
		$refname = $node->getAttributeNS(OpenDocument::NS_TEXT, 'ref-name');
		if (!empty($refname)) {
			$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'ref-name', $refname);
			$this->refname = $refname;
		}
		$reffmt = $node->getAttributeNS(OpenDocument::NS_TEXT, 'reference-format');
		if (!empty($reffmt)) {
			$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'reference-format', $reffmt);
			$this->refformat = $refname;
		}

		$this->allowedElements = array();
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_NoteCitation
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

		$element = new OpenDocument_ReferenceRef($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $content);
		$node->appendChild($element->node);

		if (is_scalar($content)) {
			$element->createTextElement($content);
		}
		return $element;
	}

	/**
	 * Set element properties
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		if (!empty($value)) {
			switch ($name) {
				case 'reference-format':
					$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'reference-format', $value);
					break;
				case 'ref-name':
					$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'ref-name', $value);
					break;
				default:
			}
		}
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
	 * Create OpenDocument_TextElement
	 *
	 * @param string $text
	 * @return OpenDocument_TextElement
	 */
	public function createTextElement($text) {
		return OpenDocument_TextElement::instance($this, $text);
	}
}
?>
