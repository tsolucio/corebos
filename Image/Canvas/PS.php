<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class for handling output in Postscript format.
 * 
 * Requires PHP extension pslib
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
 * Include file Image/Canvas.php
 */
require_once 'Image/Canvas.php';

/**
 * Include file Image/Canvas/Color.php
 */
require_once 'Image/Canvas/Color.php';

/**
 * PostScript Canvas class.
 * 
 * @category  Images
 * @package   Image_Canvas
 * @author    Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author    Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright 2003-2009 The PHP Group
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Image_Canvas
 */
class Image_Canvas_PS extends Image_Canvas
{

    /**
     * The PostScript document
     * @var resource
     * @access private
     */
    var $_ps;

    /**
     * The major version of pslib
     * @var int
     * @access private
     */
    var $_pslib;

    /**
     * The font
     * @var mixed
     * @access private
     */
    var $_psFont = false;

    /**
     * The width of the page
     * @var int
     * @access private
     */
    var $_pageWidth;

    /**
     * The height of the page
     * @var int
     * @access private
     */
    var $_pageHeight;

    /**
     * Create the PostScript canvas.
     *
     * Parameters available:
     *
     * 'page' Specify the page/paper format for the graph's page, available
     * formats are: A0, A1, A2, A3, A4, A5, A6, B5, letter, legal, ledger,
     * 11x17, cd_front, inlay, inlay_nosides
     *
     * 'align' Alignment of the graph on the page, available options are:
     * topleft, topcenter, topright, leftcenter, center, rightcenter,
     * leftbottom, centerbottom, rightbottom
     *
     * 'orientation' Specifies the paper orientation, default is 'portrait' and
     * 'landscape' is also supported.
     *
     * 'creator' The creator tag of the PostScript/graph
     *
     * 'author' The author tag of the PostScript/graph
     *
     * 'title' The title tag of the PostScript/graph
     *
     * 'width' The width of the graph on the page
     *
     * 'height' The height of the graph on the page
     *
     * 'left' The left offset of the graph on the page
     *
     * 'top' The top offset of the graph on the page
     *
     * 'filename' The PostScript file to open/add page to, using 'filename' requires
     * 'ps' An existing pslib PostScript document to add the page to
     *
     * 'add_page' (true/false) Used together with 'ps', to specify whether the
     * canvas should add a new graph page (true) or create the graph on the
     * current page (false), default is 'true'
     *
     * The 'page' and 'width' & 'height' can be mutually omitted, if 'page' is
     * omitted the page is created using dimensions of width x height, and if
     * width and height are omitted the page dimensions are used for the graph.
     *
     * If 'ps' is specified, 'filename', 'creator', 'author' and 'title' has no
     * effect.
     *
     * 'left' and 'top' are overridden by 'align'
     *
     * It is required either to specify 'width' & 'height' or 'page'.
     *
     * The PostScript format/pslib has some limitations on the capabilities,
     * which means some functionality available using other canvass (fx. alpha
     * blending and gradient fills) are not supported with PostScript
     * (see Canvas.txt in the docs/ folder for further details)
     *
     * @param array $param Parameter array
     */
    function Image_Canvas_PS($param)
    {
        if (isset($param['page'])) {
            switch (strtoupper($param['page'])) {
            case 'A0':
                $this->_pageWidth = 2380;
                $this->_pageHeight = 3368;
                break;

            case 'A1':
                $this->_pageWidth = 1684;
                $this->_pageHeight = 2380;
                break;

            case 'A2':
                $this->_pageWidth = 1190;
                $this->_pageHeight = 1684;
                break;

            case 'A3':
                $this->_pageWidth = 842;
                $this->_pageHeight = 1190;
                break;

            case 'A4':
                $this->_pageWidth = 595;
                $this->_pageHeight = 842;
                break;

            case 'A5':
                $this->_pageWidth = 421;
                $this->_pageHeight = 595;
                break;

            case 'A6':
                $this->_pageWidth = 297;
                $this->_pageHeight = 421;
                break;

            case 'B5':
                $this->_pageWidth = 501;
                $this->_pageHeight = 709;
                break;

            case 'LETTER':
                $this->_pageWidth = 612;
                $this->_pageHeight = 792;
                break;

            case 'LEGAL':
                $this->_pageWidth = 612;
                $this->_pageHeight = 1008;
                break;

            case 'LEDGER':
                $this->_pageWidth = 1224;
                $this->_pageHeight = 792;
                break;

            case '11X17':
                $this->_pageWidth = 792;
                $this->_pageHeight = 1224;
                break;

            case 'CD_FRONT':
                $this->_pageWidth = 337;
                $this->_pageHeight = 337;
                break;

            case 'INLAY':
                $this->_pageWidth = 425;
                $this->_pageHeight = 332;
                break;

            case 'INLAY_NOSIDES':
                $this->_pageWidth = 390;
                $this->_pageHeight = 332;
                break;
            }
        }

        $this->setDefaultFont(array('name' => 'Helvetica', 'color' => 'black', 'size' => 9));

        if ((isset($param['orientation'])) && (strtoupper($param['orientation']) == 'LANDSCAPE')) {
            $w = $this->_pageWidth;
            $this->_pageWidth = $this->_pageHeight;
            $this->_pageHeight = $w;
        }

        parent::Image_Canvas($param);

        if (!$this->_pageWidth) {
            $this->_pageWidth = $this->_width;
        } elseif (!$this->_width) {
            $this->_width = $this->_pageWidth;
        }

        if (!$this->_pageHeight) {
            $this->_pageHeight = $this->_height;
        } elseif (!$this->_height) {
            $this->_height = $this->_pageHeight;
        }

        $this->_width = min($this->_width, $this->_pageWidth);
        $this->_height = min($this->_height, $this->_pageHeight);

        if ((isset($param['align']))
            && (($this->_width != $this->_pageWidth) || ($this->_height != $this->_pageHeight))
        ) {
            switch (strtoupper($param['align'])) {
            case 'TOPLEFT':
                $this->_top = 0;
                $this->_left = 0;
                break;

            case 'TOPCENTER':
                $this->_top = 0;
                $this->_left = ($this->_pageWidth - $this->_width) / 2;
                break;

            case 'TOPRIGHT':
                $this->_top = 0;
                $this->_left = $this->_pageWidth - $this->_width;
                break;

            case 'LEFTCENTER':
                $this->_top = ($this->_pageHeight - $this->_height) / 2;
                $this->_left = 0;
                break;

            case 'CENTER':
                $this->_top = ($this->_pageHeight - $this->_height) / 2;
                $this->_left = ($this->_pageWidth - $this->_width) / 2;
                break;

            case 'RIGHTCENTER':
                $this->_top = ($this->_pageHeight - $this->_height) / 2;
                $this->_left = $this->_pageWidth - $this->_width;
                break;

            case 'LEFTBOTTOM':
                $this->_top = $this->_pageHeight - $this->_height;
                $this->_left = 0;
                break;

            case 'CENTERBOTTOM':
                $this->_top = $this->_pageHeight - $this->_height;
                $this->_left = ($this->_pageWidth - $this->_width) / 2;
                break;

            case 'RIGHTBOTTOM':
                $this->_top = $this->_pageHeight - $this->_height;
                $this->_left = $this->_pageWidth - $this->_width;
                break;
            }
        }

        $addPage = true;
        if ((isset($param['ps'])) && (is_resource($param['ps']))) {
            $this->_ps =& $param['ps'];
            if ((isset($param['add_page'])) && ($param['add_page'] === false)) {
                $addPage = false;
            }
        } else {
            $this->_ps = ps_new();

            if (isset($param['filename'])) {
                ps_open_file($this->_ps, $param['filename']);
            } else {
                ps_open_file($this->_ps);
            }

            ps_set_parameter($this->_ps, 'warning', 'true');

            ps_set_info($this->_ps, 'Creator', (isset($param['creator']) ? $param['creator'] : 'PEAR::Image_Canvas'));
            ps_set_info($this->_ps, 'Author', (isset($param['author']) ? $param['author'] : 'Jesper Veggerby'));
            ps_set_info($this->_ps, 'Title', (isset($param['title']) ? $param['title'] : 'Image_Canvas'));
        }

        if ($addPage) {
            ps_begin_page($this->_ps, $this->_pageWidth, $this->_pageHeight);
        }
        $this->_reset();

        $this->_pslib = $this->_version();
    }

    /**
     * Get the x-point from the relative to absolute coordinates
     *
     * @param float $x The relative x-coordinate (in percentage of total width)
     *
     * @return float The x-coordinate as applied to the canvas
     * @access private
     */
    function _getX($x)
    {
        return $this->_left + $x;
    }

    /**
     * Get the y-point from the relative to absolute coordinates
     *
     * @param float $y The relative y-coordinate (in percentage of total width)
     *
     * @return float The y-coordinate as applied to the canvas
     * @access private
     */
    function _getY($y)
    {
        return $this->_pageHeight - ($this->_top + $y);
    }

    /**
     * Get the color index for the RGB color
     *
     * @param int $color The color
     *
     * @return int The GD image index of the color
     * @access private
     */
    function _color($color = false)
    {
        if (($color === false) || ($color === 'opague') || ($color === 'transparent')) {
            return false;
        } else {
            $color = Image_Canvas_Color::color2RGB($color);
            $color[0] = $color[0]/255;
            $color[1] = $color[1]/255;
            $color[2] = $color[2]/255;
            return $color;
        }
    }

    /**
     * Get the PostScript linestyle
     *
     * @param mixed $lineStyle The line style to return, false if the one
     *   explicitly set
     *
     * @return bool True if set (so that a line should be drawn)
     * @access private
     */
    function _setLineStyle($lineStyle = false)
    {
        if ($lineStyle === false) {
            $lineStyle = $this->_lineStyle;
        }

        if (($lineStyle == 'transparent') || ($lineStyle === false)) {
            return false;
        }
        
        if (is_array($lineStyle)) {
            // TODO Implement linestyles in pslib (using ps_setcolor(.., 'pattern'...); ?
            reset($lineStyle);
            $lineStyle = current($lineStyle);
        } 

        $color = $this->_color($lineStyle);

        ps_setlinewidth($this->_ps, $this->_thickness);
        ps_setcolor($this->_ps, 'stroke', 'rgb', $color[0], $color[1], $color[2], 0);
        return true;
    }

    /**
     * Set the PostScript fill style
     *
     * @param mixed $fillStyle The fillstyle to return, false if the one
     *   explicitly set
     *
     * @return bool True if set (so that a line should be drawn)
     * @access private
     */
    function _setFillStyle($fillStyle = false)
    {
        if ($fillStyle === false) {
            $fillStyle = $this->_fillStyle;
        }

        if (($fillStyle == 'transparent') || ($fillStyle === false)) {
            return false;
        }

        $color = $this->_color($fillStyle);

        ps_setcolor($this->_ps, 'fill', 'rgb', $color[0], $color[1], $color[2], 0);
        return true;
    }

    /**
     * Set the PostScript font
     *
     * @return void
     * @access private
     */
    function _setFont()
    {
        $this->_psFont = false;
        if (isset($this->_font['name'])) {
            ps_set_parameter($this->_ps, 'FontOutline', $this->_font['name'] . '=' . $this->_font['file']);
            $this->_psFont = ps_findfont($this->_ps, $this->_font['name'], $this->_font['encoding'], $this->_font['embed']);

            if ($this->_psFont) {
                ps_setfont($this->_ps, $this->_psFont, $this->_font['size']);
                $this->_setFillStyle($this->_font['color']);
            }
        } else {
            $this->_setFillStyle('black');
        }
    }

    /**
     * Sets an image that should be used for filling.
     *
     * Image filling is not supported with PostScript, filling 'transparent'
     *
     * @param string $filename The filename of the image to fill with
     *
     * @return void
     */
    function setFillImage($filename)
    {
        $this->_fillStyle = 'transparent';
    }

    /**
     * Sets a gradient fill
     *
     * Gradient filling is not supported with PostScript, end color used as solid fill.
     *
     * @param array $gradient Gradient fill options
     *
     * @return void
     */
    function setGradientFill($gradient)
    {
        $this->_fillStyle = $gradient['end'];
    }

    /**
     * Sets the font options.
     *
     * The $font array may have the following entries:
     *
     * 'ttf' = the .ttf file (either the basename, filename or full path)
     * If 'ttf' is specified, then the following can be specified
     *
     * 'size' = size in pixels
     *
     * 'angle' = the angle with which to write the text
     *
     * @param array $fontOptions The font options.
     *
     * @return void
     */
    function setFont($fontOptions)
    {
        parent::setFont($fontOptions);

        if (!isset($this->_font['size'])) {
            $this->_font['size'] = 12;
        }

        if (!isset($this->_font['encoding'])) {
            $this->_font['encoding'] = null;
        }

        if ($this->_font['name'] == 'Helvetica') {
            $this->_font['embed'] = 0;
        }

        if (!isset($this->_font['color'])) {
            $this->_font['color'] = 'black';
        }
    }

    /**
     * Resets the canvas.
     *
     * Includes fillstyle, linestyle, thickness and polygon
     *
     * @return void
     * @access private
     */
    function _reset()
    {
        // ps_initgraphics($this->_ps);
        parent::_reset();
    }

    /**
     * Draw a line
     *
     * Parameter array:
     * 'x0': int X start point
     * 'y0': int Y start point
     * 'x1': int X end point
     * 'y1': int Y end point
     * 'color': mixed [optional] The line color
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function line($params)
    {
        $color = (isset($params['color']) ? $params['color'] : false);
        if ($this->_setLineStyle($color)) {
            ps_moveto($this->_ps, $this->_getX($params['x0']), $this->_getY($params['y0']));
            ps_lineto($this->_ps, $this->_getX($params['x1']), $this->_getY($params['y1']));
            ps_stroke($this->_ps);
        }
        parent::line($params);
    }

    /**
     * Parameter array:
     * 'connect': bool [optional] Specifies whether the start point should be
     *   connected to the endpoint (closed polygon) or not (connected line)
     * 'fill': mixed [optional] The fill color
     * 'line': mixed [optional] The line color
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function polygon($params = array())
    {
        $connectEnds = (isset($params['connect']) ? $params['connect'] : false);
        $fillColor = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor = (isset($params['line']) ? $params['line'] : false);

        $line = $this->_setLineStyle($lineColor);
        $fill = false;
        if ($connectEnds) {
            $fill = $this->_setFillStyle($fillColor);
        }

        $first = true;
        foreach ($this->_polygon as $point) {
            if ($first === true) {
                ps_moveto($this->_ps, $point['X'], $point['Y']);
                $first = $point;
            } else {
                if (isset($last['P1X'])) {
                    ps_curveto(
                        $this->_ps,
                        $last['P1X'],
                        $last['P1Y'],
                        $last['P2X'],
                        $last['P2Y'],
                        $point['X'],
                        $point['Y']
                    );
                } else {
                    ps_lineto(
                        $this->_ps,
                        $point['X'],
                        $point['Y']
                    );
                }
            }
            $last = $point;
        }

        if ($connectEnds) {
            if (isset($last['P1X'])) {
                ps_curveto(
                    $this->_ps,
                    $last['P1X'],
                    $last['P1Y'],
                    $last['P2X'],
                    $last['P2Y'],
                    $first['X'],
                    $first['Y']
                );
            } else {
                ps_lineto(
                    $this->_ps,
                    $first['X'],
                    $first['Y']
                );
            }
        }

        if (($line) && ($fill)) {
            ps_fill_stroke($this->_ps);
        } elseif ($line) {
            ps_stroke($this->_ps);
        } elseif ($fill) {
            ps_fill($this->_ps);
        }
        parent::polygon($params);
    }

    /**
     * Draw a rectangle
     *
     * Parameter array:
     * 'x0': int X start point
     * 'y0': int Y start point
     * 'x1': int X end point
     * 'y1': int Y end point
     * 'fill': mixed [optional] The fill color
     * 'line': mixed [optional] The line color
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function rectangle($params)
    {
        $x0 = $this->_getX($params['x0']);
        $y0 = $this->_getY($params['y0']);
        $x1 = $this->_getX($params['x1']);
        $y1 = $this->_getY($params['y1']);
        $fillColor = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor = (isset($params['line']) ? $params['line'] : false);

        $line = $this->_setLineStyle($lineColor);
        $fill = $this->_setFillStyle($fillColor);
        if (($line) || ($fill)) {
            ps_rect($this->_ps, min($x0, $x1), min($y0, $y1), abs($x1 - $x0), abs($y1 - $y0));
            if (($line) && ($fill)) {
                ps_fill_stroke($this->_ps);
            } elseif ($line) {
                ps_stroke($this->_ps);
            } elseif ($fill) {
                ps_fill($this->_ps);
            }
        }
        parent::rectangle($params);
    }

    /**
     * Draw an ellipse
     *
     * Parameter array:
     * 'x': int X center point
     * 'y': int Y center point
     * 'rx': int X radius
     * 'ry': int Y radius
     * 'fill': mixed [optional] The fill color
     * 'line': mixed [optional] The line color
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function ellipse($params)
    {
        $x = $params['x'];
        $y = $params['y'];
        $rx = $params['rx'];
        $ry = $params['ry'];
        $fillColor = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor = (isset($params['line']) ? $params['line'] : false);

        $line = $this->_setLineStyle($lineColor);
        $fill = $this->_setFillStyle($fillColor);
        if (($line) || ($fill)) {
            if ($rx == $ry) {
                ps_circle($this->_ps, $this->_getX($x), $this->_getY($y), $rx);
            } else {
                ps_moveto($this->_ps, $this->_getX($x - $rx), $this->_getY($y));
                ps_curveto(
                    $this->_ps,
                    $this->_getX($x - $rx), $this->_getY($y),
                    $this->_getX($x - $rx), $this->_getY($y - $ry),
                    $this->_getX($x), $this->_getY($y - $ry)
                );
                ps_curveto(
                    $this->_ps,
                    $this->_getX($x), $this->_getY($y - $ry),
                    $this->_getX($x + $rx), $this->_getY($y - $ry),
                    $this->_getX($x + $rx), $this->_getY($y)
                );
                ps_curveto(
                    $this->_ps,
                    $this->_getX($x + $rx), $this->_getY($y),
                    $this->_getX($x + $rx), $this->_getY($y + $ry),
                    $this->_getX($x), $this->_getY($y + $ry)
                );
                ps_curveto(
                    $this->_ps,
                    $this->_getX($x), $this->_getY($y + $ry),
                    $this->_getX($x - $rx), $this->_getY($y + $ry),
                    $this->_getX($x - $rx), $this->_getY($y)
                );
            }

            if (($line) && ($fill)) {
                ps_fill_stroke($this->_ps);
            } elseif ($line) {
                ps_stroke($this->_ps);
            } elseif ($fill) {
                ps_fill($this->_ps);
            }
        }
        parent::ellipse($params);
    }

    /**
     * Draw a pie slice
     *
     * Parameter array:
     * 'x': int X center point
     * 'y': int Y center point
     * 'rx': int X radius
     * 'ry': int Y radius
     * 'v1': int The starting angle (in degrees)
     * 'v2': int The end angle (in degrees)
     * 'srx': int [optional] Starting X-radius of the pie slice (i.e. for a doughnut)
     * 'sry': int [optional] Starting Y-radius of the pie slice (i.e. for a doughnut)
     * 'fill': mixed [optional] The fill color
     * 'line': mixed [optional] The line color
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function pieslice($params)
    {
        $x = $this->_getX($params['x']);
        $y = $this->_getY($params['y']);
        $rx = $this->_getX($params['rx']);
        $ry = $this->_getY($params['ry']);
        $v1 = $this->_getX($params['v1']);
        $v2 = $this->_getY($params['v2']);
        $srx = $this->_getX($params['srx']);
        $sry = $this->_getY($params['sry']);
        $fillColor = (isset($params['fill']) ? $params['fill'] : false);
        $lineColor = (isset($params['line']) ? $params['line'] : false);

        // TODO Implement pslib::pieSlice()
        parent::pieslice($params);
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
        if ($this->_psFont === false) {
             return $this->_font['size'] * 0.7 * strlen($text);
        } else {
            return ps_stringwidth($this->_ps, $text, $this->_psFont, $this->_font['size']);
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
        if (isset($this->_font['size'])) {
            return $this->_font['size'];
        } else {
            return 12;
        }
    }

    /**
     * Writes text
     *
     * Parameter array:
     * 'x': int X-point of text
     * 'y': int Y-point of text
     * 'text': string The text to add
     * 'alignment': array [optional] Alignment
     * 'color': mixed [optional] The color of the text
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function addText($params)
    {
        $x = $this->_getX($params['x']);
        $y = $this->_getY($params['y']);
        $text = $params['text'];
        $color = (isset($params['color']) ? $params['color'] : false);
        $alignment = (isset($params['alignment']) ? $params['alignment'] : false);

        $this->_setFont();

        $textWidth = $this->textWidth($text);
        $textHeight = $this->textHeight($text);

        if (!is_array($alignment)) {
            $alignment = array('vertical' => 'top', 'horizontal' => 'left');
        }
        
        if (!isset($alignment['vertical'])) {
            $alignment['vertical'] = 'top';
        }
        
        if (!isset($alignment['horizontal'])) {
            $alignment['horizontal'] = 'left';
        }

        if ($alignment['horizontal'] == 'right') {
            $x = $x - $textWidth;
        } elseif ($alignment['horizontal'] == 'center') {
            $x = $x - ($textWidth / 2);
        }

        $y -= $textHeight;

        if ($alignment['vertical'] == 'bottom') {
            $y = $y + $textHeight;
        } elseif ($alignment['vertical'] == 'center') {
            $y = $y + ($textHeight / 2);
        }

        if (($color === false) && (isset($this->_font['color']))) {
            $color = $this->_font['color'];
        }

        ps_show_xy($this->_ps, $text, $x, $y);

        parent::addText($params);
    }

    /**
     * Overlay image
     *
     * Parameter array:
     * 'x': int X-point of overlayed image
     * 'y': int Y-point of overlayed image
     * 'filename': string The filename of the image to overlay
     * 'width': int [optional] The width of the overlayed image (resizing if possible)
     * 'height': int [optional] The height of the overlayed image (resizing if possible)
     * 'alignment': array [optional] Alignment
     *
     * @param array $params Parameter array
     *
     * @return void
     */
    function image($params)
    {
        $x = $this->_getX($params['x']);
        $y = $this->_getY($params['y']);
        $filename = $params['filename'];
        $width = (isset($params['width']) ? $params['width'] : false);
        $height = (isset($params['height']) ? $params['height'] : false);
        $alignment = (isset($params['alignment']) ? $params['alignment'] : false);

        if (substr($filename, -4) == '.png') {
            $type = 'png';
        } elseif (substr($filename, -4) == '.jpg') {
            $type = 'jpeg';
        }

        $image = ps_open_image_file($this->_ps, $type, realpath($filename), '');
        $width_ = ps_get_value($this->_ps, 'imagewidth', $image);
        $height_ = ps_get_value($this->_ps, 'imageheight', $image);

        $outputWidth = ($width !== false ? $width : $width_);
        $outputHeight = ($height !== false ? $height : $height_);

        if (!is_array($alignment)) {
            $alignment = array('vertical' => 'top', 'horizontal' => 'left');
        }
        
        if (!isset($alignment['vertical'])) {
            $alignment['vertical'] = 'top';
        }
        
        if (!isset($alignment['horizontal'])) {
            $alignment['horizontal'] = 'left';
        }

        if ($alignment['horizontal'] == 'right') {
            $x -= $outputWidth;
        } elseif ($alignment['horizontal'] == 'center') {
            $x -= $outputWidth / 2;
        }

        if ($alignment['vertical'] == 'top') {
            $y += $outputHeight;
        } elseif ($alignment['vertical'] == 'center') {
            $y += $outputHeight / 2;
        }
        
        if (($width === false) && ($height === false)) {
            $scale = 1;
        } else {
            $scale = max(($height/$height_), ($width/$width_));
        }   

        ps_place_image($this->_ps, $image, $x, $y, $scale);
        ps_close_image($this->_ps, $image);
        
        parent::image($params);
    }

    /**
     * Output the result of the canvas
     *
     * @param array $param Parameter array
     *
     * @return void
     * @abstract
     */
    function show($param = false)
    {
        parent::show($param);
        ps_end_page($this->_ps);
        ps_close($this->_ps);

        $buf = ps_get_buffer($this->_ps);
        $len = strlen($buf);

        header('Content-type: application/postscript');
        header('Content-Length: ' . $len);
        header('Content-Disposition: inline; filename=image_graph.ps');
        print $buf;

        ps_delete($this->_ps);
    }

    /**
     * Output the result of the canvas
     *
     * @param array $param Parameter array
     *
     * @return void
     * @abstract
     */
    function save($param = false)
    {
        parent::save($param);
        ps_end_page($this->_ps);
        ps_close($this->_ps);

        if ($param['filename'] == "") {
            $buf = ps_get_buffer($this->_ps);
            $len = strlen($buf);

            $fp = @fopen($param['filename'], 'wb');
            if ($fp) {
                fwrite($fp, $buf, strlen($buf));
                fclose($fp);
            }
        }

        ps_delete($this->_ps);
    }
    
    /**
     * Get a canvas specific HTML tag.
     * 
     * This method implicitly saves the canvas to the filename in the 
     * filesystem path specified and parses it as URL specified by URL path
     * 
     * Parameter array:
     * 'filename': string
     * 'filepath': string Path to the file on the file system. Remember the final slash
     * 'urlpath': string Path to the file available through an URL. Remember the final slash
     * 'title': string The url title
     *
     * @param array $params Parameter array
     *
     * @return string HTML code
     */
    function toHtml($params)
    {
        parent::toHtml($params);
        return '<a href="' . $params['urlpath'] . $params['filename'] . '">' . $params['title'] . '</a>';        
    }    

    /**
     * Check which major version of pslib is installed
     *
     * @return int The mahor version number of pslib
     * @access private
     */
    function _version()
    {
        $result = false;
        $version = '';
        if (function_exists('ps_get_majorversion')) {
            $version = ps_get_majorversion();
        } else if (function_exists('ps_get_value')) {
            $version = ps_get_value($this->_ps, 'major', 0);                
        } else {
            ob_start();
            phpinfo(8);
            $php_info = ob_get_contents();
            ob_end_clean();

            if (preg_match("/<td[^>]*>pslib Version *<\/td><td[^>]*>([^<]*)<\/td>/", $php_info, $result)
            ) {
                $version = $result[1];
            }
        }               
        
        if (preg_match('/([0-9]{1,2})\.[0-9]{1,2}(\.[0-9]{1,2})?/', trim($version), $result)) {
            return $result[1];
        } else {
            return $version;
        }
    }

}

?>
