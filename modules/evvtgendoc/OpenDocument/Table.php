<?php
/**
* OpenDocument_Table class
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

require_once 'StyledElement.php';
require_once 'TableCell.php';

/**
* OpenDocument_Table element
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Table extends OpenDocument_StyledElement {

	/**
	 * Table Cells
	 *
	 * @var integer
	 */
	private $cells;

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
	const nodeNS = OpenDocument::NS_TABLE;

	/**
	 * Node namespace
	 */
	const nodePrefix = 'table';

	/**
	 * Node name
	 */
	const nodeName = 'table';

	/**
	 * Element style name prefix
	 */
	const styleNamePrefix = 'table';

	public $issubtable;

	/**
	 * Constructor
	 *
	 * @param DOMNode $node
	 * @param OpenDocument $document
	 */
	public function __construct(DOMNode $node, OpenDocument $document, $subtable = '') {
		parent::__construct($node, $document);
		return true;
	}

	/**
	 * Create OpenDocument_Table element
	 *
	 * @param mixed $object
	 * @param mixed $content
	 * @param integer $level optional
	 * @return OpenDocument_Table
	 */
	public static function instance($object, $subtable = '') {
		if ($object instanceof OpenDocument) {
			$document = $object;
			$node = $object->cursor;
		} elseif ($object instanceof OpenDocument_Element) {
			$document = $object->getDocument();
			$node = $object->getNode();
		} else {
			throw new OpenDocument_Exception(OpenDocument_Exception::ELEM_OR_DOC_EXPECTED);
		}

		$element = new OpenDocument_Table($node->ownerDocument->createElementNS(self::nodeNS, self::nodeName), $document, $subtable);
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
		if ($name=='level') {
			if (!is_int($value) && !ctype_digit($value)) {
				$value = 1;
			}
			$this->type = $value;
			$this->node->setAttributeNS(OpenDocument::NS_TABLE, 'outline-level', $value);
		}
	}

	/**
	 * Get element properties
	 *
	 * @param string  $name
	 * @return mixed
	 */
	public function __get($name) {
		if ($value = parent::__get($name)) {
			return $value;
		}
		if (isset($this->$name)) {
			return $this->$name;
		}
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

	/**
	 * Get style name
	 *
	 * @return string
	 */
	public function getStyleName() {
		return $this->node->getAttributeNS(OpenDocument::NS_TABLE, 'style-name');
	}

	/**
	 * Apply style information
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function applyStyle($name, $value, $elemtype) {
		$style_name = $this->node->getAttributeNS(OpenDocument::NS_TABLE, 'style-name');
		$style_name = $this->document->applyStyle($style_name, $name, $value, $this, $elemtype);
		$this->node->setAttributeNS(OpenDocument::NS_TABLE, 'style-name', $style_name);
	}

	/************** Elements ***********************/

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
	 * Create OpenDocument_Span element
	 *
	 * @param string $text
	 * @return OpenDocument_Span
	 */
	public function createSpan($text) {
		return OpenDocument_Span::instance($this, $text);
	}
	/**
	 *
	 * @access      public
	 * @since       0.5.0 - 08. Feb. 2007
	 */
	public function getCell($col, $row) {
		$ret = null;
		if (isset($this->cells[$row]) && isset($this->cells[$row][$col])) {
			$ret = $this->cells[$row][$col];
		} else {
			$ret = new TableCell(null, $col, $row);
			$this->cells[$row][$col] = $ret;
		}
		return $ret;
	}

	/**
	 * Create OpenDocument_TableColumn
	 *
	 * @param string $text optional
	 * @return OpenDocument_TableColumn
	 * @access public
	 */
	public function createTableColumn($numcols = '') {
		return OpenDocument_TableColumn::instance($this, $numcols);
	}

	/**
	 * Create OpenDocument_TableRow
	 *
	 * @param string $text optional
	 * @return OpenDocument_TableRow
	 * @access public
	 */
	public function createTableRow($text = '') {
		return OpenDocument_TableRow::instance($this);
	}

	/**
	 * Create OpenDocument_TableRow
	 *
	 * @param string $text optional
	 * @return OpenDocument_TableRow
	 * @access public
	 */
	public function createTableHeaderRow() {
		return OpenDocument_TableHeaderRow::instance($this);
	}

	/**
	 *
	 * @access      public
	 * @since       0.5.0 - 08. Feb. 2007
	 * @return      TableCell
	 */
	public function setCellContent($content, $col, $row) {
		$tmp = $this->getCell($col, $row);
		$tmp->setContent($content);
		return $tmp;
	}

	/**
	 * @deprecated NOT USED AND INCORRECT!
	 * @access      public
	 * @since       0.5.0 - 08. Feb. 2007
	 */
	private function includeCells() {
		$tablecells = $this->cells;
		ksort($tablecells);
		$currentRow = 0;
		foreach ($this->cells as $row => $cols) {
			if ($currentRow < $row -1) {
				$tbRow = $this->documentElement->createElementNS(OpenDocument::NS_TABLE, 'table-row');
				$tbRow->setAttributeNS(OpenDocument::NS_TABLE, 'table:number-rows-repeated', ($row - $currentRow -1));
				$tbCell = $this->documentElement->createElementNS(OpenDocument::NS_TABLE, 'table-cell');
				$tbCell->setAttributeNS(OpenDocument::NS_TABLE, 'table:style-name', 'ce1');
				$tbRow->appendChild($tbCell);
				$this->appendChild($tbRow);
			}
			ksort($cols);
			$currentCol = 0;
			$tbRow = $this->documentElement->createElementNS(OpenDocument::NS_TABLE, 'table-row');
			foreach ($cols as $col => $cell) {
				if ($currentCol < $col -1) {
					$tbCell = $this->documentElement->createElementNS(OpenDocument::NS_TABLE, 'table-cell');
					$tbCell->setAttributeNS(OpenDocument::NS_TABLE, 'table:number-columns-repeated', ($col - $currentCol -1));
					$tbRow->appendChild($tbCell);
				}
				$currentCol = $col;
				$tbRow->appendChild($cell->getDocumentFragment());
			}
			$this->appendChild($tbRow);
			$currentRow = $row;
		}
	}

	/**
	 * @deprecated NOT USED!
	 * @access      public
	 * @since       0.5.0 - 08. Feb. 2007
	 */
	public function getDocumentFragment() {
		$this->includeCells();
		return $this;
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
}
?>
