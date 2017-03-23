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
 * | Author: Tony Bibbs <tony@geeklog.net>                                 |
 * +-----------------------------------------------------------------------+
 *
 * PHP version 5
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivaniovich <info@wwwlab.net>
 * @author   Tony Bibbs <tony@geeklog.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  CVS: $Id: Session2.php 267739 2008-10-25 16:54:23Z till $
 * @link     http://pear.php.net/package/HTTP_Session2
 */

/**
 * HTTP_Session2_Exception
 */
require_once 'HTTP/Session2/Exception.php';

/**
 * Class for managing HTTP sessions
 *
 * Provides access to session-state values as well as session-level
 * settings and lifetime management methods.
 * Based on the standart PHP session handling mechanism
 * it provides for you more advanced features such as
 * database container, idle and expire timeouts, etc.
 *
 * Expample 1:
 *
 * <code>
 * // Setting some options and detecting of a new session
 * HTTP_Session2::useCookies(false);
 * HTTP_Session2::start('MySessionID');
 * HTTP_Session2::set('variable', 'The string');
 * if (HTTP_Session2::isNew()) {
 *     echo 'new session was created with the current request';
 *     $visitors++; // Increase visitors count
 * }
 *
 * //HTTP_Session2::regenerateId();
 * </code>
 *
 * Example 2:
 *
 * <code>
 * // Using database container
 * HTTP_Session2::setContainer('DB');
 * HTTP_Session2::start();
 * </code>
 *
 * Example 3:
 *
 * <code>
 * // Setting timeouts
 * HTTP_Session2::start();
 * HTTP_Session2::setExpire(time() + 60 * 60); // expires in one hour
 * HTTP_Session2::setIdle(10 * 60);            // idles in ten minutes
 * if (HTTP_Session2::isExpired()) {
 *     // expired
 *     echo('Your session is expired!');
 *     HTTP_Session2::destroy();
 * }
 * if (HTTP_Session2::isIdle()) {
 *     // idle
 *     echo('You've been idle for too long!');
 *     HTTP_Session2::destroy();
 * }
 * HTTP_Session2::updateIdle();
 * </code>
 *
 * @category HTTP
 * @package  HTTP_Session2
 * @author   Alexander Radivaniovich <info@wwwlab.net>
 * @author   Tony Bibbs <tony@geeklog.net>
 * @license  http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/HTTP_Session2
 */
class HTTP_Session2
{
    /**
     * @const STARTED - The session was started with the current request
     */
    const STARTED = 1;

    /**
     * @const CONTINUE - No new session was started with the current request
     */
    const CONTINUED = 2;

    /**
     * @const ERR_UNKNOWN_CONTAINER - Container not found.
     */
    const ERR_UNKNOWN_CONTAINER = 667;

    /**
     * @const ERR_SYSTEM_PERM - System permissions not sufficient.
     *        E.g. Not enough permissions to override ini-settings.
     */
    const ERR_SYSTEM_PERM = 668;
    
    /**
     * @const ERR_SYSTEM_PRECONDITION - Precondition failed. E.g. error occured and 
     *        HTTP_Session2 can't start up, etc..
     */
    const ERR_SYSTEM_PRECONDITION = 669;

    /**
     * @const ERR_NOT_IMPLEMENTED Feature is not yet Implement in the container.
     */
    const ERR_NOT_IMPLEMENTED = 670;

    /**
     * Container instance
     */
    public static $container;

    /**
     * Sets user-defined session storage functions
     *
     * Sets the user-defined session storage functions which are used
     * for storing and retrieving data associated with a session.
     * This is most useful when a storage method other than
     * those supplied by PHP sessions is preferred.
     * i.e. Storing the session data in a local database.
     *
     * @param string $container         Name of the container (e.g. DB, MDB, ...).
     * @param array  $container_options Options, most likely an array.
     *
     * @return void
     * @see session_set_save_handler()
     */
    static function setContainer($container, $container_options = null)
    {
        $container_class     = 'HTTP_Session2_Container_' . $container;
        $container_classfile = 'HTTP/Session2/Container/' . $container . '.php';

        if (!class_exists($container_class)) {
            include_once $container_classfile;
        }
        if (!class_exists($container_class)) {
            throw new HTTP_Session2_Exception(
                "Container class, $container_class, does not exist",
                self::ERR_UNKNOWN_CONTAINER);
        }
        self::$container = new $container_class($container_options);

        self::$container->set();
    }

    /**
     * Initializes session data
     *
     * Creates a session (or resumes the current one
     * based on the session id being passed
     * via a GET variable or a cookie).
     * You can provide your own name and/or id for a session.
     *
     * @param string $name Name of a session, default is 'SessionID'
     * @param string $id   Id of a session which will be used
     *                     only when the session is new
     *
     * @return void
     * @see    session_name()
     * @see    session_id()
     * @see    session_start()
     */
    public static function start($name = 'SessionID', $id = null)
    {
        self::name($name);
        if (is_null(self::detectID())) {
            if ($id) {
                self::id($id);
            } else {
                self::id(uniqid(dechex(rand())));
            }
        }
        session_start();
        if (!isset($_SESSION['__HTTP_Session2_Info'])) {
            $_SESSION['__HTTP_Session2_Info'] = self::STARTED;
        } else {
            $_SESSION['__HTTP_Session2_Info'] = self::CONTINUED;
        }
    }

    /**
     * Writes session data and ends session
     *
     * Session data is usually stored after your script
     * terminated without the need to call HTTP_Session2::stop(),
     * but as session data is locked to prevent concurrent
     * writes only one script may operate on a session at any time.
     * When using framesets together with sessions you will
     * experience the frames loading one by one due to this
     * locking. You can reduce the time needed to load all the
     * frames by ending the session as soon as all changes
     * to session variables are done.
     *
     * @return void
     * @see    session_write_close()
     */
    public static function pause()
    {
        session_write_close();
    }

    /**
     * Frees all session variables and destroys all data
     * registered to a session
     *
     * This method resets the $_SESSION variable and
     * destroys all of the data associated
     * with the current session in its storage (file or DB).
     * It forces new session to be started after this method
     * is called. It does not unset the session cookie.
     *
     * @return void
     * @see    session_unset()
     * @see    session_destroy()
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Free all session variables
     *
     * @todo   TODO Save expire and idle timestamps?
     * @return void
     */
    public static function clear()
    {
        $info = $_SESSION['__HTTP_Session2_Info'];

        session_unset();

        $_SESSION['__HTTP_Session2_Info'] = $info;
    }

    /**
     * Tries to find any session id in $_GET, $_POST or $_COOKIE
     *
     * @return string Session ID (if exists) or null
     */
    public static function detectID()
    {
        if (self::useCookies()) {
            if (isset($_COOKIE[self::name()])) {
                return $_COOKIE[self::name()];
            }
        } else {
            if (isset($_GET[self::name()])) {
                return $_GET[self::name()];
            }
            if (isset($_POST[self::name()])) {
                return $_POST[self::name()];
            }
        }
        return null;
    }

    /**
     * Sets new name of a session
     *
     * @param string $name New name of a sesion
     *
     * @return string Previous name of a session
     * @see    session_name()
     */
    public static function name($name = null)
    {
        if (isset($name)) {
            return session_name($name);
        }
        return session_name();
    }

    /**
     * Sets new ID of a session
     *
     * @param string $id New ID of a sesion
     *
     * @return string Previous ID of a session
     * @see    session_id()
     */
    public static function id($id = null)
    {
        if (isset($id)) {
            return session_id($id);
        }
        return session_id();
    }

    /**
     * Sets the maximum expire time
     *
     * @param integer $time Time in seconds
     * @param bool    $add  Add time to current expire time or not
     *
     * @return void
     */
    public static function setExpire($time, $add = false)
    {
        if ($add && isset($_SESSION['__HTTP_Session2_Expire'])) {
            $_SESSION['__HTTP_Session2_Expire'] += $time;
        } else {
            $_SESSION['__HTTP_Session2_Expire'] = $time;
        }
        if (!isset($_SESSION['__HTTP_Session2_Expire_TS'])) {
            $_SESSION['__HTTP_Session2_Expire_TS'] = time();
        }
    }

    /**
     * Sets the maximum idle time
     *
     * Sets the time-out period allowed
     * between requests before the session-state
     * provider terminates the session.
     *
     * @param integer $time Time in seconds
     * @param bool    $add  Add time to current maximum idle time or not
     *
     * @return void
     */
    public static function setIdle($time, $add = false)
    {
        if ($add && isset($_SESSION['__HTTP_Session2_Idle'])) {
            $_SESSION['__HTTP_Session2_Idle'] += $time;
        } else {
            $_SESSION['__HTTP_Session2_Idle'] = $time;
        }
        if (!isset($_SESSION['__HTTP_Session2_Idle_TS'])) {
            $_SESSION['__HTTP_Session2_Idle_TS'] = time();
        }
    }

    /**
     * Returns the time up to the session is valid
     *
     * @return integer Time when the session idles
     */
    public static function sessionValidThru()
    {
        if (
            !isset($_SESSION['__HTTP_Session2_Idle_TS'])
            || !isset($_SESSION['__HTTP_Session2_Idle'])) {
            return 0;
        }
        return $_SESSION['__HTTP_Session2_Idle_TS']
            + $_SESSION['__HTTP_Session2_Idle'];
    }

    /**
     * Check if session is expired
     *
     * @return boolean
     */
    public static function isExpired()
    {
        if (
            isset($_SESSION['__HTTP_Session2_Expire'])
            && $_SESSION['__HTTP_Session2_Expire'] > 0
            && isset($_SESSION['__HTTP_Session2_Expire_TS'])
            &&
            (
                $_SESSION['__HTTP_Session2_Expire_TS']
                + $_SESSION['__HTTP_Session2_Expire']
            ) <= time()) {
            return true;
        }
        return false;
    }

    /**
     * Check if session is idle
     *
     * @return boolean Obvious
     */
    public static function isIdle()
    {
        if (
            isset($_SESSION['__HTTP_Session2_Idle'])
            && $_SESSION['__HTTP_Session2_Idle'] > 0
            && isset($_SESSION['__HTTP_Session2_Idle_TS'])
            && (
                $_SESSION['__HTTP_Session2_Idle_TS']
                + $_SESSION['__HTTP_Session2_Idle']
            ) <= time()) {
            return true;
        }
        return false;
    }

    /**
     * Updates the idletime
     *
     * @return void
     */
    public static function updateIdle()
    {
        if (isset($_SESSION['__HTTP_Session2_Idle_TS'])) {
            $_SESSION['__HTTP_Session2_Idle_TS'] = time();
        }
    }

    /**
     * If optional parameter is specified it indicates whether the module will
     * use cookies to store the session id on the client side in a cookie.
     *
     * By default this cookie will be deleted when the browser is closed!
     *
     * It will throw an Exception if it's not able to set the session.use_cookie
     * property.
     *
     * It returns the previous value of this property.
     *
     * @param boolean $useCookies If specified it will replace the previous value of
     *                            this property. By default 'null', which doesn't
     *                            change any setting on your system. If you supply a
     *                            parameter, please supply 'boolean'.
     *
     * @return boolean The previous value of the property
     * 
     * @throws HTTP_Session2_Exception If ini_set() fails!
     * @see    session_set_cookie_params()
     * @link   http://php.net/manual/en/function.session-set-cookie-params.php
     */
    public static function useCookies($useCookies = null)
    {
        $return = false;
        if (ini_get('session.use_cookies') == '1') {
            $return = true;
        }
        if ($useCookies !== null) {
            if ($useCookies === true) {
                $status = ini_set('session.use_cookies', 1);
            } else {
                $status = ini_set('session.use_cookies', 0);
            }
            if ($status === false) {
                $msg  = "Could not set 'session.use_cookies'. Please check your ";
                $msg .= 'permissions to override php.ini-settings. E.g. a possible ';
                $msg .= 'php_admin_value setting or blocked ini_set() calls ';
                throw new HTTP_Session2_Exception($msg, self::ERR_SYSTEM_PERM);
            }
        }
        return $return;
    }

    /**
     * Gets a value indicating whether the session
     * was created with the current request
     *
     * You MUST call this method only after you have started
     * the session with the HTTP_Session2::start() method.
     *
     * @return boolean true when the session was created with the current request
     *                 false otherwise
     *
     * @see  self::start()
     * @uses self::STARTED
     */
    public static function isNew()
    {
        // The best way to check if a session is new is to check
        // for existence of a session data storage
        // with the current session id, but this is impossible
        // with the default PHP module wich is 'files'.
        // So we need to emulate it.
        return !isset($_SESSION['__HTTP_Session2_Info']) ||
            $_SESSION['__HTTP_Session2_Info'] == self::STARTED;
    }

    /**
     * Register variable with the current session
     *
     * @param string $name Name of a global variable
     *
     * @return void
     * @see session_register()
     */
    public static function register($name)
    {
        session_register($name);
    }

    /**
     * Unregister a variable from the current session
     *
     * @param string $name Name of a global variable
     *
     * @return void
     * @see    session_unregister()
     */
    public static function unregister($name)
    {
        session_unregister($name);
    }

    /**
     * Returns session variable
     *
     * @param string $name    Name of a variable
     * @param mixed  $default Default value of a variable if not set
     *
     * @return mixed  Value of a variable
     */
    public static function &get($name, $default = null)
    {
        if (!isset($_SESSION[$name]) && isset($default)) {
            $_SESSION[$name] = $default;
        }
        return $_SESSION[$name];
    }

    /**
     * Sets session variable
     *
     * @param string $name  Name of a variable
     * @param mixed  $value Value of a variable
     *
     * @return mixed Old value of a variable
     */
    public static function set($name, $value)
    {
        $return = (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
        if (null === $value) {
            unset($_SESSION[$name]);
        } else {
            $_SESSION[$name] = $value;
        }
        return $return;
    }

    /**
     * Returns local variable of a script
     *
     * Two scripts can have local variables with the same names
     *
     * @param string $name    Name of a variable
     * @param mixed  $default Default value of a variable if not set
     *
     * @return mixed  Value of a local variable
     */
    static function &getLocal($name, $default = null)
    {
        $local = md5(self::localName());
        if (!is_array($_SESSION[$local])) {
            $_SESSION[$local] = array();
        }
        if (!isset($_SESSION[$local][$name]) && isset($default)) {
            $_SESSION[$local][$name] = $default;
        }
        return $_SESSION[$local][$name];
    }

    /**
     * Sets local variable of a script.
     * Two scripts can have local variables with the same names.
     *
     * @param string $name  Name of a local variable
     * @param mixed  $value Value of a local variable
     *
     * @return mixed Old value of a local variable
     */
    static function setLocal($name, $value)
    {
        $local = md5(self::localName());
        if (!is_array($_SESSION[$local])) {
            $_SESSION[$local] = array();
        }
        $return = $_SESSION[$local][$name];
        if (null === $value) {
            unset($_SESSION[$local][$name]);
        } else {
            $_SESSION[$local][$name] = $value;
        }
        return $return;
    }

    /**
     * set the usage of transparent SID
     *
     * @param boolean $useTransSID Flag to use transparent SID
     *
     * @return boolean
     */
    static function useTransSID($useTransSID = false)
    {
        $return = ini_get('session.use_trans_sid') ? true : false;
        if ($useTransSID === false) {
            ini_set('session.use_trans_sid', $useTransSID ? 1 : 0);
        }
        return $return;
    }

    /**
     * Sets new local name
     *
     * @param string $name New local name
     *
     * @return string Previous local name
     */
    static function localName($name = null)
    {
        $return = '';
        if (isset($GLOBALS['__HTTP_Session2_Localname'])) {
            $return .= $GLOBALS['__HTTP_Session2_Localname'];
        }
        if (!empty($name)) {
            $GLOBALS['__HTTP_Session2_Localname'] = $name;
        }
        return $return;
    }

    /**
     * init
     *
     * @return void
     */
    static function init()
    {
        // Disable auto-start of a sesion
        ini_set('session.auto_start', 0);

        // Set local name equal to the current script name
        self::localName($_SERVER['SCRIPT_NAME']);
    }

    /**
     * Regenrates session id
     *
     * If session_regenerate_id() is not available emulates its functionality
     *
     * @param boolean $deleteOldSessionData Whether to delete data of old session
     *
     * @return boolean
     */
    public static function regenerateId($deleteOldSessionData = false)
    {
        if (function_exists('session_regenerate_id')) {
            return session_regenerate_id($deleteOldSessionData);

            // emulate session_regenerate_id()
        } else {

            do {
                $newId = uniqid(dechex(rand()));
            } while ($newId === session_id());

            if ($deleteOldSessionData) {
                session_unset();
            }

            session_id($newId);

            return true;
        }
    }

    /**
     * This function copies session data of specified id to specified table
     *
     * @param string $target Target to replicate to
     * @param string $id     Id of record to replicate
     *
     * @return boolean
     */
    public static function replicate($target, $id = null)
    {
        return self::$container->replicate($target, $id);
    }

    /**
     * If optional parameter is specified it determines the number of seconds
     * after which session data will be seen as 'garbage' and cleaned up
     *
     * It returns the previous value of this property
     *
     * @param int $gcMaxLifetime If specified it will replace the previous value of
     *                           this property, and must be integer.
     *
     * @return boolean The previous value of the property
     */
    public static function setGcMaxLifetime($gcMaxLifetime = null)
    {
        $return = ini_get('session.gc_maxlifetime');
        if (isset($gcMaxLifetime) && is_int($gcMaxLifetime) && $gcMaxLifetime >= 1) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }
        return $return;
    }

    /**
     * If optional parameter is specified it determines the
     * probability that the gc (garbage collection) routine is started
     * and session data is cleaned up
     *
     * It returns the previous value of this property
     *
     * @param int $gcProbability If specified it will replace the previous value of
     *                           this property.
     *
     * @return boolean The previous value of the property
     */
    public static function setGcProbability($gcProbability = null)
    {
        $return = ini_get('session.gc_probability');
        if (isset($gcProbability)  &&
            is_int($gcProbability) &&
            $gcProbability >= 1    &&
            $gcProbability <= 100) {
            ini_set('session.gc_probability', $gcProbability);
        }
        return $return;
    }
}

/**
 * init {@link HTTP_Session2}
 * 
 * @see HTTP_Session2::init()
 */
HTTP_Session2::init();
