<?php declare(strict_types=1);

namespace Ghostff\Session\Drivers;

use SessionHandlerInterface;

class Cookie extends SetGet implements SessionHandlerInterface
{
    public function open($path, $name)
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        return $this->get($_COOKIE[$id] ?? '');
    }

    public function write($id, $data): bool
    {
        $cookie_params            = session_get_cookie_params();
        $cookie_params['expires'] = $cookie_params['lifetime'];
        unset($cookie_params['lifetime']);

        return setcookie($id, $this->set($data), $cookie_params);
    }

    public function destroy($id): bool
    {
        #No need using set cookie already done that in Save class
        return true;
    }

    public function gc($max_lifetime): bool
    {
        return true;
    }
}