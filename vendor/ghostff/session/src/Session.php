<?php declare(strict_types=1);

namespace Ghostff\Session;

use InvalidArgumentException;
use RuntimeException;

class Session
{
    public const CONFIG_DRIVER                  = 'driver';
    public const CONFIG_ENCRYPT_DATA            = 'encrypt_data';
    public const CONFIG_SALT_KEY                = 'salt_key';
    public const CONFIG_START_OPTIONS           = 'start_options';
    public const CONFIG_START_OPTIONS_NAME      = 'name';
    public const CONFIG_START_OPTIONS_SAVE_PATH = 'save_path';
    public const CONFIG_MYSQL_DS                = 'mysql';
    public const CONFIG_MEMCACHED_DS            = 'memcached';
    public const CONFIG_REDIS_DS                = 'redis';


    protected const DEFAULT_SEGMENT  = ':';
    protected const SESSION_INDEX    = 0;
    protected const FLASH_INDEX      = 1;

    protected static ?array $config        = null;
    protected array         $data          = [];
    protected string        $segment       = self::DEFAULT_SEGMENT;
    protected bool          $changed       = false;
    protected string        $id            = '';
    protected string        $name          = '';
    protected array         $cookie_params = [];

    public static function setConfigurationFile(string $file_name = __DIR__ . '/default_config.php'): array
    {
        return self::$config ?: (self::$config = include($file_name));
    }

    public static function updateConfiguration(array $config_override): array
    {
        return self::$config = self::arrayMergeRecursiveDistinct(self::$config ?: self::setConfigurationFile(), $config_override);
    }

    /**
     * Merges arrays recursively returns distinct values.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    protected static function arrayMergeRecursiveDistinct(array $array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value)
        {
            if (\is_array($value) && isset($merged[$key]) && \is_array($merged[$key])) {
                $merged[$key] = self::arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                if (\is_numeric($key)) {
                    if (! \in_array($value, $merged)) {
                        $merged[] = $value;
                    }
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }


    public function __construct(array $config_override = null, string $id = null)
    {
        if ($id != null)
        {
            if (headers_sent($filename, $line_num)) {
                throw new RuntimeException(sprintf('ID must be set before any output is sent to the browser (file: %s, line: %s)', $filename, $line_num));
            } elseif (preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $id) < 1) {
                throw new InvalidArgumentException('Invalid Session ID.');
            } else {
                session_id($id);
            }
        }

        $config = $config_override ? self::updateConfiguration($config_override) : self::setConfigurationFile();
        $driver = $config[self::CONFIG_DRIVER];
        session_set_save_handler(new $driver($config), false);
        session_start($config[self::CONFIG_START_OPTIONS] + ['read_and_close' => true]);

        $this->id                         = session_id();
        $this->data                       = $_SESSION;
        $this->cookie_params              = session_get_cookie_params();
        $this->name                       = $config[self::CONFIG_START_OPTIONS][self::CONFIG_START_OPTIONS_NAME];
        $this->cookie_params['expires']   = $this->cookie_params['lifetime'];

        unset($this->cookie_params['lifetime']);
        setcookie($this->name, $this->id, $this->cookie_params);
    }

    /**
     * Sets new session id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Create a new storage segment.
     *
     * @param string $name The name of the segment.
     *
     * @return Session
     */
    public function segment(string $name): self
    {
        $session          = new self();
        $session->data    =& $this->data;
        $session->changed =& $this->changed;
        $session->segment = $name;

        return $session;
    }

    /**
     * Sets a value in current segment storage.
     *
     * @param string $name  The name of the value to set.
     * @param mixed  $value The value.
     *
     * @return $this
     */
    public function set(string $name, $value): self
    {
        $this->data[$this->segment][self::SESSION_INDEX][$name] = $value;
        $this->changed = true;

        return $this;
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return $this
     */
    public function push(string $name, $value): self
    {
        $values = $this->getOrDefault($name, []);
        if ( ! is_array($values))
        {
            $values = [$values];
        }

        $values[] = $value;

        return $this->set($name, $values);
    }

    /**
     * Gets a value from current segment storage.
     *
     * @param string $name  The name of the value to retrieve.
     *
     * @return mixed
     */
    public function get(string $name)
    {
        if (! isset($this->data[$this->segment][self::SESSION_INDEX]) || ! array_key_exists($name, $this->data[$this->segment][self::SESSION_INDEX])) {
            throw new RuntimeException("\"{$name}\" does not exist in current session segment.");
        }

        return $this->data[$this->segment][self::SESSION_INDEX][$name];
    }

    /**
     * Removes a value from segment storage.
     *
     * @param string $name
     *
     * @return $this
     */
    public function del(string $name): self
    {
        unset($this->data[$this->segment][self::SESSION_INDEX][$name]);
        $this->changed = true;

        return $this;
    }

    /**
     * Removes a value from flash segment storage.
     *
     * @param string $name
     *
     * @return $this
     */
    public function delFlash(string $name): self
    {
        unset($this->data[$this->segment][self::FLASH_INDEX][$name]);
        $this->changed = true;

        return $this;
    }

    /**
     * Pops element off the end or end of specified key values
     *
     * @param string $name  The key of the values.
     * @param bool   $back  Be default items are removed from the bottom, setting this to true reverses the process.
     *
     * @return mixed|null
     */
    public function pop(string $name, bool $back = false)
    {
        $this->changed = true;

        return $back
            ? array_shift($this->data[$this->segment][self::SESSION_INDEX][$name])
            : array_pop($this->data[$this->segment][self::SESSION_INDEX][$name]);
    }

    /**
     * Get a value from current segment storage or default to $default
     *  if specified name was not found.
     *
     * @param string $name      The name of the value to retrieve.
     * @param null   $default   The fallback value if $name value was not found.
     *
     * @return mixed|null
     */
    public function getOrDefault(string $name, $default = null)
    {
        return $this->data[$this->segment][self::SESSION_INDEX][$name] ?? $default;
    }

    /**
     * Sets flash a value in current segment storage.
     *  Note: Flash values are deleted after retrieval.
     *
     * @param string $name  The name of the value to set.
     * @param mixed  $value The value.
     *
     * @return $this
     */
    public function setFlash(string $name, $value): self
    {
        $this->data[$this->segment][self::FLASH_INDEX][$name] = $value;
        $this->changed = true;

        return $this;
    }

    /**
     * Gets a flash value from current segment storage.
     *
     * @param string $name  The name of the value to retrieve.
     *
     * @return mixed
     */
    public function getFlash(string $name)
    {
        if (! isset($this->data[$this->segment][self::FLASH_INDEX]) || ! array_key_exists($name, $this->data[$this->segment][self::FLASH_INDEX])) {
            throw new RuntimeException("flash(\"{$name}\") does not exist in current session segment.");
        }

        $value =  $this->data[$this->segment][self::FLASH_INDEX][$name];
        unset($this->data[$this->segment][self::FLASH_INDEX][$name]);
        $this->changed = true;

        return $value;
    }

    /**
     * Get a flash value from current segment storage or default to $default
     *  if specified name was not found.
     *
     * @param string $name      The name of the value to retrieve.
     * @param null   $default   The fallback value if $name value was not found.
     *
     * @return mixed|null
     */
    public function getFlashOrDefault(string $name, $default = null)
    {
        $value =  $this->data[$this->segment][self::FLASH_INDEX][$name] ?? $default;
        unset($this->data[$this->segment][self::FLASH_INDEX][$name]);
        $this->changed = true;

        return $value;
    }

    /**
     * Gets all storage data.
     *
     * @param string|null $segment If specified on the storage for specified segment is returned.
     *
     * @return array
     */
    public function getAll(string $segment = null): array
    {
        if ($segment == null) {
            return $this->data;
        }

        return $this->data[$segment];
    }

    /**
     * Checks if value exist in current segment storage.
     *
     * @param string $name      The name to search for.
     * @param bool   $in_flash  If specified, search will be matched against flash values.
     *
     * @return bool
     */
    public function exist(string $name, bool $in_flash = false): bool
    {
        $type = $in_flash ? self::FLASH_INDEX : self::SESSION_INDEX;

        return isset($this->data[$this->segment][$type]) && array_key_exists($name, $this->data[$this->segment][$type]);
    }

    /**
     * Generate a new session identifier.
     * https://www.php.net/manual/en/function.session-regenerate-id.php
     *
     * @param bool $delete_old
     *
     * @return Session
     */
    public function rotate(bool $delete_old = false): self
    {
        if (headers_sent($filename, $line_num))
        {
            throw new RuntimeException(sprintf('ID must be regenerated before any output is sent to the browser. (file: %s, line: %s)', $filename, $line_num));
        }

        session_start();
        session_regenerate_id($delete_old);
        $this->id = session_id();
        session_write_close();

        return $this;
    }

    /**
     * Clear all data in current segment storage.
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->data[$this->segment] = [];
        $this->changed = true;

        return $this;
    }

    public function commit(): void
    {
        if ($this->changed) {
            $this->changed = false;
            session_start();
            $_SESSION = $this->data;
            session_write_close();
        }
    }

    /**
     * Destroy the session data including namespace and segments
     */
    public function destroy(): void
    {
        session_start();
        $_SESSION = [];
        @session_destroy();
        session_write_close();

        $this->cookie_params['expires'] = time() - 42000;
        setcookie($this->name, '', $this->cookie_params);
    }

    public function __destruct()
    {
        $this->commit();
    }
}
