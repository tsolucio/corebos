<?php declare(strict_types=1);

namespace Ghostff\Session\Drivers;

use Ghostff\Session\Session;
use SessionHandlerInterface;

class Redis extends SetGet implements SessionHandlerInterface
{
    private \Redis $conn;
    private string $name;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->name = $config[Session::CONFIG_START_OPTIONS][Session::CONFIG_START_OPTIONS_NAME];
        $config     = $config[Session::CONFIG_REDIS_DS];

        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', $config['save_path']);

        $this->conn = new \Redis();
        $this->conn->pconnect($config['host'], $config['port'], $config['timeout'], $config['persistent_id']);
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        return $this->get($this->conn->get("{$this->name}{$id}") ?: '');
    }

    public function write($id, $data): bool
    {
        return $this->conn->setEx("{$this->name}{$id}", (int) ini_get('session.gc_maxlifetime'), $this->set($data));
    }

    public function destroy($id): bool
    {
        return $this->conn->del("{$this->name}{$id}") > 0;
    }

    public function gc($max_lifetime): bool
    {
        return true;
    }
}