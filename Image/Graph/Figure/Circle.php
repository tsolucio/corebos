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
 * @subpackage Figure
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    SVN: $Id: Circle.php 291170 2009-11-23 03:50:22Z neufeind $
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/Figure/Ellipse.php
 */
require_once 'Image/Graph/Figure/Ellipse.php';

/**
 * Circle to draw on the canvas
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Figure
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.8.0
 * @link       http://pear.php.net/package/Image_Graph
 */
class Image_Graph_Figure_Circle extends Image_Graph_Figure_Ellipse
{

    /**
     * Image_Graph_Circle [Constructor]
     *
     * @param int $x      The center pixel of the circle on the canvas
     * @param int $y      The center pixel of the circle on the canvas
     * @param int $radius The radius in pixels of the circle
     */
    function Image_Graph_Figure_Circle($x, $y, $radius)
    {
        parent::__construct($x, $y, $radius, $radius);
    }

}
?>
