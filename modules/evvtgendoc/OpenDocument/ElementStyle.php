<?php
/**
* OpenDocument_ElementStyle class concretes element style object
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

require_once 'Style.php';

class OpenDocument_ElementStyle extends OpenDocument_Style {
	/**
	 * Possible properties values
	 *
	 */
	const
			FONT_WEIGHT_BOLD = 'bold',
			FONT_WEIGHT_NORMAL = 'normal',
			FONT_STYLE_ITALIC = 'italic',
			FONT_STYLE_NORMAL = 'normal',
			UNDERLINE_STYLE_SOLID = 'solid';

	/**
	 * List of properties
	 *
	 */
	protected $fontWeight;
	protected $fontStyle;
	protected $fontName;
	protected $fontSize;
	protected $backgroundColor;
	protected $color;
	protected $lineHeight;
	protected $underlineStyle;
	protected $underlineWidth;
	protected $underlineColor;

	/**
	 * Constructor
	 *
	 * @param OpenDocument_StyledElement $element
	 */
	public function __construct($element) {
		$this->map = array(
			'fontWeight' => 'fo:font-weight',
			'fontStyle' => 'fo:font-style',
			'fontName' => 'style:font-name',
			'fontSize' => 'fo:font-size',
			'backgroundColor' => 'fo:background-color',
			'color' => 'fo:color',
			'lineHeight' => 'style:line-height',
			'underlineStyle' => 'style:text-underline-style',
			'underlineWidth' => 'style:text-underline-width',
			'underlineColor' => 'style:text-underline-color'
		);
		parent::__construct($element);
	}

	/**
	 * Set style value
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		if ($name == 'fontName') {
			$this->element->getDocument()->addFont($value);
		}
		parent::__set($name, $value);
	}
}
?>
