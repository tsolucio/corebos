<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Image_Graph - PEAR PHP OO Graph Rendering Utility.
 *
 * PHP version 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Text
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    SVN: $Id: Font.php 291170 2009-11-23 03:50:22Z neufeind $
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/Common.php
 */
require_once 'Image/Graph/Common.php';

/**
 * A font.
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Text
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.8.0
 * @link       http://pear.php.net/package/Image_Graph
 * @abstract
 */
class Image_Graph_Font extends Image_Graph_Common
{

    /**
     * The name of the font
     * @var string
     * @access private
     */
    var $_name = false;

    /**
     * The angle of the output
     * @var int
     * @access private
     */
    var $_angle = false;

    /**
     * The size of the font
     * @var int
     * @access private
     */
    var $_size = 11;
    
    /**
     * The color of the font
     * @var Color
     * @access private
     */
    var $_color = 'black';

    /**
     * Image_Graph_Font [Constructor]
     *
     * @param string $name Font name
     * @param int    $size Font size
     */
    function Image_Graph_Font($name = false, $size = false)
    {
        parent::__construct();
        if ($name !== false) {
            $this->_name = $name;
        }
        if ($size !== false) {
            $this->_size = $size;
        }
    }

    /**
     * Set the color of the font
     *
     * @param mixed $color The color object of the Font
     *
     * @return void
     */
    function setColor($color)
    {
        $this->_color = $color;
    }

    /**
     * Set the angle slope of the output font.
     *
     * 0 = normal, 90 = bottom and up, 180 = upside down, 270 = top and down
     *
     * @param int $angle The angle in degrees to slope the text
     *
     * @return void
     */
    function setAngle($angle)
    {
        $this->_angle = $angle;
    }

    /**
     * Set the size of the font
     *
     * @param int $size The size in pixels of the font
     *
     * @return void
     */
    function setSize($size)
    {
        $this->_size = $size;
    }
    
    /**
     * Get the font 'array'
     *
     * @param array $options Font options (optional)
     *
     * @return array The font 'summary' to pass to the canvas
     * @access private
     */
    function _getFont($options = false)
    {
        if ($options === false) {
            $options = array();
        }
        
        if ($this->_name !== false) {
            $options['name'] = $this->_name;
        }
        
        if (!isset($options['color'])) {
            $options['color'] = $this->_color;
        }

        if (!isset($options['size'])) {
            $options['size'] = $this->_size;
        }

        if ((!isset($options['angle'])) && ($this->_angle !== false)) {
            $options['angle'] = $this->_angle;
        }
        return $options;
    }

}

?>
