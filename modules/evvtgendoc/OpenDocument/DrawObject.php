<?php
/**
* OpenDocument_DrawObject class
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
* Copyright 2015 JPL TSolucio, S.L.   --   This file is a part of coreBOS.
*/

require_once 'Element.php';

/**
* OpenDocument_DrawObject element
*
* @category   File Formats
* @package    OpenDocument
* @author     Joe Bordes
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_DrawObject extends OpenDocument_StyledElement {

	/**
	 * Collection of children objects
	 *
	 * @var ArrayIterator
	 * @access read-only
	 */
	public $children;

	// Atributos
	public $href;
	public $type;
	public $show;
	public $actuate;
	public $draw_notifyonupdateofranges;
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
	const nodeName = 'object';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'obj';

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document) {
		parent::__construct($node, $document);
		$this->allowedElements = array();
		return true;
	}

	/**
	 * Create element instance
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @return OpenDocument_FrameImage
	 * @throws OpenDocument_Exception
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
		$text = $href = $type = $show = $actuate = $draw_notifyonupdateofranges = '';
		$element = new OpenDocument_FrameImage($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $text, $href, $type, $show, $actuate, $draw_notifyonupdateofranges);
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
}
?>
