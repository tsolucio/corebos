<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Image_Canvas
 *
 * Canvas based creation of images to facilitate different output formats
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, see
 * <http://www.gnu.org/licenses/>
 *
 * @category  Images
 * @package   Image_Canvas
 * @author    Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author    Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright 2003-2009 The PHP Group
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/Image_Canvas
 */

/**
* Color class to be extended; from package PEAR::Image_Color
*/
require_once 'Image/Color.php';

/**
* Class for color-handling
*
* This is used to extend the functionality of the current PEAR::Image_Color v0.4.
* I hope to be allowed to incorporate some of the improvements in a new Image_Color release.
*
* @author   Stefan Neufeind <pear.neufeind@speedpartner.de>
* @package  Image_Canvas
* @access   public
*/
class Image_Canvas_Color extends Image_Color
{
    /**
    * Allocates a color in the given image.
    *
    * Userdefined color specifications get translated into
    * an array of rgb values.
    *
    * @param resource &$img  GD-resource
    * @param mixed    $color Any color representation supported by color2RGB()
    *
    * @return resource Image color handle
    * @see color2RGB()
    * @access public
    * @static
    */
    function allocateColor(&$img, $color)
    {
        $color = Image_Canvas_Color::color2RGB($color);

        if (($color[3] == 255) || (!function_exists("imagecolorallocatealpha"))) {
            return imagecolorallocate($img, $color[0], $color[1], $color[2]);
        } else {
            return imagecolorallocatealpha($img, $color[0], $color[1], $color[2], 127-round(($color[3]*127)/255));
        }
    }

    /**
    * Convert any color-representation into an array of 4 ints (RGBA).
    *
    * Userdefined color specifications get translated into
    * an array of rgb values.
    *
    * @param mixed $color Any color representation supported by Image_Canvas_Color::color2RGB()
    *
    * @return array Array of 4 ints (RGBA-representation)
    * @access public
    * @static
    */
    function color2RGB($color)
    {
        if (is_array($color)) {
            if (!is_numeric($color[0])) {
                return null; // error
            }
            if (count($color) == 3) { // assume RGB-color

                // 255 = alpha-value; full opaque
                return array((int) $color[0],
                             (int) $color[1],
                             (int) $color[2],
                             255);
            }
            if (count($color) == 4) { // assume RGBA-color

                // 255 = alpha-value; full opaque
                return array((int) $color[0],
                             (int) $color[1],
                             (int) $color[2],
                             (int) $color[3]);
            }
            return null; // error
        } elseif (is_string($color)) {
            $alphaPos = strpos($color, '@');
            if ($alphaPos === false) {
                $alpha = 255;
            } else {
                $alphaFloat = (float) substr($color, $alphaPos+1);
                // restrict to range 0..1
                $alphaFloat = max(min($alphaFloat, 1), 0);
                $alpha = (int) round((float) 255 * $alphaFloat);
                $color = substr($color, 0, $alphaPos);
            }
            if ($color[0] == '#') {  // hex-color given, e.g. #FFB4B4
                $tempColor = parent::hex2rgb($color);
                return array((int) $tempColor[0],
                             (int) $tempColor[1],
                             (int) $tempColor[2],
                             $alpha);
            }
            if (strpos($color, '%') !== false) {
                $tempColor = parent::percentageColor2RGB($color);
                return array((int) $tempColor[0],
                             (int) $tempColor[1],
                             (int) $tempColor[2],
                             $alpha);
            } else {
                $tempColor = parent::namedColor2RGB($color);
                return array((int) $tempColor[0],
                             (int) $tempColor[1],
                             (int) $tempColor[2],
                             $alpha);
            }
        } else {
            return null; // error
        }
    }

    /**
    *   getRange
    *   Given a degree, you can get the range of colors between one color and
    *   another color.
    *
    *   @param string $degrees How much each 'step' between the colors we should take.
    *
    *   @return array Array of all the colors, one element for each color.
    *   @access public
    */
    function getRange ($degrees)
    {
        $tempColors = parent::getRange($degrees);

        // now add alpha-channel information
        $steps = count($tempColors);
        for ($counter=0;$counter<$steps;$counter++) {
            $tempColors[$counter] = parent::hex2rgb($tempColors[$counter]);
            unset($tempColors[$counter]['hex']);
            $tempColors[$counter][3] = (int) round(
                (((float) $this->color1[3]*($steps-$counter))+
                 ((float) $this->color2[3]*($counter))
                ) / $steps
            );
        }

        return $tempColors;
    }

    /**
    * Internal method to correctly set the colors.
    *
    * @param mixed $col1 color 1
    * @param mixed $col2 color 2
    *
    * @return void
    * @access   private
    */
    function _setColors ( $col1, $col2 )
    {
        $this->color1 = Image_Canvas_Color::color2RGB($col1);
        $this->color2 = Image_Canvas_Color::color2RGB($col2);
    }
}
?>