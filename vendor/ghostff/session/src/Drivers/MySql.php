<?php declare(strict_types=1);

namespace Ghostff\Session\Drivers;

use Ghostff\Session\Session;
use PDO;
use PDOException;
use RuntimeException;
use SessionHandlerInterface;

class MySql extends SetGet implements SessionHandlerInterface
{
    private PDO    $conn;
    private string $table;

    public function __construct(array $config)
    {
        if (! extension_loaded('pdo')) {
            throw new RuntimeException('\'Pdo\' extension is needed to use this driver.');
        }

        parent::__construct($config);
        $config      = $config[Session::CONFIG_MYSQL_DS];
        $dsn         = "{$config['driver']}:host={$config['host']};dbname={$config['db_name']}";
        $this->table = $table = $config['db_table'];
        $this->conn  = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_PERSISTENT => $config['persistent_conn'],
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        ]);

        try {
            $this->conn->query("SELECT 1 FROM `{$table}` LIMIT 1");
        } catch (PDOException $e) {
            $this->conn->query('CREATE TABLE `' . $table . '` (
              `id` varchar(250) NOT NULL,
              `data` text NOT NULL,
              `time` int(11) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
        }
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
        $data      = '';
        $statement = $this->conn->prepare("SELECT `data` FROM `{$this->table}` WHERE `id` = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        if ($statement->execute()) {
            $result = $statement->fetch();
            $data   = $result['data'] ?? '';
        }
        $statement = null; // close

        return $this->get($data);
    }

    public function write($id, $data): bool
    {
        $statement = $this->conn->prepare("REPLACE INTO `{$this->table}` (`id`, `data`, `time`) VALUES (:id, :data, :time)");
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $statement->bindValue(':data', $this->set($data), PDO::PARAM_STR);
        $statement->bindValue(':time', time(), PDO::PARAM_INT);
        $completed = $statement->execute();
        $statement = null; // close

        return $completed;
    }

    public function destroy($id): bool
    {
        $statement = $this->conn->prepare("DELETE FROM `{$this->table}` WHERE `id` = :id");
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $completed = $statement->execute();
        $statement = null; // close

        return $completed;
    }

    public function gc($max_lifetime): bool
    {
        $max_lifetime = time() - $max_lifetime;
        $statement    = $this->conn->prepare("DELETE FROM `{$this->table}` WHERE `time` < :time");
        $statement->bindParam(':time', $max_lifetime, PDO::PARAM_INT);
        $completed = $statement->execute();
        $statement = null; // close

        return $completed;
    }
}