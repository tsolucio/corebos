<?php

namespace Vaites\ApacheTika;

use Closure;
use Exception;
use stdClass;

use Vaites\ApacheTika\Clients\CLIClient;
use Vaites\ApacheTika\Clients\WebClient;
use Vaites\ApacheTika\Metadata\Metadata;
use Vaites\ApacheTika\Metadata\MetadataInterface;

/**
 * Apache Tika client interface
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 * @link    https://tika.apache.org/1.24/formats.html
 */
abstract class Client
{
    protected const MODE = null;

    /**
     * Checked flag
     *
     * @var bool
     */
    protected $checked = false;

    /**
     * Response using callbacks
     *
     * @var string
     */
    protected $response = null;

    /**
     * Platform (unix or win)
     *
     * @var string
     */
    protected $platform = null;

    /**
     * Cached responses to avoid multiple request for the same file.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Text encoding
     *
     * @var string|null
     */
    protected $encoding = null;

    /**
     * Callback called on secuential read
     *
     * @var callable|null
     */
    protected $callback = null;

    /**
     * Enable or disable appending when using callback
     *
     * @var bool
     */
    protected $callbackAppend = true;

    /**
     * Size of chunks for callback
     *
     * @var int
     */
    protected $chunkSize = 1048576;

    /**
     * Remote download flag
     *
     * @var bool
     */
    protected $downloadRemote = false;

    /**
     * Configure client
     */
    public function __construct()
    {
        $this->platform = defined('PHP_WINDOWS_VERSION_MAJOR') ? 'win' : 'unix';
    }

    /**
     * Get a class instance throwing an exception if check fails
     *
     * @param string|null     $param1   path or host
     * @param string|int|null $param2   Java binary path or port for web client
     * @param array           $options  options for cURL request
     * @param bool            $check    check JAR file or server connection
     * @return \Vaites\ApacheTika\Clients\CLIClient|\Vaites\ApacheTika\Clients\WebClient
     * @throws \Exception
     */
    public static function make(string $param1 = null, $param2 = null, array $options = [], bool $check = true): Client
    {
        if(preg_match('/\.jar$/', func_get_arg(0)))
        {
            $path = $param1 ? (string) $param1 : null;
            $java = $param2 ? (string) $param2 : null;

            return new CLIClient($path, $java, $check);
        }
        else
        {
            $host = $param1 ? (string) $param1 : null;
            $port = $param2 ? (int) $param2 : null;

            return new WebClient($host, $port, $options, $check);
        }
    }

    /**
     * Get a class instance delaying the check
     *
     * @param string|null $param1 path or host
     * @param int|null    $param2 Java binary path or port for web client
     * @param array       $options options for cURL request
     * @return \Vaites\ApacheTika\Clients\CLIClient|\Vaites\ApacheTika\Clients\WebClient
     * @throws \Exception
     */
    public static function prepare($param1 = null, $param2 = null, $options = []): Client
    {
        return self::make($param1, $param2, $options, false);
    }

    /**
     * Get the encoding
     */
    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    /**
     * Set the encoding
     *
     * @throws \Exception
     */
    public function setEncoding(string $encoding): self
    {
        if(!empty($encoding))
        {
            $this->encoding = $encoding;
        }
        else
        {
            throw new Exception('Invalid encoding');
        }

        return $this;
    }

    /**
     * Get the callback
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    /**
     * Set the callback (callable or closure) for call on secuential read
     *
     * @throws \Exception
     */
    public function setCallback(callable $callback, bool $append = true): self
    {
        if($callback instanceof Closure || is_array($callback))
        {
            $this->callbackAppend = (bool) $append;
            $this->callback = $callback;
        }
        elseif(is_string($callback))
        {
            $this->callbackAppend = (bool) $append;
            $this->callback = function($chunk) use ($callback)
            {
                return call_user_func_array($callback, [$chunk]);
            };
        }
        else
        {
            throw new Exception('Invalid callback');
        }

        return $this;
    }

    /**
     * Get the chunk size
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * Set the chunk size for secuential read
     *
     * @throws \Exception
     */
    public function setChunkSize(int $size): self
    {
        if(static::MODE == 'cli')
        {
            $this->chunkSize = $size;
        }
        else
        {
            throw new Exception('Chunk size is not supported on web mode');
        }

        return $this;
    }

    /**
     * Get the remote download flag
     */
    public function getDownloadRemote(): bool
    {
        return $this->downloadRemote;
    }

    /**
     * Set the remote download flag
     */
    public function setDownloadRemote(bool $download): self
    {
        $this->downloadRemote = (bool) $download;

        return $this;
    }

    /**
     * Gets file metadata
     *
     * @throws \Exception
     */
    public function getMetadata(string $file): MetadataInterface
    {
        $response = $this->parseJsonResponse($this->request('meta', $file));

        if($response instanceof stdClass === false)
        {
            throw new Exception("Unexpected metadata response for $file");
        }

        return Metadata::make($response, $file);
    }

    /**
     * Gets recursive file metadata where the returned array indexes are the file name.
     *
     * Example: for a sample.zip with an example.doc file, the return array looks like if be defined as:
     *
     *  [
     *      'sample.zip' => new Metadata()
     *      'sample.zip/example.doc' => new DocumentMetadata()
     *  ]
     *
     * @link https://cwiki.apache.org/confluence/display/TIKA/TikaServer#TikaServer-RecursiveMetadataandContent
     * @throws \Exception
     */
    public function getRecursiveMetadata(string $file, ?string $format = 'ignore'): array
    {
        if(in_array($format, ['text', 'html', 'ignore']) === false)
        {
            throw new Exception("Unknown recursive type (must be text, html, ignore or null)");
        }

        $response = $this->parseJsonResponse($this->request("rmeta/$format", $file));

        if(is_array($response) === false)
        {
            throw new Exception("Unexpected metadata response for $file");
        }

        $metadata = [];

        foreach($response as $item)
        {
            $name = basename($file);
            if(isset($item->{'X-TIKA:embedded_resource_path'}))
            {
                $name .= $item->{'X-TIKA:embedded_resource_path'};
            }

            $metadata[$name] = Metadata::make($item, $file);
        }

        return $metadata;
    }

    /**
     * Detect language
     *
     * @throws \Exception
     */
    public function getLanguage(string $file): string
    {
        return $this->request('lang', $file);
    }

    /**
     * Detect MIME type
     *
     * @throws \Exception
     */
    public function getMIME(string $file): string
    {
        return $this->request('mime', $file);
    }

    /**
     * Extracts HTML
     *
     * @throws \Exception
     */
    public function getHTML(string $file, callable $callback = null, bool $append = true): string
    {
        if(!is_null($callback))
        {
            $this->setCallback($callback, $append);
        }

        return $this->request('html', $file);
    }

    /**
     * Extracts XHTML
     *
     * @throws \Exception
     */
    public function getXHTML(string $file, callable $callback = null, bool $append = true): string
    {
        if(!is_null($callback))
        {
            $this->setCallback($callback, $append);
        }

        return $this->request('xhtml', $file);
    }

    /**
     * Extracts text
     *
     * @throws \Exception
     */
    public function getText(string $file, callable $callback = null, bool $append = true): string
    {
        if(!is_null($callback))
        {
            $this->setCallback($callback, $append);
        }

        return $this->request('text', $file);
    }

    /**
     * Extracts main text
     *
     * @throws \Exception
     */
    public function getMainText(string $file, callable $callback = null, bool $append = true): string
    {
        if(!is_null($callback))
        {
            $this->setCallback($callback, $append);
        }

        return $this->request('text-main', $file);
    }

    /**
     * Returns current Tika version
     *
     * @throws \Exception
     */
    public function getVersion(): string
    {
        return $this->request('version');
    }

    /**
     * Return the list of Apache Tika supported versions
     *
     * @throws \Exception
     */
    public function getSupportedVersions(): array
    {
        static $versions = null;

        if(is_null($versions))
        {
            $composer = file_get_contents(dirname(__DIR__) . '/composer.json');

            if($composer === false)
            {
                throw new Exception("An error ocurred trying to read package's composer.json file");
            }

            $versions = json_decode($composer, true)['extra']['supported-versions'] ?? null;

            if(empty($versions))
            {
                throw new Exception("An error ocurred trying to read package's composer.json file");
            }
        }

        return $versions;
    }

    /**
     * Sets the checked flag
     */
    public function setChecked(bool $checked): self
    {
        $this->checked = (bool) $checked;

        return $this;
    }

    /**
     * Checks if instance is checked
     */
    public function isChecked(): bool
    {
        return $this->checked;
    }

    /**
     * Check if a response is cached
     */
    protected function isCached(string $type, string $file): bool
    {
        return isset($this->cache[sha1($file)][$type]);
    }

    /**
     * Get a cached response
     *
     * @return mixed
     */
    protected function getCachedResponse(string $type, string $file)
    {
        return $this->cache[sha1($file)][$type] ?? null;
    }

    /**
     * Check if a request type must be cached
     */
    protected function isCacheable(string $type): bool
    {
        return in_array($type, ['lang', 'meta']);
    }

    /**
     * Caches a response
     *
     * @param mixed $response
     */
    protected function cacheResponse(string $type, $response, string $file): bool
    {
        $this->cache[sha1($file)][$type] = $response;

        return true;
    }

    /**
     * Checks if a specific version is supported
     */
    public function isVersionSupported(string $version): bool
    {
        return in_array($version, $this->getSupportedVersions());
    }

    /**
     * Check if a mime type is supported
     *
     * @param string $mime
     * @return bool
     * @throws \Exception
     */
    public function isMIMETypeSupported(string $mime): bool
    {
        return array_key_exists($mime, $this->getSupportedMIMETypes());
    }

    /**
     * Check the request before executing
     *
     * @throws \Exception
     */
    public function checkRequest(string $type, string $file = null): ?string
    {
        // no checks for getters
        if(in_array($type, ['detectors', 'mime-types', 'parsers', 'version']))
        {
            //
        } // invalid local file
        elseif($file !== null && !preg_match('/^http/', $file) && !file_exists($file))
        {
            throw new Exception("File $file can't be opened");
        } // invalid remote file
        elseif($file !== null && preg_match('/^http/', $file))
        {
            $headers = get_headers($file);

            if(empty($headers) || !preg_match('/200/', $headers[0]))
            {
                throw new Exception("File $file can't be opened", 2);
            }
        } // download remote file if required only for integrated downloader
        elseif($file !== null && preg_match('/^http/', $file) && $this->downloadRemote)
        {
            $file = $this->downloadFile($file);
        }

        return $file;
    }

    /**
     * Filter response to fix common issues
     *
     * @param string $response
     * @return string
     */
    protected function filterResponse(string $response): string
    {
        // fix Log4j2 warning
        $response = trim(str_replace
        (
            'WARNING: sun.reflect.Reflection.getCallerClass is not supported. This will impact performance.',
            '',
            $response
        ));

        return trim($response);
    }

    /**
     * Parse the response returned by Apache Tika
     *
     * @return mixed
     * @throws \Exception
     */
    protected function parseJsonResponse(string $response)
    {
        // an empty response throws an error
        if(empty($response) || trim($response) == '')
        {
            throw new Exception('Empty response');
        }

        // decode the JSON response
        $json = json_decode($response);

        // exceptions if metadata is not valid
        if(json_last_error())
        {
            dd($response);

            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Error parsing JSON response';

            throw new Exception($message, json_last_error());
        }

        return $json;
    }

    /**
     * Download file to a temporary folder
     *
     * @link https://wiki.apache.org/tika/TikaJAXRS#Specifying_a_URL_Instead_of_Putting_Bytes
     * @throws \Exception
     */
    protected function downloadFile(string $file): string
    {
        $dest = tempnam(sys_get_temp_dir(), 'TIKA');

        if($dest === false)
        {
            throw new Exception("Can't create a temporary file at " . sys_get_temp_dir());
        }

        $fp = fopen($dest, 'w+');

        if($fp === false)
        {
            throw new Exception("$dest can't be opened");
        }

        $ch = curl_init($file);

        if($ch === false)
        {
            throw new Exception("$file can't be downloaded");
        }

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);

        if(curl_errno($ch))
        {
            throw new Exception(curl_error($ch));
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if($code != 200)
        {
            throw new Exception("$file can't be downloaded", $code);
        }

        return $dest;
    }

    /**
     * Must return the supported MIME types
     *
     * @throws \Exception
     */
    abstract public function getSupportedMIMETypes(): array;

    /**
     * Must return the available detectors
     *
     * @throws \Exception
     */
    abstract public function getAvailableDetectors(): array;

    /**
     * Must return the available parsers
     *
     * @throws \Exception
     */
    abstract public function getAvailableParsers(): array;

    /**
     * Check Java binary, JAR path or server connection
     */
    abstract public function check(): void;

    /**
     * Configure and make a request and return its results.
     *
     * @throws \Exception
     */
    abstract public function request(string $type, string $file = null): ?string;
}
