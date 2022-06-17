<?php declare(strict_types=1);

namespace Ghostff\Session\Drivers;

use Ghostff\Session\Session;
use RuntimeException;

class SetGet
{
    private bool   $encrypt;
    private string $key;

    public function __construct(array $config)
    {
        $this->encrypt = $config[Session::CONFIG_ENCRYPT_DATA];
        $this->key     = $config[Session::CONFIG_SALT_KEY];

        # if session data encryption is enabled, we want to make sure openssl is loaded.
        if ($this->encrypt && ! extension_loaded('openssl')) {
            throw new RuntimeException('The openssl extension is missing. Please check your PHP configuration.');
        }
    }

    /**
     * Encrypts session data is required.
     *
     * @param string $data
     *
     * @return string
     */
    public function set(string $data): string
    {
        if (! $this->encrypt) {
            return $data;
        }

        // Set a random salt
        $salt   = openssl_random_pseudo_bytes(16);
        $salted = '';
        $dx     = '';

        // Salt the key(32) and iv(16) = 48
        while (strlen($salted) < 48) {
            $dx     = hash('sha256', "{$dx}{$this->key}{$salt}", true);
            $salted .= $dx;
        }

        $key            = substr($salted, 0, 32);
        $iv             = substr($salted, 32,16);
        $encrypted_data = openssl_encrypt($data, 'AES-256-CBC', $key, 1, $iv);

        return base64_encode("{$salt}{$encrypted_data}");
    }

    /**
     * Decrypts session data if required.
     *
     * @param string $data
     *
     * @return string
     */
    public function get(string $data): string
    {
        if (! $this->encrypt || ! $data) {
            return $data;
        }

        $data    = base64_decode($data);
        $salt    = substr($data, 0, 16);
        $ct      = substr($data, 16);
        $rounds  = 3; // depends on key length
        $data00  = "{$this->key}{$salt}";
        $hash    = [];
        $hash[0] = hash('sha256', $data00, true);
        $result  = $hash[0];

        for ($i = 1; $i < $rounds; $i++) {
            $hash[$i] = hash('sha256', "{$hash[$i - 1]}{$data00}", true);
            $result .= $hash[$i];
        }

        $passphrase = substr($result, 0, 32);
        $iv         = substr($result, 32, 16);

        if (! \is_string($ct)) {
            // possible cause, session was set without encryption but tried to be retrieved with encryption.
            throw new \RangeException('Invalid data. Maybe clear your session an try again');
        }

        return openssl_decrypt($ct, 'AES-256-CBC', $passphrase, 1, $iv) ?: '';
    }
}