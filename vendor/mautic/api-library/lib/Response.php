<?php
/**
 * @copyright   2014 Mautic, NP. All rights reserved.
 * @author      Mautic
 *
 * @see        http://mautic.org
 *
 * @license     MIT http://opensource.org/licenses/MIT
 */

namespace Mautic;

use Mautic\Exception\UnexpectedResponseFormatException;

/**
 * Class helping with API responses.
 */
class Response
{
    private $headers;
    private $body;
    private $info;

    /**
     * @param string $response
     */
    public function __construct($response, array $info)
    {
        $this->info = $info;
        $this->parseResponse($response);
        $this->validate();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getDecodedBody()
    {
        try {
            $parsed = $this->decodeFromJson();
        } catch (UnexpectedResponseFormatException $e) {
            $parsed = $this->decodeFromUrlParams();
        }

        return $parsed;
    }

    /**
     * @return array
     *
     * @throws UnexpectedResponseFormatException
     */
    public function decodeFromJson()
    {
        $parsed = json_decode($this->body, true);

        if (is_null($parsed)) {
            throw new UnexpectedResponseFormatException($this);
        }

        return $parsed;
    }

    /**
     * @return array
     *
     * @throws UnexpectedResponseFormatException
     */
    public function decodeFromUrlParams()
    {
        if (false !== strpos($this->body, '=')) {
            parse_str($this->body, $parsed);
        }

        if (empty($parsed)) {
            throw new UnexpectedResponseFormatException($this);
        }

        return $parsed;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return bool
     */
    public function isZip()
    {
        return !empty($this->info['content_type']) && 'application/zip' === $this->info['content_type'];
    }

    /**
     * @return bool
     */
    public function isHtml()
    {
        return '<' === substr(trim($this->body), 0, 1);
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function saveToFile($path)
    {
        if (!file_exists($path)) {
            if (!@mkdir($path) && !is_dir($path)) {
                throw new \Exception('Cannot create directory '.$path);
            }
        }
        $file = tempnam($path, 'mautic_api_');

        if (!is_writable($file)) {
            throw new \Exception($file.' is not writable');
        }

        if (!$handle = fopen($file, 'w')) {
            throw new \Exception('Cannot open file '.$file);
        }

        if (false === fwrite($handle, $this->body)) {
            throw new \Exception('Cannot write into file '.$file);
        }

        fclose($handle);

        return [
            'file' => $file,
        ];
    }

    /**
     * @param string $response
     */
    private function parseResponse($response)
    {
        $exploded      = explode("\r\n\r\n", $response);
        $this->body    = array_pop($exploded);
        $this->headers = implode("\r\n\r\n", $exploded);
    }

    /**
     * @throws UnexpectedResponseFormatException
     */
    private function validate()
    {
        if (!in_array($this->info['http_code'], [200, 201])) {
            $message = 'The response has unexpected status code ('.$this->info['http_code'].').';
            throw new UnexpectedResponseFormatException($this, $message, $this->info['http_code']);
        }

        if ($this->isHtml()) {
            throw new UnexpectedResponseFormatException($this);
        }
    }
}
