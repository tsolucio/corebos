<?php
/**
* OpenDocument_Footnote class
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
* OpenDocument_Footnote element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Footnote extends OpenDocument_StyledElement {

	/**
	 * Node namespace
	 */
	const nodeNS = OpenDocument::NS_TEXT;

	/**
	 * Node amespace
	 */
	const nodePrefix = 'text';

	/**
	 * Node name
	 */
	const nodeName = 'note';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'N';

	public $id;
	public $noteclass;
	public static $id_counter=0;

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document, $content, $idval = '', $ncval = 'footnote') {
		parent::__construct($node, $document);
		$this->allowedElements = array(
			'OpenDocument_NoteCitation',
			'OpenDocument_NoteBody'
		);
		return true;
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_Footnote
	 * @throws OpenDocument_Exception
	 */
	public static function instance($object, $content, $idval = '', $ncval = 'footnote') {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_Footnote($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $content, $idval, $ncval);
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
		switch ($name) {
			case 'id':
				if (empty($value)) {
					$value = self::$id_counter++;
				}
				$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'id', $value);
				break;
			case 'noteclass':
				if (empty($value)) {
					$value='footnote';
				}
				$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'note-class', $value);
				break;
			default:
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
	public function createNoteCitation($text = '') {
		return OpenDocument_NoteCitation::instance($this, $text);
	}
	public function createNoteBody() {
		return OpenDocument_NoteBody::instance($this);
	}
}
?>
