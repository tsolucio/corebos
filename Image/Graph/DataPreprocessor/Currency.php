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
 * @subpackage DataPreprocessor
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    SVN: $Id: Currency.php 291170 2009-11-23 03:50:22Z neufeind $
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/DataPreprocessor/Formatted.php
 */
require_once 'Image/Graph/DataPreprocessor/Formatted.php';

/**
 * Format data as a currency.
 *
 * Uses the {@link Image_Graph_DataPreprocessor_Formatted} to represent the
 * values as a currency, i.e. 10 => � 10.00
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage DataPreprocessor
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @copyright  2003-2009 The PHP Group
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.8.0
 * @link       http://pear.php.net/package/Image_Graph
 */
class Image_Graph_DataPreprocessor_Currency extends Image_Graph_DataPreprocessor_Formatted
{

    /**
     * Image_Graph_CurrencyData [Constructor].
     *
     * @param string $currencySymbol The symbol representing the currency
     */
    function Image_Graph_DataPreprocessor_Currency($currencySymbol)
    {
        parent::__construct($currencySymbol.' %0.2f');
    }

}

?>
