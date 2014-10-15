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
 * @subpackage Marker
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    SVN: $Id: Pointing.php 291406 2009-11-29 00:54:22Z neufeind $
 * @link       http://pear.php.net/package/Image_Graph
 */
 
/**
 * Include file Image/Graph/Marker.php
 */
require_once 'Image/Graph/Marker.php';

/**
 * Data marker as a 'pointing marker'.
 *
 * Points to the data using another marker (as start and/or end)
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Marker
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.8.0
 * @link       http://pear.php.net/package/Image_Graph
 */
class Image_Graph_Marker_Pointing extends Image_Graph_Marker
{

    /**
     * The starting marker
     * @var Marker
     * @access private
     */
    var $_markerStart;

    /**
     * The ending marker
     * @var Marker
     * @access private
     */
    var $_markerEnd;

    /**
     * The X offset from the 'data'
     * @var int
     * @access private
     */
    var $_deltaX = -1;

    /**
     * The Y offset from the 'data'
     * @var int
     * @access private
     */
    var $_deltaY = -1;

    /**
     * Create an pointing marker, ie a pin on a board
     *
     * @param int    $deltaX     The the X offset from the real 'data' point
     * @param int    $deltaY     The the Y offset from the real 'data' point
     * @param Marker &$markerEnd The ending marker that represents 'the head of
     *   the pin'
     */
    function Image_Graph_Marker_Pointing($deltaX, $deltaY, & $markerEnd)
    {
        parent::__construct();
        $this->_deltaX = $deltaX;
        $this->_deltaY = $deltaY;
        $this->_markerStart = null;
        $this->_markerEnd =& $markerEnd;
    }

    /**
     * Sets the starting marker, ie the tip of the pin on a board
     *
     * @param Marker &$markerStart The starting marker that represents 'the tip
     *   of the pin'
     *
     * @return void
     */
    function setMarkerStart(& $markerStart)
    {
        $this->_markerStart =& $markerStart;
        $this->_markerStart->_setParent($this);
    }

    /**
     * Draw the marker on the canvas
     *
     * @param int   $x      The X (horizontal) position (in pixels) of the marker on
     *   the canvas
     * @param int   $y      The Y (vertical) position (in pixels) of the marker on the
     *   canvas
     * @param array $values The values representing the data the marker 'points' to
     *
     * @return void
     * @access private
     */
    function _drawMarker($x, $y, $values = false)
    {
        parent::_drawMarker($x, $y, $values);
        if ($this->_markerStart) {
            $this->_markerStart->_setParent($this);
            $this->_markerStart->_drawMarker($x, $y, $values);
        }
        $this->_getLineStyle();
        $this->_canvas->line(array('x0' => $x, 'y0' => $y, 'x1' => $x + $this->_deltaX, 'y1' => $y + $this->_deltaY));
        $this->_markerEnd->_setParent($this);
        $this->_markerEnd->_drawMarker(
            $x + $this->_deltaX,
            $y + $this->_deltaY,
            $values
        );
    }

}

?>
