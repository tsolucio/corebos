<?php
/**
 * HTTP_Session2_Exception
 *
 * Base exception class for HTTP_Session2
 *
 * Copyright (c) 2007, Till Klampaeckel
 *
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *  * Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  * Neither the name of PEAR nor the names of its contributors may be used to
 *    endorse or promote products derived from this software without specific prior
 *    written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP version 5
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  CVS: $Id: Exception.php 266531 2008-09-19 13:12:58Z till $
 * @link     http://pear.php.net/package/HTTP_Session2
 */

/**
 * PEAR/Exception.php
 * @ignore
 */
require_once 'PEAR/Exception.php';

/**
 * HTTP_Session2_Exception
 *
 * PHP version 5
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/HTTP_Session2
 */
class HTTP_Session2_Exception extends PEAR_Exception
{
    /**
     * @var array $exceptionStack To add an exception, when we re-throw.
     */
    protected $exceptionStack = array();

    /**
     * __construct
     *
     * @param string $message   An error message.
     * @param mixed  $code      An error code.
     * @param mixed  $exception The previous exception, when we re-throw [optional]
     *
     * @uses parent::__construct()
     * @uses self::$exceptionStack
     */
    public function __construct($message, $code = null, $exception = null)
    {
        if ($exception !== null) {
            array_push($this->exceptionStack, $exception);
        }
        parent::__construct($message, $code);
    }

    /**
     * __toString() implementation for lazy coders like me :)
     *
     * @return string
     * @uses   parent::$message
     * @uses   parent::$code
     */
    public function __toString()
    {
        return "{$this->message} Code: {$this->code}"; 
    }

    /**
     * Return all stacked exceptions
     *
     * @return array
     * @uses   self::$exceptionStack
     */
    public function getExceptionStack()
    {
        return $this->exceptionStack;
    }
}
