<?php namespace Vaites\ApacheTika\Tests;

use Exception;

use PHPUnit\Framework\TestCase;

use Vaites\ApacheTika\Client;
use Vaites\ApacheTika\Metadata\Metadata;

/**
 * Error tests
 */
class ErrorTest extends TestCase
{
    /**
     * Current tika version
     *
     * @var string
     */
    protected static $version = null;

    /**
     * Binary path (jars)
     *
     * @var string
     */
    protected static $binaries = null;

    /**
     * Get env variables
     */
    public function __construct(string $name = null, array $data = array(), $dataName = '')
    {
        self::$version = getenv('APACHE_TIKA_VERSION');
        self::$binaries = getenv('APACHE_TIKA_BINARIES');

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Test wrong command line mode path
     */
    public function testAppPath(): void
    {
        try
        {
            $client = Client::prepare('/nonexistent/path/to/apache-tika.jar');
            $client->getVersion();
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Apache Tika app JAR not found', $exception->getMessage());
        }
    }

    /**
     * Test unexpected exit value for command line mode
     */
    public function testAppExitValue(): void
    {
        $path = self::getPathForVersion(self::$version);

        try
        {
            $client = Client::make($path);

            rename($path, $path . '.bak');

            $client->getVersion();
        }
        catch(Exception $exception)
        {
            rename($path . '.bak', $path);

            $this->assertStringContainsString('Unexpected exit value', $exception->getMessage());
        }
    }

    /**
     * Test invalid Java binary path for command line mode
     */
    public function testJavaBinary(): void
    {
        $path = self::getPathForVersion(self::$version);

        try
        {
            $client = Client::make($path, '/nonexistent/path/to/java');
            $client->getVersion();
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Java command not found', $exception->getMessage());
        }
    }

    /**
     * Test wrong server
     */
    public function testServerConnection(): void
    {
        try
        {
            $client = Client::prepare('localhost', 9997);
            $client->getVersion();

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertThat($exception->getCode(), $this->logicalOr
            (
                $this->equalTo(CURLE_COULDNT_CONNECT),
                $this->equalTo(CURLE_OPERATION_TIMEDOUT)
            ));
        }
    }

    /**
     * Test wrong request options
     */
    public function testRequestOptions(): void
    {
        try
        {
            $client = Client::make('localhost', 9998);
            $client->request('bad');

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Unknown type bad', $exception->getMessage());
        }
    }

    /**
     * Test invalidrequest options
     */
    public function testRequestRestrictedOptions(): void
    {
        try
        {
            Client::make('localhost', 9998, [CURLOPT_PUT => false]);
        }
        catch(Exception $exception)
        {
            $this->assertEquals(3, $exception->getCode());
        }
    }

    /**
     * Test wrong recursive metadata type
     */
    public function testRequestMetadataType(): void
    {
        try
        {
            $client = Client::make('localhost', 9998);
            $client->getRecursiveMetadata(dirname(__DIR__) . '/samples/sample3.png', 'bad');

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Unknown recursive type', $exception->getMessage());
        }
    }

    /**
     * Test unsupported media type
     *
     * NOTE: return value was changed in version 1.23
     *
     * @link    https://github.com/apache/tika/blob/master/CHANGES.txt
     */
    public function testUnsupportedMedia(): void
    {
        try
        {
            $client = Client::make('localhost', 9998);
            $client->getText(dirname(__DIR__) . '/samples/sample4.doc');

            $this->fail();
        }
        catch(Exception $exception)
        {
            if(version_compare(self::$version, '1.23') < 0)
            {
                $this->assertEquals(415, $exception->getCode());
            }
            else
            {
                $this->assertEquals(0, $exception->getCode());
            }
        }
    }

    /**
     * Test unknown recursive metadata type
     */
    public function testUnknownRecursiveMetadataType(): void
    {
        try
        {
            $client = Client::make('localhost', 9998);
            $client->getRecursiveMetadata('example.doc', 'error');

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Unknown recursive type', $exception->getMessage());
        }
    }

    /**
     * Test invalid chunk size
     */
    public function testUnsupportedChunkSize(): void
    {
        try
        {
            $client = Client::make('localhost', 9998);
            $client->setChunkSize(1024);
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Chunk size is not supported', $exception->getMessage());
        }
    }

    /**
     * Test wrong request type for all clients
     *
     * @dataProvider    parameterProvider
     */
    public function testRequestType(array $parameters): void
    {
        try
        {
            $client = call_user_func_array(['Vaites\ApacheTika\Client', 'make'], $parameters);
            $client->request('bad');

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertStringContainsString('Unknown type bad', $exception->getMessage());
        }
    }

    /**
     * Test nonexistent local file for all clients
     *
     * @dataProvider    parameterProvider
     */
    public function testLocalFile(array $parameters): void
    {
        try
        {
            $client = call_user_func_array(['Vaites\ApacheTika\Client', 'make'], $parameters);
            $client->getText('/nonexistent/path/to/file.pdf');

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertEquals(0, $exception->getCode());
        }
    }

    /**
     * Test nonexistent remote file for all clients
     *
     * @dataProvider    parameterProvider
     */
    public function testRemoteFile(array $parameters): void
    {
        try
        {
            $client = call_user_func_array(['Vaites\ApacheTika\Client', 'make'], $parameters);
            $client->getText('http://localhost/nonexistent/path/to/file.pdf');

            $this->fail();
        }
        catch(Exception $exception)
        {
            $this->assertEquals(2, $exception->getCode());
        }
    }

    /**
     * Client parameters provider
     */
    public function parameterProvider(): array
    {
        return
        [
            [[self::getPathForVersion(self::$version)]],
            [['localhost', 9998]]
        ];
    }

    /**
     * Get the full path of Tika app for a specified version
     */
    private static function getPathForVersion(string $version): string
    {
        return self::$binaries . "/tika-app-{$version}.jar";
    }
}