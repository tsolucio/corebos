<?php declare(strict_types=1);

use Ghostff\Session\Drivers\File;
use Ghostff\Session\Session;

class CookieSessionTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Session::updateConfiguration([
            Session::CONFIG_DRIVER => File::class,
            Session::CONFIG_START_OPTIONS => [Session::CONFIG_START_OPTIONS_SAVE_PATH => $this->session_path]
        ]);
    }
}