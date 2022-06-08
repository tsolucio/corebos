<?php

namespace Vaites\ApacheTika\Tests;

use Vaites\ApacheTika\Client;

/**
 * Tests for web mode
 */
class WebTest extends BaseTest
{
    protected static $process = null;

    /**
     * Start Tika server and create shared instance of clients
     */
    public static function setUpBeforeClass(): void
    {
        self::$client = Client::make('localhost', 9998, [CURLOPT_TIMEOUT => 30]);
    }

    /**
     * OCR language test
     */
    public function testHttpHeader(): void
    {
        $client = Client::make('localhost', 9998)->setHeader('Foo', 'bar');

        $this->assertEquals('bar', $client->getHeader('foo'));
    }

    /**
     * OCR language test
     */
    public function testOCRLanguage(): void
    {
        $client = Client::make('localhost', 9998)->setOCRLanguage('spa');

        $this->assertEquals(['spa'], $client->getOCRLanguages());
    }

    /**
     * OCR languages test
     */
    public function testOCRLanguages(): void
    {
        $client = Client::make('localhost', 9998)->setOCRLanguages(['fra', 'spa']);

        $this->assertEquals(['fra', 'spa'], $client->getOCRLanguages());
    }

    /**
     * cURL multiple options test
     */
    public function testCurlOptions(): void
    {
        $client = Client::make('localhost', 9998, [CURLOPT_TIMEOUT => 3]);
        $options = $client->getOptions();

        $this->assertEquals(3, $options[CURLOPT_TIMEOUT]);
    }

    /**
     * cURL single option test
     */
    public function testCurlSingleOption(): void
    {
        $client = Client::make('localhost', 9998)->setOption(CURLOPT_TIMEOUT, 3);

        $this->assertEquals(3, $client->getOption(CURLOPT_TIMEOUT));
    }

    /**
     * cURL timeout option test
     */
    public function testCurlTimeoutOption(): void
    {
        $client = Client::make('localhost', 9998)->setTimeout(3);

        $this->assertEquals(3, $client->getTimeout());
    }

    /**
     * cURL headers test
     */
    public function testCurlHeaders(): void
    {
        $header = 'Content-Type: image/jpeg';

        $client = Client::make('localhost', 9998, [CURLOPT_HTTPHEADER => [$header]]);
        $options = $client->getOptions();

        $this->assertContains($header, $options[CURLOPT_HTTPHEADER]);
    }

    /**
     * Set host test
     */
    public function testSetHost(): void
    {
        $client = Client::make('localhost', 9998);
        $client->setHost('127.0.0.1');

        $this->assertEquals('127.0.0.1', $client->getHost());
    }

    /**
     * Set port test
     */
    public function testSetPort(): void
    {
        $client = Client::make('localhost', 9998);
        $client->setPort(9997);

        $this->assertEquals(9997, $client->getPort());
    }

    /**
     * Set url host test
     */
    public function testSetUrlHost(): void
    {
        $client = Client::make('http://localhost:9998');

        $this->assertEquals('localhost', $client->getHost());
    }

    /**
     * Set url port test
     */
    public function testSetUrlPort(): void
    {
        $client = Client::make('http://localhost:9998');

        $this->assertEquals(9998, $client->getPort());
    }

    /**
     * Set retries test
     */
    public function testSetRetries(): void
    {
        $client = Client::make('localhost', 9998);
        $client->setRetries(5);

        $this->assertEquals(5, $client->getRetries());
    }

    /**
     * Test delayed check
     */
    public function testDelayedCheck(): void
    {
        $client = Client::prepare('localhost', 9997);
        $client->setPort(9998);

        $this->assertStringContainsString(self::$version, $client->getVersion());
    }
}