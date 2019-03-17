<?php
/**
* OpenDocument_Section class
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
* Copyright 2018 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
*/

require_once 'StyledElement.php';

/**
* OpenDocument_Section element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Section extends OpenDocument_StyledElement {

	/**
	 * Collection of children objects
	 *
	 * @var ArrayIterator
	 * @access read-only
	 */
	public $children;

	/**
	 * Node namespace
	 */
	const nodeNS = OpenDocument::NS_TEXT;

	/**
	 * Node namespace
	 */
	const nodePrefix = 'section';

	/**
	 * Node name
	 */
	const nodeName = 'section';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'Sect';

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
			'OpenDocument_Paragraph',
		);
	}

	/**
	 * Create OpenDocument_Section element
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @param integer $level optional
	 * @return OpenDocument_Section
	 */
	public static function instance($object) {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_Section($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document);
		$node->appendChild($element->node);

		return $element;
	}

	/**
	 * Generate element new style name
	 *
	 * @return string
	 */
	public function generateStyleName() {
		self::$styleNameMaxNumber ++;
		return self::styleNamePrefix . self::$styleNameMaxNumber;
	}

	public function createParagraph($text = '') {
		return OpenDocument_Paragraph::instance($this, $text);
	}

	public function createSpan($text) {
		return OpenDocument_Span::instance($this, $text);
	}
}
?>
