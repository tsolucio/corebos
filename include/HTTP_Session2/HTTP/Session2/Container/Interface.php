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
 * PHP Version 5
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivaniovich <info@wwwlab.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  CVS: $Id: Interface.php 267742 2008-10-25 17:01:14Z till $
 * @link     http://pear.php.net/package/HTTP_Session2
 */

/**
 * Container class for storing session data data
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivaniovich <info@wwwlab.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/HTTP_Session2
 */
interface HTTP_Session2_Container_Interface
{
    /**
     * open
     *
     * @param string $save_path    Path to save sessions in.
     * @param string $session_name Name of the session.
     *
     * @return void
     */
    public function open($save_path, $session_name);

    /**
     * close
     *
     * @return void
     */
    public function close();

    /**
     * read
     *
     * @param string $id The session ID.
     *
     * @return void
     */
    public function read($id);

    /**
     * write
     *
     * @param string $id   The session ID.
     * @param string $data The data to save/write.
     *
     * @return void
     */
    public function write($id, $data);

    /**
     * destroy
     *
     * @param string $id The session ID.
     *
     * @return void
     */
    public function destroy($id);

    /**
     * gc
     *
     * @param int $maxlifetime The session's maximum lifetime.
     *
     * @return void
     */
    public function gc($maxlifetime);

    /**
     * Replicate session data to specified target
     *
     * @param string $target Target to replicate to
     * @param string $id     Id of record to replicate,
     *                       if not specified current session id will be used
     *
     * @return boolean
     */
    public function replicate($target, $id = null);
}
