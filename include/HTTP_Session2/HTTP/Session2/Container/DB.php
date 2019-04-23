<?php
/**
 * +-----------------------------------------------------------------------+
 * | Copyright (c) 2002, Alexander Radivanovich                            |
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
 * +-----------------------------------------------------------------------+
 *
 * PHP Version 5
 *
 * @category   HTTP
 * @package    HTTP_Session2
 * @author     Alexander Radivanovich <info@wwwlab.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    CVS: $Id: DB.php 266493 2008-09-18 16:58:29Z till $
 * @link       http://pear.php.net/package/HTTP_Session2
 * @deprecated This driver/container is deprecated from 0.9.0
 */

/**
 * HTTP/Session2/Container.php
 * @ignore
 */
require_once 'HTTP/Session2/Container.php';

/**
 * HTTP/Session2/Exception.php
 * 
 * @todo Implement HTTP_Session2_Containter_DB_Exception
 */
require_once 'HTTP/Session2/Exception.php';

/**
 * DB.php
 * @ignore
 */
require_once 'DB.php';

/**
 * Database container for session data
 *
 * Create the following table to store session data
 * <code>
 * CREATE TABLE `sessiondata` (
 *     `id` CHAR(32) NOT NULL,
 *     `expiry` INT UNSIGNED NOT NULL DEFAULT 0,
 *     `data` TEXT NOT NULL,
 *     PRIMARY KEY (`id`)
 * );
 * </code>
 *
 * @category   HTTP
 * @package    HTTP_Session2
 * @author     Alexander Radivanovich <info@wwwlab.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTTP_Session2
 * @deprecated This driver/container is deprecated from 0.9.0
 */
class HTTP_Session2_Container_DB extends HTTP_Session2_Container
{

    /**
     * DB connection object
     *
     * @var object DB
     */
    private $_db = null;

    /**
     * Session data cache id
     *
     * @var mixed
     */
    private $_crc = false;

    /**
     * Constrtuctor method
     *
     * $options is an array with the options.<br>
     * The options are:
     * <ul>
     * <li>'dsn' - The DSN string</li>
     * <li>'table' - Table with session data, default is 'sessiondata'</li>
     * <li>'autooptimize' - Boolean, 'true' to optimize
     * the table on garbage collection, default is 'false'.</li>
     * </ul>
     *
     * @param array $options The options
     *
     * @return void
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * Connect to database by using the given DSN string
     *
     * @param string $dsn DSN string
     *
     * @return boolean
     * @throws HTTP_Session2_Exception An exception?!
     */
    protected function connect($dsn)
    {
        if (is_string($dsn)) {
            $this->_db = DB::connect($dsn);
        } else if (is_object($dsn) && is_a($dsn, 'db_common')) {
            $this->_db = $dsn;
        } else if (DB::isError($dsn)) {
            throw new HTTP_Session2_Exception($dsn->getMessage(), $dsn->getCode());
        } else {
            $msg  = "The given dsn was not valid in file ";
            $msg .= __FILE__ . " at line " . __LINE__;
            throw new HTTP_Session2_Exception($msg,
                HTTP_Session2::ERR_SYSTEM_PRECONDITION);
        }
        return true;
    }

    /**
     * Set some default options
     *
     * @return void
     */
    protected function setDefaults()
    {
        $this->options['dsn']          = null;
        $this->options['table']        = 'sessiondata';
        $this->options['autooptimize'] = false;
    }

    /**
     * Establish connection to a database
     *
     * @param string $save_path    The path to save/write sessions.
     * @param string $session_name The session name.
     *
     * @return boolean
     * @uses   self::connect();
     * @uses   self::$options
     */
    public function open($save_path, $session_name)
    {
        return $this->connect($this->options['dsn']);
    }

    /**
     * Free resources
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id The Id!
     *
     * @return mixed
     * @throws HTTP_Session2_Exception An exception!?
     */
    public function read($id)
    {
        $query = sprintf("SELECT data FROM %s WHERE id = %s AND expiry >= %d",
            $this->options['table'],
            $this->_db->quote(md5($id)),
            time());

        $result = $this->_db->getOne($query);
        if (DB::isError($result)) {
            throw new HTTP_Session2_Exception($result->getMessage(),
                $result->getCode());
        }
        $this->_crc = strlen($result) . crc32($result);
        return $result;
    }

    /**
     * Write session data
     *
     * @param string $id   The id.
     * @param string $data The data.
     *
     * @return boolean
     * @todo   Remove sprintf(), they are expensive.
     */
    public function write($id, $data)
    {
        if ((false !== $this->_crc)
            && ($this->_crc === strlen($data) . crc32($data))) {
            /* $_SESSION hasn't been touched, no need to update the blob column */
            $query = "UPDATE %s SET expiry = %d WHERE id = %s AND expiry >= %d";
            $query = sprintf($query,
                $this->options['table'],
                time() + ini_get('session.gc_maxlifetime'),
                $this->_db->quote(md5($id)),
                time());
        } else {
            /* Check if table row already exists */
            $query = sprintf("SELECT COUNT(id) FROM %s WHERE id = '%s'",
                $this->options['table'],
                md5($id));

            $result = $this->_db->getOne($query);
            if (DB::isError($result)) {
                new DB_Error($result->code, PEAR_ERROR_DIE);
                return false;
            }
            if (0 == intval($result)) {
                /* Insert new row into table */
                $query = "INSERT INTO %s (id, expiry, data) VALUES (%s, %d, %s)";
                $query = sprintf($query,
                    $this->options['table'],
                    $this->_db->quote(md5($id)),
                    time() + ini_get('session.gc_maxlifetime'),
                    $this->_db->quote($data));
            } else {
                /* Update existing row */
                $query  = "UPDATE %s SET expiry = %d, data = %s";
                $query .= " WHERE id = %s AND expiry >= %d";
                $query  = sprintf($query,
                    $this->options['table'],
                    time() + ini_get('session.gc_maxlifetime'),
                    $this->_db->quote($data),
                    $this->_db->quote(md5($id)),
                    time());
            }
        }
        $result = $this->_db->query($query);
        if (DB::isError($result)) {
            new DB_Error($result->code, PEAR_ERROR_DIE);
            return false;
        }
        return true;
    }

    /**
     * Destroy session data
     *
     * @param string $id The id.
     *
     * @return boolean
     */
    public function destroy($id)
    {
        $query = sprintf("DELETE FROM %s WHERE id = %s",
            $this->options['table'],
            $this->_db->quote(md5($id)));

        $result = $this->_db->query($query);
        if (DB::isError($result)) {
            new DB_Error($result->code, PEAR_ERROR_DIE);
            return false;
        }
        return true;
    }

    /**
     * Garbage collection
     *
     * @param int $maxlifetime The session's maximum lifetime.
     *
     * @return boolean
     * @todo   Find out why the DB is not used for garbage collection.
     */
    public function gc($maxlifetime)
    {
        $query = sprintf("DELETE FROM %s WHERE expiry < %d",
            $this->options['table'],
            time());

        $result = $this->_db->query($query);
        if (DB::isError($result)) {
            new DB_Error($result->code, PEAR_ERROR_DIE);
            return false;
        }

        if ($this->options['autooptimize']) {
            switch($this->_db->type) {
            case 'mysql':
                $query = sprintf("OPTIMIZE TABLE %s", $this->options['table']);
                break;
            case 'pgsql':
                $query = sprintf("VACUUM %s", $this->options['table']);
                break;
            default:
                $query = null;
                break;
            }
            if (isset($query)) {
                $result = $this->_db->query($query);
                if (DB::isError($result)) {
                    new DB_Error($result->code, PEAR_ERROR_DIE);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Replicate session data to specified target
     *
     * @param string $target Target to replicate to
     * @param string $id     Id of record to replicate,
     *                       if not specified current session id will be used
     *
     * @return boolean
     */
    public function replicate($target, $id = null)
    {
        if (is_null($id)) {
            $id = HTTP_Session2::id();
        }

        // Check if table row already exists
        $query  = sprintf("SELECT COUNT(id) FROM %s WHERE id = %s",
            $target,
            $this->_db->quoteSmart(md5($id)));
        $result = $this->_db->getOne($query);
        if (DB::isError($result)) {
            new DB_Error($result->code, PEAR_ERROR_DIE);
            return false;
        }

        // Insert new row into target table
        if (0 == intval($result)) {
            $query  = "INSERT INTO $target SELECT * FROM";
            $query .= " " . $this->options['table'];
            $query .= " WHERE id = " . $this->_db->quoteSmart(md5($id));
        } else {
            // Update existing row
            $query  = "UPDATE $target dst,";
            $query .= " " . $this->options['table'];
            $query .= " src SET dst.expiry = src.expiry,";
            $query .= " dst.data = src.data";
            $query .= " WHERE dst.id = src.id";
            $query .= " AND src.id = " . $this->_db->quoteSmart(md5($id));
        }

        $result = $this->_db->query($query);
        if (DB::isError($result)) {
            new DB_Error($result->code, PEAR_ERROR_DIE);
            return false;
        }

        return true;
    }
}
