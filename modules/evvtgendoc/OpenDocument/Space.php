<?php
/**
* OpenDocument_TextSpace class
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
* OpenDocument_TextSpace element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_TextSpace extends OpenDocument_StyledElement {

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
	const nodeName = 's';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'TS';

	public $numspaces;

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document, $numspc = '') {
		parent::__construct($node, $document);
		$numspaces = $node->getAttributeNS(OpenDocument::NS_TEXT, 'c');
		if (empty($numspaces)) {
			$numspaces=$numspc;
		}
		if (!empty($numspaces)) {
			$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'c', $numspaces);
			$this->numspaces = $numspaces;
		}

		$this->allowedElements = array();
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_TextSpace
	 * @throws OpenDocument_Exception
	 */
	public static function instance($object, $numspc = '') {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_TextSpace($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $numspc);
		$node->appendChild($element->node);

		return $element;
	}

	/**
	 * Set element properties
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		if ($name=='numspaces') {
			if (!is_int($value) && !ctype_digit($value)) {
				$value = 1;
			}
			$this->node->setAttributeNS(OpenDocument::NS_TEXT, 'c', $value);
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
}
?>
