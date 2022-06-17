<?php declare(strict_types=1);

namespace Ghostff\Session\Drivers;

use SessionHandlerInterface;

class File extends SetGet implements SessionHandlerInterface
{
    private string $save_path = '';

    public function open($path, $name): bool
    {
        $this->save_path = $path . DIRECTORY_SEPARATOR;

        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        return $this->get((string) @file_get_contents("{$this->save_path}/sess_{$id}"));
    }

    public function write($id, $data): bool
    {
        return file_put_contents( "{$this->save_path}sess_{$id}", $this->set($data)) !== false;
    }

    public function destroy($id): bool
    {
        if (file_exists($file = "{$this->save_path}sess_{$id}")) {
            unlink($file);
        }

        return true;
    }

    public function gc($max_lifetime): bool
    {
        $time = time();
        foreach (glob("{$this->save_path}sess_*") as $file) {
            if (filemtime($file) + $max_lifetime < $time && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}