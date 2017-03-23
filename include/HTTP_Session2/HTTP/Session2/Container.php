<?php
/**
 * +-----------------------------------------------------------------------+
 * | Copyright (c) 2004, Tony Bibbs                                        |
 * | All rights reserved.                                                  |
 * |                                                                       |
 * | Redistribution and use in source and binary forms, with or without    |
 * | modification, are permitted provided that the following conditions    |
 * | are met:                                                              |
 * |                                                                       |
 * | o Redistributions of source code must retain the above copyright      |
 * |   notice, this list of conditions and the following disclaimer.       |
 * | o Redistributions in binary form must reproduce the above copyright   |
 * |   notice, this list of conditions and the following disclaimer in the |
 * |   documentation and/or other materials provided with the distribution.|
 * | o The names of the authors may not be used to endorse or promote      |
 * |   products derived from this software without specific prior written  |
 * |   permission.                                                         |
 * |                                                                       |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
 * |                                                                       |
 * +-----------------------------------------------------------------------+
 * | Author: Alexander Radivanovich <info@wwwlab.net>                      |
 * |         Tony Bibbs <tony@geeklog.net>                                 |
 * +-----------------------------------------------------------------------+
 *
 * PHP version 5
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivaniovich <info@wwwlab.net>
 * @author   Tony Bibbs <tony@geeklog.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  CVS: $Id: Container.php 295734 2010-03-02 13:25:15Z till $
 * @link     http://pear.php.net/package/HTTP_Session2
 */

/**
 * HTTP_Session2_Container_Interface
 */
require_once 'HTTP/Session2/Container/Interface.php';

/**
 * Container class for storing session data data
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivaniovich <info@wwwlab.net>
 * @author   Tony Bibbs <tony@geeklog.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/HTTP_Session2
 */
abstract class HTTP_Session2_Container implements HTTP_Session2_Container_Interface
{
    /**
     * Additional options for the container object
     *
     * @var array
     */
    protected $options = array();

    /**
     * Constrtuctor method
     *
     * @param array $options Additional options for the container object
     *
     * @return void
     */
    public function __construct($options = null)
    {
        $this->setDefaults();
        if (is_array($options)) {
            $this->parseOptions($options);
        }
    }

    /**
     * Call session_write_close() in destructor for compatibility with PHP >= 5.0.5
     *
     * @return void
     */
    public function __destruct()
    {
        session_write_close();
    }

    /**
     * Set some default options
     *
     * @return void
     */
    protected function setDefaults()
    {
    }

    /**
     * Parse options passed to the container class
     *
     * @param array $options Options
     *
     * @return void
     */
    protected function parseOptions($options)
    {
        foreach ($options as $option => $value) {
            if (in_array($option, array_keys($this->options))) {
                $this->options[$option] = $value;
            }
        }
    }

    /**
     * Set session save handler
     *
     * @return void
     */
    public function set()
    {
        session_module_name('user');
        session_set_save_handler(array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc'));
    }
}