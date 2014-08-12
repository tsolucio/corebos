<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling output in SWF flash format.
 *
 * Requires PHP extension ming
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
 * @author    Torsten Roehr <troehr@php.net>
 * @author    Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright 2003-2009 The PHP Group
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/Image_Canvas
 */

/**
 * Include file Image/Canvas.php
 */
require_once 'Image/Canvas.php';

/**
 * Include file Image/Canvas/Color.php
 */
require_once 'Image/Canvas/Color.php';

/**
 * SVG Canvas class.
 *
 * @category  Images
 * @package   Image_Canvas
 * @author    Torsten Roehr <troehr@php.net>
 * @author    Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright 2003-2009 The PHP Group
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Image_Canvas
 */
class Image_Canvas_SWF extends Image_Canvas
{

    /**
     * The canvas of the graph
     * @var object SWFMovie
     * @access private
     */
    var $_canvas;

    /**
     * The default Flash version
     *
     * Ming supports up to Flash version 6
     *
     * @var int
     * @access private
     */
    var $_version = 6;

    /**
     * Creates the SWF movie object
     *
     * Parameters available:
     *
     * 'width'      The width of the graph
     * 'height'     The height of the graph
     * 'version'    The flash version, supports up to version 6
     * 'background' An array with the background color, e.g.
     *              array('red' => 255,
     *                    'green' => 0,
     *                    'blue' => 0)
     *              Either integers between 0 and 255 or hexadecimals
     *              between 0x00 and 0xFF
     *
     * @param array $params Parameter array
     *
     * @return Image_Canvas_SWF
     */
    function Image_Canvas_SWF($params)
    {
        parent::Image_Canvas($params);
        $this->_reset();

        $version = (isset($params['version']) && $params['version'] <= 6)
                   ? $params['version'] : $this->_version;

        $this->_canvas = new SWFMovie($version);
        $this->_canvas->setDimension($this->_width, $this->_height);

        if (isset($params['background'])) {
            $this->setBackground($params['background']);
        }
    }

    /**
     * Sets the background color
     *
     * Values can be specified either as integers between 0 and 255 or as hexadecimals between 0x00 and 0xFF
     *
     * @param mixed $color Color
     *
     * @return void
     * @access public
     */
    function setBackground($color)
    {
        $color = Image_Canvas_Color::color2RGB($color);
        $this->_canvas->setBackground($color[0], $color[1], $color[2]);
    }

    /**
     * Add an object to the movie
     *
     * @param string $element The element
     *
     * @return void
     * @access public
     */
    function addElement($element)
    {
        $this->_canvas->add($element);
    }

    /**
     * Get the color index for the RGB color
     *
     * @param int $color The color
     *
     * @return int A SVG compatible color
     * @access private
     */
    function _color($color = false)
    {
        if ($color === false) {
            return array();
        } else {
            return Image_Canvas_Color::color2RGB($color);
        }
    }

    /**
     * Get the opacity for the RGB color
     *
     * @param int $color The color
     *
     * @return int A SWF compatible opacity value
     * @access private
     */
    function _opacity($color = false)
    {
        if ($color === false) {
            return false;
        } else {
            $color = Image_Canvas_Color::color2RGB($color);
            if ($color[3] != 255) {
                return sprintf('%0.1f', $color[3]/255);
            } else {
                return 255;
            }
        }
    }

    /**
     * Get the applicable linestyle
     *
     * @param mixed $lineStyle The line style to return, false if the one
     *   explicitly set
     *
     * @return mixed A compatible linestyle
     * @access private
     */
    function _getLineStyle($lineStyle = false)
    {
        if ($lineStyle === false) {
            $lineStyle = $this->_lineStyle;
        }

        return $this->_color($lineStyle);
    }

    /**
     * Get the applicable fillstyle
     *
     * @param mixed $fillStyle The fillstyle to return, false if the one
     *   explicitly set
     *
     * @return mixed A compatible fillstyle
     * @access private
     */
    function _getFillStyle($fillStyle = false)
    {
        if ($fillStyle === false) {
            $fillStyle = $this->_fillStyle;
        }

        return $this->_color($fillStyle);
    }

    /**
     * Sets an image that should be used for filling
     *
     * @param string $filename The filename of the image to fill with
     *
     * @todo
     * @return void
     */
    function setFillImage($filename)
    {
    }

    /**
     * Sets a gradient fill
     *
     * @param array $gradient Gradient fill options
     *
     * @todo
     * @return void
     */
    function setGradientFill($gradient)
    {
    }

    /**
     * Sets the font options.
     *
     * The $font array may have the following entries:
     * 'type' : 'ttf' (TrueType) or omitted for default<br>
     * If 'type' is 'ttf' then the following can be specified<br>
     * 'size'  : size in pixels<br>
     * 'angle' : the angle with which to write the text
     * 'file'  : the .ttf file (either the basename, filename or full path)
     *
     * @param array $fontOptions The font options.
     *
     * @return void
     */
    function setFont($fontOptions)
    {
        parent::setFont($fontOptions);
        if (!isset($this->_font['size'])) {
            $this->_font['size'] = 10;
        }
    }

    /**
     * Draw a line end
     *
     * Parameter array:
     * 'x'     : int X point
     * 'y'     : int Y point
     * 'end'   : string The end type of the end
     * 'size'  : int The size of the end
     * 'color' : string The color of the end
     * 'angle' : int [optional] The angle with which to draw the end
     * 'url'   : string [optional] Target URL
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function drawEnd($params)
    {
        $x     = $this->_getX($params['x']);
        $y     = $this->_getY($params['y']);
        $size  = $params['size'];
        $angle = deg2rad((isset($params['angle']) ? $params['angle'] : 0));
        $pi2   = pi() / 2;

        switch ($params['end']) {

        case 'lollipop':
        case 'circle':
            if (($fill = $this->_getFillStyle($params['color'])) !== false) {
                $shapeObj = new SWFShape();
                $shapeObj->setRightFill($fill[0], $fill[1], $fill[2]);
                $shapeObj->movePenTo($x + $size / 2, $y);
                $shapeObj->drawCircle($size / 2);

                if (isset($params['url'])) {
                    $button = new SWFButton();
                    $button->addShape($shapeObj, SWFBUTTON_HIT | SWFBUTTON_UP | SWFBUTTON_DOWN | SWFBUTTON_OVER);
                    $button->addAction(new SWFAction("getURL('{$params['url']}');"), SWFBUTTON_MOUSEUP);
                    $this->_canvas->add($button);
                } else {
                    $this->_canvas->add($shapeObj);
                }

                parent::drawEnd($params);
            }
            break;

        case 'diamond':
            $x0    = round($params['x'] + cos($angle) * $size * 0.65);
            $y0    = round($params['y'] - sin($angle) * $size * 0.65);
            $shape = array(
                array($x0 + round(cos($angle) * $size * 0.65),
                      $y0 - round(sin($angle) * $size * 0.65)),
                array($x0 + round(cos($angle + $pi2) * $size * 0.65),
                      $y0 - round(sin($angle + $pi2) * $size * 0.65)),
                array($x0 + round(cos($angle + pi()) * $size * 0.65),
                      $y0 - round(sin($angle + pi()) * $size * 0.65)),
                array($x0 + round(cos($angle + 3 * $pi2) * $size * 0.65),
                      $y0 - round(sin($angle + 3 * $pi2) * $size * 0.65))
            );
            break;

        case 'line':
            $shape = array(
                array($x + round(cos($angle + $pi2) * $size / 2),
                      $y - round(sin($angle + $pi2) * $size / 2)),
                array($x + round(cos($angle + 3 * $pi2) * $size / 2),
                      $y - round(sin($angle + 3 * $pi2) * $size / 2))
            );
            break;

        case 'box':
        case 'rectangle':
            $x0    = round($params['x'] + cos($angle) * $size / 2);
            $y0    = round($params['y'] - sin($angle) * $size / 2);
            $pi4   = pi() / 4;
            $shape = array(
                array($x0 + round(cos($angle + $pi4) * $size / 2),
                      $y0 - round(sin($angle + $pi4) * $size / 2)),
                array($x0 + round(cos($angle + $pi2 + $pi4) * $size / 2),
                      $y0 - round(sin($angle + $pi2 + $pi4) * $size / 2)),
                array($x0 + round(cos($angle + pi() + $pi4) * $size / 2),
                      $y0 - round(sin($angle + pi() + $pi4) * $size / 2)),
                array($x0 + round(cos($angle + 3 * $pi2 + $pi4) * $size / 2),
                      $y0 - round(sin($angle + 3 * $pi2 + $pi4) * $size / 2))
            );
            break;

        case 'arrow':
            $shape = array(
                array($x + cos($angle) * $size,
                      $y - sin($angle) * $size),
                array($x + cos($angle + $pi2) * $size * 0.4,
                      $y - sin($angle + $pi2) * $size * 0.4),
                array($x + cos($angle + 3 * $pi2) * $size * 0.4,
                      $y - sin($angle + 3 * $pi2) * $size * 0.4)
            );
            break;

        case 'arrow2':
            $shape = array(
                array($x + round(cos($angle) * $size),
                      $y - round(sin($angle) * $size)),
                array($x + round(cos($angle + $pi2 + deg2rad(45)) * $size),
                      $y - round(sin($angle + $pi2 + deg2rad(45)) * $size)),
                array($x,
                      $y),
                array($x + round(cos($angle + 3 * $pi2 - deg2rad(45)) * $size),
                      $y - round(sin($angle + 3 * $pi2 - deg2rad(45)) * $size))
            );
            break;
        }

        if (isset($shape)) {
            // output the shape
            if (($fill = $this->_getFillStyle($params['color'])) !== false) {
                $shapeObj = new SWFShape();
                $shapeObj->setRightFill($fill[0], $fill[1], $fill[2]);
                $shapeObj->setLine(0, $fill[0], $fill[1], $fill[2]);
                $shapeObj->movePenTo($shape[0][0], $shape[0][1]);
                for ($count = count($shape); $count--; $count > 0) {
                    $shapeObj->drawLineTo($shape[$count][0], $shape[$count][1]);
                }

                if (isset($params['url'])) {
                    $button = new SWFButton();
                    $button->addShape($shapeObj, SWFBUTTON_HIT | SWFBUTTON_UP | SWFBUTTON_DOWN | SWFBUTTON_OVER);
                    $button->addAction(new SWFAction("getURL('{$params['url']}');"), SWFBUTTON_MOUSEUP);
                    $this->_canvas->add($button);
                } else {
                    $this->_canvas->add($shapeObj);
                }
            }
        }
        parent::drawEnd($params);
    }

    /**
     * Parameter array:
     * 'x0'    : int X start point
     * 'y0'    : int Y start point
     * 'x1'    : int X end point
     * 'y1'    : int Y end point
     * 'color' : mixed [optional] The line color
     * 'url'   : string [optional] Target URL
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function line($params)
    {
        $x0 = $this->_getX($params['x0']);
        $y0 = $this->_getY($params['y0']);
        $x1 = $this->_getX($params['x1']);
        $y1 = $this->_getY($params['y1']);

        $color = (isset($params['color']) ? $params['color'] : false);
        $color = $this->_getLineStyle($color);

        $shape = new SWFShape();
        $shape->setLine(1, $color[0], $color[1], $color[2]);

        $shape->movePenTo($x0, $y0);
        $shape->drawLine($x1 - $x0, $y1 - $y0);

        if (isset($params['url'])) {
            $button = new SWFButton();
            $button->addShape($shape, SWFBUTTON_HIT | SWFBUTTON_UP | SWFBUTTON_DOWN | SWFBUTTON_OVER);
            $button->addAction(new SWFAction("getURL('{$params['url']}');"), SWFBUTTON_MOUSEUP);
            $this->_canvas->add($button);
        } else {
            $this->_canvas->add($shape);
        }

        parent::line($params);
    }

    /**
     * Parameter array:
     * 'connect': bool [optional] Specifies whether the start point should be
     *                            connected to the endpoint (closed polygon)
     *                            or not (connected line)
     * 'fill'   : mixed [optional] The fill color
     * 'line'   : mixed [optional] The line color
     * 'url'    : string [optional] Target URL
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function polygon($params = array())
    {
        $connectEnds = (isset($params['connect']) ? $params['connect'] : false);
        $fillColor   = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor   = (isset($params['line']) ? $params['line'] : false);

        $lineStyle = $this->_getLineStyle($lineColor);
        $fillStyle = $this->_getFillStyle($fillColor);

        $shape = new SWFShape();
        if ($connectEnds) {
            $shape->setRightFill($fillStyle[0], $fillStyle[1], $fillStyle[2]);
        }
        $shape->setLine(0, $lineStyle[0], $lineStyle[1], $lineStyle[2]);
        $shape->movePenTo($this->_polygon[0]['X'], $this->_polygon[0]['Y']);

        foreach ($this->_polygon as $point) {
            $shape->drawLineTo($point['X'], $point['Y']);
        }

        if ($connectEnds) {
            $shape->drawLineTo($this->_polygon[0]['X'], $this->_polygon[0]['Y']);
        }

        if (isset($params['url'])) {
            $button = new SWFButton();
            $button->addShape($shape, SWFBUTTON_HIT | SWFBUTTON_UP | SWFBUTTON_DOWN | SWFBUTTON_OVER);
            $button->addAction(new SWFAction("getURL('{$params['url']}');"), SWFBUTTON_MOUSEUP);
            $this->_canvas->add($button);
        } else {
            $this->_canvas->add($shape);
        }

        parent::polygon($params);
    }

    /**
     * Draw a rectangle
     *
     * Parameter array:
     * 'x0'   : int X start point
     * 'y0'   : int Y start point
     * 'x1'   : int X end point
     * 'y1'   : int Y end point
     * 'fill' : The fill style
     * 'line' : The line style
     * 'url'  : string [optional] Target URL
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function rectangle($params)
    {
        $x0 = min($this->_getX($params['x0']), $this->_getX($params['x1']));
        $y0 = min($this->_getY($params['y0']), $this->_getY($params['y1']));
        $x1 = max($this->_getX($params['x0']), $this->_getX($params['x1']));
        $y1 = max($this->_getY($params['y0']), $this->_getY($params['y1']));

        $fillColor = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor = (isset($params['line']) ? $params['line'] : false);

        $fillColor = $this->_getFillStyle($fillColor);
        $lineColor = $this->_getLineStyle($lineColor);

        // use fill color if no line color is set or transparent
        if (count($lineColor) === 0) {
            $lineColor = $fillColor;
        }

        $shape = new SWFShape();
        $shape->setLine(1, $lineColor[0], $lineColor[1], $lineColor[2]);

        if (count($fillColor)) {
            $shape->setRightFill($fillColor[0], $fillColor[1], $fillColor[2]);
        }

        $shape->movePenTo($x0, $y0);
        $shape->drawLine($x1 - $x0, 0);
        $shape->drawLine(0, $y1 - $y0);
        $shape->drawLine($x0 - $x1, 0);
        $shape->drawLine(0, $y0 - $y1);

        if (isset($params['url'])) {
            $button = new SWFButton();
            $button->addShape($shape, SWFBUTTON_HIT | SWFBUTTON_UP | SWFBUTTON_DOWN | SWFBUTTON_OVER);
            $button->addAction(new SWFAction("getURL('{$params['url']}');"), SWFBUTTON_MOUSEUP);
            $this->_canvas->add($button);
        } else {
            $this->_canvas->add($shape);
        }

        parent::rectangle($params);
    }

    /**
     * Draw an ellipse
     *
     * Parameter array:
     * 'x'    : int X center point
     * 'y'    : int Y center point
     * 'rx'   : int X radius
     * 'ry'   : int Y radius
     * 'fill' : mixed [optional] The fill color
     * 'line' : mixed [optional] The line color
     * 'url'  : string [optional] Target URL
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function ellipse($params)
    {
        $x  = $this->_getX($params['x']);
        $y  = $this->_getY($params['y']);
        $rx = $this->_getX($params['rx']);
        $ry = $this->_getY($params['ry']);

        // calculate scale factors
        $scaleX = 1.0;
        $scaleY = 1.0;
        $moveX  = 0;
        $moveY  = 0;

        if ($rx > $ry) {
            $scaleY = $ry / $rx;
            $moveY  = $ry * (1 - $scaleY);
        } elseif ($rx < $ry) {
            $scaleX = $rx / $ry;
            $moveX  = $rx * (1 - $scaleX);
        }

        $fillColor = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor = (isset($params['line']) ? $params['line'] : false);

        $fillColor = $this->_getFillStyle($fillColor);
        $lineColor = $this->_getLineStyle($lineColor);

        $shape = new SWFShape();
        $shape->setRightFill($fillColor[0], $fillColor[1], $fillColor[2]);
        $shape->movePenTo($x, $y);
        $shape->setLine(1, $lineColor[0], $lineColor[1], $lineColor[2]);

        if (count($fillColor)) {
            $shape->setRightFill($fillColor[0], $fillColor[1], $fillColor[2]);
        }

        $shape->drawCircle(max($rx, $ry));

        if (isset($params['url'])) {
            $button = new SWFButton();
            $button->addShape($shape, SWFBUTTON_HIT | SWFBUTTON_UP | SWFBUTTON_DOWN | SWFBUTTON_OVER);
            $button->addAction(new SWFAction("getURL('{$params['url']}');"), SWFBUTTON_MOUSEUP);
            $ellipse = $this->_canvas->add($button);
        } else {
            $ellipse = $this->_canvas->add($shape);
        }

        $ellipse->move($moveX, $moveY);
        $ellipse->scaleTo($scaleX, $scaleY);

        parent::ellipse($params);
    }

    /**
     * Draw a pie slice
     *
     * Parameter array:
     * 'x'    : int X center point
     * 'y'    : int Y center point
     * 'rx'   : int X radius
     * 'ry'   : int Y radius
     * 'v1'   : int The starting angle (in degrees)
     * 'v2'   : int The end angle (in degrees)
     * 'srx'  : int [optional] Starting X-radius of the pie slice (i.e. for a doughnut)
     * 'sry'  : int [optional] Starting Y-radius of the pie slice (i.e. for a doughnut)
     * 'fill' : mixed [optional] The fill color
     * 'line' : mixed [optional] The line color
     *
     * @param array $params Parameter array
     *
     * @todo
     * @return void
     */
    function pieslice($params)
    {
    }

    /**
     * Get the width of a text,
     *
     * @param string $text The text to get the width of
     *
     * @return int The width of the text
     */
    function textWidth($text)
    {
        if (isset($this->_font['vertical']) && $this->_font['vertical']) {
            return $this->_font['size'];
        } else {
            return round($this->_font['size'] * 0.5 * strlen($text));
        }
    }

    /**
     * Get the height of a text,
     *
     * @param string $text The text to get the height of
     *
     * @return int The height of the text
     */
    function textHeight($text)
    {
        if (isset($this->_font['vertical']) && $this->_font['vertical']) {
            return round($this->_font['size'] * 0.7 * strlen($text));
        } else {
            return $this->_font['size'];
        }
    }

    /**
     * Writes text
     *
     * Parameter array:
     * 'x'     : int X-point of text
     * 'y'     : int Y-point of text
     * 'text'  : string The text to add
     * 'color' : mixed [optional] The color of the text
     *
     * @param array $params Parameter array
     *
     * @todo Vertical alignment
     * @return void
     */
    function addText($params)
    {
        $x0         = $this->_getX($params['x']);
        $y0         = $this->_getY($params['y']);
        $text       = str_replace("\r", '', $params['text']);
        $color      = (isset($params['color']) ? $params['color'] : false);
        $textHeight = $this->textHeight($text);
        $alignment  = (isset($params['alignment']) ? $params['alignment'] : false);

        if (!is_array($alignment)) {
            $alignment = array('vertical' => 'top', 'horizontal' => 'left');
        }

        if (!isset($alignment['vertical'])) {
            $alignment['vertical'] = 'top';
        }

        if (!isset($alignment['horizontal'])) {
            $alignment['horizontal'] = 'left';
        }

        if (($color === false) && (isset($this->_font['color']))) {
            $color = $this->_font['color'];
        }

        if ($color == 'transparent') {
            return;
        }

        if (strpos($this->_font['file'], '.') === false) {
            $this->_font['file'] = IMAGE_CANVAS_SYSTEM_FONT_PATH . $this->_font['file'] . '.fdb';
        }

        $textColor   = $this->_color($color);
        $textOpacity = $this->_opacity($color);

        $lines = explode("\n", $text);
        foreach ($lines as $line) {

            $x = $x0;
            $y = $y0;

            $y0 += $textHeight + 2;

            $width = $this->textWidth($line);
            $height = $this->textHeight($line);

            if ($alignment['horizontal'] == 'right') {
                $x -= $width;
            } else if ($alignment['horizontal'] == 'center') {
                $x -= $width / 2;
            }

            $font = new SWFFont($this->_font['file']);
            $text = new SWFText();
            $text->setFont($font);
            $text->moveTo($x, $y + $this->_font['size']);
            $text->setColor($textColor[0], $textColor[1], $textColor[2], $textOpacity);
            $text->setHeight($this->_font['size']);
            $text->addString($line);
            $this->_canvas->add($text);
        }

        parent::addText($params);
    }

    /**
     * Overlay image
     *
     * Parameter array:
     * 'x'         : int X-point of overlayed image
     * 'y'         : int Y-point of overlayed image
     * 'filename'  : string The filename of the image to overlay
     * 'width'     : int [optional] The width of the overlayed image (resizing if possible)
     * 'height'    : int [optional] The height of the overlayed image (resizing if possible)
     * 'alignment' : array [optional] Alignment
     * 'url'       : string [optional] Target URL
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function image($params)
    {
        parent::image($params);
    }

    /**
     * Display the SWF
     *
     * @param array $param Parameter array
     *
     * @return void
     */
    function show($param = false)
    {
        parent::show($param);
        $this->_canvas->output();
    }

    /**
     * Save the SWF to a file
     *
     * @param array $param Parameter array
     *              array('filename'    => 'canvas.swf',
     *                    'compression' => 0)
     *
     *              The compression level can be a value between 0 and 9,
     *              defining the SWF compression similar to gzip compression.
     *              This parameter is only available as of Flash MX (6).
     *
     * @return void
     */
    function save($param = false)
    {
        if (!isset($param['compression'])) {
            $param['compression'] = 0;
        }

        parent::save($param);
        $this->_canvas->save($param['filename'], $param['compression']);
    }

    /**
     * Get SWF movie object
     *
     * @return object
     */
    function getData()
    {
        return $this->_canvas;
    }

    /**
     * Set clipping to occur
     *
     * Parameter array:
     *
     * 'x0' : int X point of Upper-left corner
     * 'y0' : int X point of Upper-left corner
     * 'x1' : int X point of lower-right corner
     * 'y1' : int Y point of lower-right corner
     *
     * @param array $params Parameter array (x0, y0, x1, y1)
     *
     * @todo
     * @return void
     */
    function setClipping($params = false)
    {
    }

    /**
     * Get an SWF specific HTML tag
     *
     * This method implicitly saves the canvas to the filename in the
     * filesystem path specified and parses it as URL specified by URL path
     *
     * Parameter array:
     * 'filename' : string
     * 'filepath' : string Path to the file on the file system. Remember the final slash
     * 'urlpath'  : string Path to the file available through an URL. Remember the final slash
     * 'width'    : int The width in pixels
     * 'height'   : int The height in pixels
     * 'quality'  : Flash quality
     * 'scale'    : Scale
     * 'menu'     : Whether to display the Flash menu on mouse right-click
     *
     * @param array $params Parameter array
     *
     * @return string HTML-output
     */
    function toHtml($params)
    {
        parent::toHtml($params);
        return '<object data="' . $params['urlpath'] . $params['filename'] . '" type="application/x-shockwave-flash" width="' . $params['width'] . '" height="' . $params['height'] . '">
                    <param name="movie" value="' . $params['urlpath'] . $params['filename'] . '">
                    <param name="quality" value="' . $params['quality'] . '">
                    <param name="scale" value="' . $params['scale'] . '">
                    <param name="menu" value="' . $params['menu'] . '">
                </object>';
    }
}
?>
