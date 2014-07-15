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
 * @subpackage Grid
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    SVN: $Id: Grid.php 291170 2009-11-23 03:50:22Z neufeind $
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/Plotarea/Element.php
 */
require_once 'Image/Graph/Plotarea/Element.php';

/**
 * A grid displayed on the plotarea.
 *
 * A grid is associated with a primary and a secondary axis. The grid is
 * displayed in context of the primary axis' label interval - meaning that a
 * grid for an Y-axis displays a grid for every label on the y-axis (fx. a {@link
 * Image_Graph_Grid_Lines}, which displays horizontal lines for every label on
 * the y-axis from the x-axis minimum to the x-axis maximum). You should always
 * add the grid as one of the first elements to the plotarea. This is due to the
 * fact that elements are 'outputted' in the order they are added, i.e. if an
 * grid is added *after* a chart, the grid will be displayed on top of the chart
 * which is (probably) not desired.
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Grid
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.8.0
 * @link       http://pear.php.net/package/Image_Graph
 * @abstract
 */
class Image_Graph_Grid extends Image_Graph_Plotarea_Element
{

    /**
     * The primary axis: the grid 'refers' to
     * @var Axis
     * @access private
     */
    var $_primaryAxis = null;

    /**
     * The secondary axis
     * @var Axis
     * @access private
     */
    var $_secondaryAxis = null;
    
    /**
     * The starting point of the grid
     * @var mixed
     * @access private
     */
    var $_gridStart = '#min#';
    
    /**
     * The ending point of the grid
     * @var mixed
     * @access private
     */
    var $_gridEnd = '#max#';

    /**
     * Set the primary axis: the grid should 'refer' to
     *
     * @param Image_Graph_Axis &$axis The axis
     *
     * @return void
     * @access private
     */
    function _setPrimaryAxis(& $axis)
    {
        $this->_primaryAxis =& $axis;
    }

    /**
     * Set the secondary axis
     *
     * @param Image_Graph_Axis &$axis The axis
     *
     * @return void
     * @access private
     */
    function _setSecondaryAxis(& $axis)
    {
        $this->_secondaryAxis =& $axis;
    }

    /**
     * Get the points on the secondary axis that the grid should 'connect'
     *
     * @return array The secondary data values that should mark the grid 'end points'
     * @access private
     */
    function _getSecondaryAxisPoints()
    {
        if (is_a($this->_secondaryAxis, 'Image_Graph_Axis_Radar')) {
            $secondaryValue = false;
            $firstValue = $secondaryValue;
            while (($secondaryValue = $this->_secondaryAxis->_getNextLabel($secondaryValue)) !== false) {
                $secondaryAxisPoints[] = $secondaryValue;
            }
            $secondaryAxisPoints[] = $firstValue;
        } else {
            $secondaryAxisPoints = array ($this->_gridStart, $this->_gridEnd);
        }
        return $secondaryAxisPoints;
    }

    /**
     * Get the X pixel position represented by a value
     *
     * @param double $point the value to get the pixel-point for
     *
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointX($point)
    {
        if (($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_Y)
            || ($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY)
        ) {
            $point['AXIS_Y'] = $this->_primaryAxis->_type;
        } else {
            $point['AXIS_Y'] = $this->_secondaryAxis->_type;
        }
        return parent::_pointX($point);
    }

    /**
     * Get the Y pixel position represented by a value
     *
     * @param double $point the value to get the pixel-point for
     *
     * @return double The pixel position along the axis
     * @access private
     */
    function _pointY($point)
    {
        if (($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_Y)
            || ($this->_primaryAxis->_type == IMAGE_GRAPH_AXIS_Y_SECONDARY)
        ) {
            $point['AXIS_Y'] = $this->_primaryAxis->_type;
        } else {
            $point['AXIS_Y'] = $this->_secondaryAxis->_type;
        }
        return parent::_pointY($point);
    }

    /**
     * Causes the object to update all sub elements coordinates.
     *
     * @return void
     * @access private
     */
    function _updateCoords()
    {
        $this->_setCoords(
            $this->_parent->_plotLeft,
            $this->_parent->_plotTop,
            $this->_parent->_plotRight,
            $this->_parent->_plotBottom
        );
        parent::_updateCoords();
    }
    
    /**
     * Sets the starting and ending points of the grid,
     * these defaults to 'min' and 'max' which signals that the grid should
     * span the entire the perpendicular axis
     *
     * @param mixed $start The starting value, use 'min' to start and "beginning"  of the perpendicular axis
     * @param mixed $end   The starting value, use 'min' to start and "end" of the perpendicular axis
     *
     * @return void
     */
    function setAxisPoints($start = 'min', $end = 'max')
    {
        // no check for 'max' since it would make no sense
        $this->_gridStart = ($start === 'min' ? '#min#' : $start);
        // same - no check for 'min' 
        $this->_gridEnd = ($end === 'max' ? '#max#' : $end);
    }
}

?>
