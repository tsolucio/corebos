<?php

namespace ClickHouseDB;

use ClickHouseDB\Transport\Http;

class Settings
{
    /**
     * @var Http
     */
    private $client = null;

    /**
     * @var array
     */
    private $settings = [];

    private $_ReadOnlyUser = false;

    /**
     * @var bool
     */
    private $_isHttps = false;

    /**
     * Settings constructor.
     * @param Http $client
     */
    public function __construct(Http $client)
    {
        $default = [
            'extremes' => false,
            'readonly' => true,
            'max_execution_time' => 20,
            'enable_http_compression' => 1,
            'https' => false,
        ];

        $this->settings = $default;
        $this->client = $client;
    }

    /**
     * @param string|int $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->is($key)) {
            return null;
        }
        return $this->settings[$key];
    }

    /**
     * @param string|int $key
     * @return bool
     */
    public function is($key)
    {
        return isset($this->settings[$key]);
    }


    /**
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->get('database');
    }

    /**
     * @param string $db
     * @return $this
     */
    public function database($db)
    {
        $this->set('database', $db);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeOut()
    {
        return $this->get('max_execution_time');
    }

    /**
     * @return mixed|null
     */
    public function isEnableHttpCompression()
    {
        return $this->getSetting('enable_http_compression');
    }

    /**
     * @param bool|int $flag
     * @return $this
     */
    public function enableHttpCompression($flag)
    {
        $this->set('enable_http_compression', intval($flag));
        return $this;
    }


    public function https($flag = true)
    {
        $this->set('https', $flag);
        return $this;
    }

    public function isHttps()
    {
        return $this->get('https');
    }


    /**
     * @param int|bool $flag
     * @return $this
     */
    public function readonly($flag)
    {
        $this->set('readonly', $flag);
        return $this;
    }

    /**
     * @param string $session_id
     * @return $this
     */
    public function session_id($session_id)
    {
        $this->set('session_id', $session_id);
        return $this;
    }

    /**
     * @return mixed|bool
     */
    public function getSessionId()
    {
        if (empty($this->settings['session_id'])) {
            return false;
        }
        return $this->get('session_id');
    }

    /**
     * @return string|bool
     */
    public function makeSessionId()
    {
        $this->session_id(sha1(uniqid('', true)));
        return $this->getSessionId();
    }

    /**
     * @param int|float $time
     * @return $this
     */
    public function max_execution_time($time)
    {
        $this->set('max_execution_time', $time);
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings_array
     * @return $this
     */
    public function apply($settings_array)
    {
        foreach ($settings_array as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param int|bool $flag
     */
    public function setReadOnlyUser($flag)
    {
        $this->_ReadOnlyUser = $flag;
    }

    /**
     * @return bool
     */
    public function isReadOnlyUser()
    {
        return $this->_ReadOnlyUser;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getSetting($name)
    {
        if (!isset($this->settings[$name])) {
            return null;
        }

        return $this->get($name);
    }
}
