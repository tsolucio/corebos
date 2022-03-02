<?php

namespace Vaites\ApacheTika\Clients;

use Exception;

use Vaites\ApacheTika\Client;

/**
 * Apache Tika web client
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 * @link    https://cwiki.apache.org/confluence/display/TIKA/TikaServer
 */
class WebClient extends Client
{
    protected const MODE = 'web';

    /**
     * Cached responses to avoid multiple request for the same file
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Apache Tika server host
     *
     * @var string
     */
    protected $host = null;

    /**
     * Apache Tika server port
     *
     * @var int
     */
    protected $port = null;

    /**
     * Number of retries on server error
     *
     * @var int
     */
    protected $retries = 3;

    /**
     * Default cURL options
     *
     * @var array
     */
    protected $options =
    [
        CURLINFO_HEADER_OUT     => true,
        CURLOPT_HTTPHEADER      => [],
        CURLOPT_PUT             => true,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_TIMEOUT         => 5
    ];

    /**
     * Configure class and test if server is running
     *
     * @throws \Exception
     */
    public function __construct(string $host = null, int $port = null, array $options = [], bool $check = true)
    {
        parent::__construct();

        if(is_string($host) && filter_var($host, FILTER_VALIDATE_URL))
        {
            $this->setUrl($host);
        }
        elseif($host)
        {
            $this->setHost($host);
        }

        if(is_numeric($port))
        {
            $this->setPort($port);
        }

        if(!empty($options))
        {
            $this->setOptions($options);
        }

        $this->setDownloadRemote(true);

        if($check === true)
        {
            $this->check();
        }
    }

    /**
     * Get the base URL
     */
    public function getUrl(): string
    {
        return sprintf('http://%s:%d', $this->host, $this->port ?: 9998);
    }

    /**
     * Set the host and port using an URL
     */
    public function setUrl(string $url): self
    {
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);

        if(!empty($host))
        {
            $this->setHost((string) $host);
        }

        if(!empty($port))
        {
            $this->setPort((int) $port);
        }

        return $this;
    }

    /**
     * Get the host
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Set the host
     */
    public function setHost(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the port
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Set the port
     */
    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the number of retries
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * Set the number of retries
     */
    public function setRetries(int $retries): self
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * Get all the options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get an specified option
     *
     * @return  mixed
     */
    public function getOption(int $key)
    {
        return $this->options[$key] ?? null;
    }

    /**
     * Set a cURL option to be set with curl_setopt()
     *
     * @link http://php.net/manual/en/curl.constants.php
     * @link http://php.net/manual/en/function.curl-setopt.php
     * @param mixed $value
     * @throws \Exception
     */
    public function setOption(int $key, $value): self
    {
        if(in_array($key, [CURLINFO_HEADER_OUT, CURLOPT_PUT, CURLOPT_RETURNTRANSFER]))
        {
            throw new Exception("Value for cURL option $key cannot be modified", 3);
        }

        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Set the cURL options
     *
     * @throws \Exception
     */
    public function setOptions(array $options): self
    {
        foreach($options as $key => $value)
        {
            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * Get all the HTTP headers
     */
    public function getHeaders(): array
    {
        return $this->options[CURLOPT_HTTPHEADER];
    }

    /**
     * Get an specified HTTP header
     *
     * @return  mixed
     */
    public function getHeader(string $name): ?string
    {
        $value = [];

        foreach($this->options[CURLOPT_HTTPHEADER] as $header)
        {
            if(preg_match("/$name:\s+(.+)/i", $header, $match))
            {
                $value = $match[1];
                break;
            }
        }

        return $value;
    }

    /**
     * Set a cURL header to be set with curl_setopt()
     *
     * @param mixed $value
     * @throws \Exception
     */
    public function setHeader(string $name, $value): self
    {
        $this->options[CURLOPT_HTTPHEADER][] = "$name: $value";

        return $this;
    }

    /**
     * Set the HTTP headers
     *
     * @throws \Exception
     */
    public function setHeaders(array $headers): self
    {
        foreach($headers as $name => $value)
        {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * Get the accepted OCR languages
     */
    public function getOCRLanguages(): array
    {
        return explode('+', $this->getHeader('X-Tika-OCRLanguage') ?: '');
    }

    /**
     * Set the accepted OCR language
     *
     * @throws \Exception
     */
    public function setOCRLanguage(string $language): self
    {
        $this->setHeader('X-Tika-OCRLanguage', $language);

        return $this;
    }

    /**
     * Set the accepted OCR languages
     *
     * @throws \Exception
     */
    public function setOCRLanguages(array $languages): self
    {
        $this->setHeader('X-Tika-OCRLanguage', implode('+', $languages));

        return $this;
    }

    /**
     * Get the timeout value for cURL
     */
    public function getTimeout(): int
    {
        return $this->getOption(CURLOPT_TIMEOUT);
    }

    /**
     * Set the timeout value for cURL
     *
     * @throws \Exception
     */
    public function setTimeout(int $value): self
    {
        $this->setOption(CURLOPT_TIMEOUT, (int) $value);

        return $this;
    }

    /**
     * Returns the supported MIME types
     *
     * @throws \Exception
     */
    public function getSupportedMIMETypes(): array
    {
        $mimeTypes = json_decode($this->request('mime-types'), true);

        ksort($mimeTypes);

        return $mimeTypes;
    }

    /**
     * Returns the available detectors
     *
     * @throws \Exception
     */
    public function getAvailableDetectors(): array
    {
        $detectors = [json_decode($this->request('detectors'), true)];

        foreach($detectors as $index => $parent)
        {
            $detectors[$parent['name']] = $parent;

            if(isset($parent['children']))
            {
                foreach($parent['children'] as $subindex => $child)
                {
                    $detectors[$parent['name']]['children'][$child['name']] = $child;

                    unset($detectors[$parent['name']]['children'][$subindex]);
                }
            }

            unset($detectors[$index]);
        }

        return $detectors;
    }

    /**
     * Returns the available parsers
     *
     * @throws \Exception
     */
    public function getAvailableParsers(): array
    {
        $parsers = [json_decode($this->request('parsers'), true)];

        foreach($parsers as $index => $parent)
        {
            $parsers[$parent['name']] = $parent;

            if(isset($parent['children']))
            {
                foreach($parent['children'] as $subindex => $child)
                {
                    $parsers[$parent['name']]['children'][$child['name']] = $child;

                    unset($parsers[$parent['name']]['children'][$subindex]);
                }
            }

            unset($parsers[$index]);
        }

        return $parsers;
    }

    /**
     * Check if server is running
     *
     * @throws \Exception
     */
    public function check(): void
    {
        if($this->isChecked() === false)
        {
            $this->setChecked(true);

            // throws an exception if server is unreachable or can't connect
            $this->request('version');
        }
    }

    /**
     * Configure, make a request and return its results
     *
     * @throws \Exception
     */
    public function request(string $type, string $file = null): string
    {
        static $retries = [];

        // check if not checked
        $this->check();

        // check if is cached
        if($file !== null && $this->isCached($type, $file))
        {
            return $this->getCachedResponse($type, $file);
        }
        elseif($file !== null && !isset($retries[sha1($file)]))
        {
            $retries[sha1($file)] = $this->retries;
        }

        // parameters for cURL request
        [$resource, $headers] = $this->getParameters($type, $file);

        // check the request
        $file = $this->checkRequest($type, $file);

        // cURL options
        $options = $this->getCurlOptions($type, $file);

        // sets headers
        foreach($headers as $header)
        {
            $options[CURLOPT_HTTPHEADER][] = $header;
        }

        // cURL init and options
        $options[CURLOPT_URL] = $this->getUrl() . "/$resource";

        // get the response and the HTTP status code
        [$response, $status] = $this->exec($options);

        // reduce memory usage closing cURL resource
        if(isset($options[CURLOPT_INFILE]) && is_resource($options[CURLOPT_INFILE]))
        {
            fclose($options[CURLOPT_INFILE]);
        }

        // request completed successfully
        if($status == 200)
        {
            // cache certain responses
            if($file !== null && $this->isCacheable($type))
            {
                $this->cacheResponse($type, $response, $file);
            }
        } // request completed successfully but result is empty
        elseif($status == 204)
        {
            $response = null;
        } // retry on request failed with error 500
        elseif($status == 500 && $file !== null && $retries[sha1($file)]--)
        {
            $response = $this->request($type, $file);
        } // other status code is an error
        else
        {
            $this->error($status, $resource, $file);
        }

        return $this->filterResponse($response);
    }

    /**
     * Make a request to Apache Tika Server
     *
     * @throws \Exception
     */
    protected function exec(array $options = []): array
    {
        // cURL init and options
        $curl = curl_init();

        // we avoid curl_setopt_array($curl, $options) because strange Windows behaviour (issue #8)
        foreach($options as $option => $value)
        {
            curl_setopt($curl, $option, $value);
        }

        // make the request directly
        if(is_null($this->callback))
        {
            $this->response = (string) curl_exec($curl);
        }
        // with a callback, the response is appended on each block inside the callback
        else
        {
            $this->response = '';
            curl_exec($curl);
        }

        // exception if cURL fails
        if(curl_errno($curl))
        {
            throw new Exception(curl_error($curl), curl_errno($curl));
        }

        // return the response and the status code
        return [trim($this->response), curl_getinfo($curl, CURLINFO_HTTP_CODE)];
    }

    /**
     * Throws an exception for an error status code
     *
     * @throws \Exception
     */
    protected function error(int $status, string $resource, string $file = null): void
    {
        switch($status)
        {
            //  method not allowed
            case 405:
                $message = 'Method not allowed';
                break;

            //  unsupported media type
            case 415:
                $message = 'Unsupported media type';
                break;

            //  unprocessable entity
            case 422:
                $message = 'Unprocessable document';

                // using remote files require Tika server to be launched with specific options
                if($this->downloadRemote === false && $file !== null && preg_match('/^http/', $file))
                {
                    $message .= ' (is server launched using "-enableUnsecureFeatures -enableFileUrl" arguments?)';
                }

                break;

            // server error
            case 500:
                $message = 'Error while processing document';
                break;

            // unexpected
            default:
                $message = "Unexpected response for /$resource ($status)";
                $status = 501;
        }

        throw new Exception($message, $status);
    }

    /**
     * Get the parameters to make the request
     *
     * @link https://wiki.apache.org/tika/TikaJAXRS#Specifying_a_URL_Instead_of_Putting_Bytes
     * @throws \Exception
     */
    protected function getParameters(string $type, string $file = null): array
    {
        $headers = [];
        $callback = null;

        if(!empty($file) && preg_match('/^http/', $file))
        {
            $headers[] = "fileUrl:$file";
        }

        switch($type)
        {
            case 'html':
                $resource = 'tika';
                $headers[] = 'Accept: text/html';
                break;

            case 'lang':
                $resource = 'language/stream';
                break;

            case 'mime':
                $resource = 'detect/stream';

                if($file !== null)
                {
                    $name = basename($file);
                    $headers[] = "Content-Disposition: attachment, filename=$name";
                }
                break;

            case 'detectors':
            case 'parsers':
            case 'meta':
            case 'mime-types':
            case 'rmeta/html':
            case 'rmeta/ignore':
            case 'rmeta/text':
                $resource = $type;
                $headers[] = 'Accept: application/json';
                $callback = function($response)
                {
                    return json_decode($response, true);
                };
                break;

            case 'text':
                $resource = 'tika';
                $headers[] = 'Accept: text/plain';
                break;

            case 'text-main':
                $resource = 'tika/main';
                $headers[] = 'Accept: text/plain';
                break;

            case 'version':
                $resource = $type;
                break;

            case 'xhtml':
                throw new Exception("Tika Server does not support XHTML output");

            default:
                throw new Exception("Unknown type $type");
        }

        return [$resource, $headers, $callback];
    }

    /**
     * Get the cURL options
     *
     * @throws \Exception
     */
    protected function getCurlOptions(string $type, string $file = null): array
    {
        // base options
        $options = $this->options;

        // callback
        if(!is_null($this->callback))
        {
            $callback = $this->callback;

            $options[CURLOPT_WRITEFUNCTION] = function($handler, $data) use ($callback)
            {
                if($this->callbackAppend === true)
                {
                    $this->response .= $data;
                }

                $callback($data);

                // safe because cURL must receive the number of *bytes* written
                return strlen($data);
            };
        }

        // remote file options
        if($file && preg_match('/^http/', $file))
        {
            //
        } // local file options
        elseif($file && file_exists($file) && is_readable($file))
        {
            $options[CURLOPT_INFILE] = fopen($file, 'r');
            $options[CURLOPT_INFILESIZE] = filesize($file);
        } // other options for specific requests
        elseif(in_array($type, ['detectors', 'mime-types', 'parsers', 'version']))
        {
            $options[CURLOPT_PUT] = false;
        } // file not accesible
        else
        {
            throw new Exception("File $file can't be opened");
        }

        return $options;
    }
}
