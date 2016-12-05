<?php
/**
 * HTTP_Session2_Container_MDB2
 *
 * PHP Version 5
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Till Klampaeckel <till@php.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  CVS: $Id: MDB2.php 267741 2008-10-25 17:00:06Z till $
 * @link     http://pear.php.net/package/HTTP_Session2
 */

/**
 * HTTP/Session2/Container.php
 * @ignore
 */
require_once 'HTTP/Session2/Container.php';

/**
 * HTTP/Session2/Exception.php
 */
require_once 'HTTP/Session2/Exception.php';

/**
 * MDB2.php
 * @ignore
 */
require_once 'MDB2.php';

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
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivanovich <info@wwwlab.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/HTTP_Session2
 * @uses     MDB2
 * @uses     MDB2_Driver_*
 */
class HTTP_Session2_Container_MDB2 extends HTTP_Session2_Container
{

    /**
     * MDB2 connection object
     *
     * @var object DB
     */
    protected $db = null;

    /**
     * Session data cache id
     *
     * @var mixed
     */
    protected $crc = false;

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
     * @return object
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * Connect to database by using the given DSN string
     *
     * @param mixed $dsn DSN string or MDB2 object
     *
     * @return boolean
     * @throws HTTP_Session2_Exception An exception?!
     */
    protected function connect($dsn)
    {
        // pseudo singleton approach
        if (is_object($this->db)) {
            return true;
        }
        if (is_string($dsn) || is_array($dsn)) {
            if (MDB2::isError($this->db = MDB2::connect($dsn))) {
                throw new HTTP_Session2_Exception($this->db->getDebugInfo(),
                    $this->db->getCode());
            }
        } else if (is_object($dsn) && ($dsn instanceof MDB2_Driver_Common)) {
            $this->db = $dsn;
        } else if (MDB2::isError($dsn)) {
            throw new HTTP_Session2_Exception($dsn->getDebugInfo(),
                $dsn->getCode());
        } else {
            $msg  = "The given dsn was not valid in file ";
            $msg .= __FILE__ . " at line " . __LINE__;
            throw new HTTP_Session2_Exception($msg,
                HTTP_Session2::ERR_SYSTEM_PRECONDITION);
        }
        if (MDB2::isError($this->db)) {
            throw new HTTP_Session2_Exception($this->db->getMessage(),
                $this->db->getCode());
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
        if (is_object($this->db)) {
            $this->db->disconnect();
        }
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id The Id!
     *
     * @return mixed
     * @throws HTTP_Session2_Exception An exception!?
     * @todo   Get rid off sprintf()
     */
    public function read($id)
    {
        $query = sprintf("SELECT data FROM %s WHERE id = %s AND expiry >= %d",
            $this->options['table'],
            $this->db->quote(md5($id)),
            time());

        $result = $this->db->queryOne($query);
        if (MDB2::isError($result)) {
            throw new HTTP_Session2_Exception($result->getMessage(),
                $result->getCode());
        }
        $this->crc = strlen($result) . crc32($result);
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
        if ((false !== $this->crc)
            && ($this->crc === strlen($data) . crc32($data))) {
            /* $_SESSION hasn't been touched, no need to update the blob column */
            $query = "UPDATE %s SET expiry = %d WHERE id = %s AND expiry >= %d";
            $query = sprintf($query,
                $this->options['table'],
                time() + ini_get('session.gc_maxlifetime'),
                $this->db->quote(md5($id)),
                time());
        } else {
            /* Check if table row already exists */
            $query = sprintf("SELECT COUNT(id) FROM %s WHERE id = '%s'",
                $this->options['table'],
                md5($id));

            $result = $this->db->queryOne($query);
            if (MDB2::isError($result)) {
                throw new HTTP_Session2_Exception($result->getUserInfo(),
                    $result->getCode());
            }
            if (0 == intval($result)) {
                /* Insert new row into table */
                $query = "INSERT INTO %s (id, expiry, data) VALUES (%s, %d, %s)";
                $query = sprintf($query,
                    $this->options['table'],
                    $this->db->quote(md5($id)),
                    time() + ini_get('session.gc_maxlifetime'),
                    $this->db->quote($data));
            } else {
                /* Update existing row */
                $query  = "UPDATE %s SET expiry = %d, data = %s";
                $query .= " WHERE id = %s AND expiry >= %d";
                $query  = sprintf($query,
                    $this->options['table'],
                    time() + ini_get('session.gc_maxlifetime'),
                    $this->db->quote($data),
                    $this->db->quote(md5($id)),
                    time());
            }
        }
        $result = $this->db->query($query);
        if (MDB2::isError($result)) {
            throw new HTTP_Session2_Exception($result->getUserInfo(),
                    $result->getCode());
        }
        return true;
    }

    /**
     * Destroy session data
     *
     * @param string $id The id.
     *
     * @return boolean
     * @throws HTTP_Session2_Exception An exception containing MDB2 data.
     */
    public function destroy($id)
    {
        $query = sprintf("DELETE FROM %s WHERE id = %s",
            $this->options['table'],
            $this->db->quote(md5($id)));

        $result = $this->db->query($query);
        if (MDB2::isError($result)) {
            throw new HTTP_Session2_Exception ($result->getMessage(),
                $result->getCode());
        }
        return true;
    }

    /**
     * Garbage collection
     *
     * Currently supported are mysql, mysqli and pgsql.
     *
     * @param int $maxlifetime The session's maximum lifetime.
     *
     * @return boolean
     * @throws HTTP_Session2_Exception An exception that contains MDB2 data.
     * @todo   Fix database-specific garbage collection.
     */
    public function gc($maxlifetime)
    {
        $query = sprintf("DELETE FROM %s WHERE expiry < %d",
            $this->options['table'],
            time());

        $result = $this->db->query($query);
        if (MDB2::isError($result)) {
            throw new HTTP_Session2_Exception($result->getMessage(),
                $result->getCode());
        }

        if ($this->options['autooptimize']) {
            $this->db->loadModule('Manager');
            $result = $this->db->vacuum($this->options['table']);
            if (MDB2::isError($result)) {
                throw new HTTP_Session2_Exception($result->getMessage(),
                    $result->getCode());
            }
        }
        return true;
    }

    /**
     * Replicate session data to specified target
     *
     * @param string $target The target (table) to replicate to.
     * @param string $id     Id of record to replicate,
     *                       if not specified current session id will be used
     *
     * @return boolean
     * @throws HTTP_Session2_Exception To carry any MDB2 related error out.
     */
    public function replicate($target, $id = null)
    {
        if ($id === null) {
            $id = HTTP_Session2::id();
        }

        // Check if table row already exists
        $query  = "SELECT COUNT(id) FROM $target";
        $query .= " WHERE id = " . $this->db->quote(md5($id), 'text');
        $result = $this->db->queryOne($query);
        if (MDB2::isError($result)) {
            throw new HTTP_Session2_Exception($result->getDebugInfo(),
                $result->getCode());
        }

        // Insert new row into dest table
        if (0 == intval($result)) {
            $query = sprintf("INSERT INTO %s SELECT * FROM %s WHERE id = %s",
                $target,
                $this->options['table'],
                $this->db->quote(md5($id), 'text'));

        } else {
            // Update existing row
            $query  = "UPDATE $target dst, " . $this->options['table'];
            $query .= " src SET dst.expiry = src.expiry,";
            $query .= " dst.data = src.data";
            $query .= " WHERE dst.id = src.id";
            $query .= " AND src.id = " . $this->db->quote(md5($id), 'text');
        }

        $result = $this->db->query($query);
        if (MDB2::isError($result)) {
            throw new HTTP_Session2_Exception($result->getDebugInfo(),
                $result->getCode());
        }

        return true;
    }
}
