<?php

namespace Vaites\ApacheTika\Clients;

use Exception;
use ZipArchive;

use Vaites\ApacheTika\Client;

/**
 * Apache Tika command line interface client
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 * @link    https://tika.apache.org/1.23/gettingstarted.html#Using_Tika_as_a_command_line_utility
 */
class CLIClient extends Client
{
    protected const MODE = 'cli';

    /**
     * Apache Tika app path
     *
     * @var string
     */
    protected $path = null;

    /**
     * Java binary path
     *
     * @var string
     */
    protected $java = null;

    /**
     * Java arguments
     *
     * @var string
     */
    protected $javaArgs = null;

    /**
     * Environment variables
     *
     * @var array
     */
    protected $envVars = [];

    /**
     * Configure client
     *
     * @throws \Exception
     */
    public function __construct(string $path = null, string $java = null, bool $check = true)
    {
        parent::__construct();

        if($path)
        {
            $this->setPath($path);
        }

        if($java)
        {
            $this->setJava($java);
        }

        if($check === true)
        {
            $this->check();
        }
    }

    /**
     * Get the path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set the path
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the Java path
     */
    public function getJava(): ?string
    {
        return $this->java;
    }

    /**
     * Set the Java path
     */
    public function setJava(string $java): self
    {
        $this->java = $java;

        return $this;
    }

    /**
     * Get the Java arguments
     */
    public function getJavaArgs(): ?string
    {
        return $this->javaArgs;
    }

    /**
     * Set the Java arguments
     *
     * NOTE: to modify child process jvm args, prepend "J" to each argument (-JXmx4g)
     */
    public function setJavaArgs(string $args): self
    {
        $this->javaArgs = $args;

        return $this;
    }

    /**
     * Get the environment variables
     */
    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
     * Set the environment variables
     */
    public function setEnvVars(array $variables): self
    {
        $this->envVars = $variables;

        return $this;
    }

    /**
     * Returns current Tika version
     *
     * @throws \Exception
     */
    public function getVersion(): string
    {
        $manifest = [];

        if(class_exists(ZipArchive::class) && file_exists($this->path))
        {
            $zip = new ZipArchive();

            if($zip->open($this->path))
            {
                if(preg_match_all('/(.+):\s+(.+)\r?\n/U', $zip->getFromName('META-INF/MANIFEST.MF'), $match))
                {
                    foreach($match[1] as $index => $key)
                    {
                        $manifest[$key] = $match[2][$index];
                    }
                }
            }
        }

        return $manifest['Implementation-Version'] ?? $this->request('version');
    }

    /**
     * Returns the supported MIME types
     *
     * NOTE: the data provided by the CLI must be parsed: mime type has no spaces, aliases go next prefixed with spaces
     *
     * @throws \Exception
     */
    public function getSupportedMIMETypes(): array
    {
        $mime = null;
        $mimeTypes = [];

        $response = preg_split("/\n/", $this->request('mime-types')) ?: [];

        foreach($response as $line)
        {
            if(preg_match('/^\w+/', $line))
            {
                $mime = trim($line);
                $mimeTypes[$mime] = ['alias' => []];
            }
            else
            {
                [$key, $value] = preg_split('/:\s+/', trim($line));

                if($key == 'alias')
                {
                    $mimeTypes[$mime]['alias'][] = $value;
                }
                else
                {
                    $mimeTypes[$mime][$key] = $value;
                }
            }
        }


        return $mimeTypes;
    }

    /**
     * Returns the available detectors
     *
     * @throws \Exception
     */
    public function getAvailableDetectors(): array
    {
        $detectors = [];

        $split = preg_split("/\n/", $this->request('detectors')) ?: [];

        $parent = null;
        foreach($split as $line)
        {
            if(preg_match('/composite/i', $line))
            {
                $parent = trim(preg_replace('/\(.+\):/', '', $line) ?: '');
                $detectors[$parent] = ['children' => [], 'composite' => true, 'name' => $parent];
            }
            else
            {
                $child = trim($line);
                $detectors[$parent]['children'][$child] = ['composite' => false, 'name' => $child];
            }
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
        $parsers = [];

        $split = preg_split("/\n/", $this->request('parsers')) ?: [];
        array_shift($split);

        $parent = null;
        foreach($split as $line)
        {
            if(preg_match('/composite/i', $line))
            {
                $parent = trim(preg_replace('/\(.+\):/', '', $line) ?: '');

                $parsers[$parent] = ['children' => [], 'composite' => true, 'name' => $parent, 'decorated' => false];
            }
            else
            {
                $child = trim($line);

                $parsers[$parent]['children'][$child] = ['composite' => false, 'name' => $child, 'decorated' => false];
            }
        }

        return $parsers;
    }

    /**
     * Check Java binary, JAR path or server connection
     *
     * @throws \Exception
     */
    public function check(): void
    {
        if($this->isChecked() === false)
        {
            // Java command must not return an error
            try
            {
                $this->exec(($this->java ?: 'java') . ' -version');
            }
            catch(Exception $exception)
            {
                throw new Exception('Java command not found');
            }

            // JAR path must exists
            if(file_exists($this->path) === false)
            {
                throw new Exception('Apache Tika app JAR not found');
            }

            $this->setChecked(true);
        }
    }

    /**
     * Configure and make a request and return its results
     *
     * @throws \Exception
     */
    public function request(string $type, string $file = null): string
    {
        // check if not checked
        $this->check();

        // check if is cached
        if($file !== null && $this->isCached($type, $file))
        {
            return $this->getCachedResponse($type, $file);
        }

        // command arguments
        $arguments = $this->getArguments($type, $file);

        // check the request
        $file = $this->checkRequest($type, $file);

        // add last argument
        if($file)
        {
            $arguments[] = escapeshellarg($file);
        }

        // build command
        $jar = escapeshellarg($this->path);
        $command = trim(($this->java ?: 'java') . " -jar $jar " . implode(' ', $arguments) . " {$this->javaArgs}");

        // run command
        $response = $this->exec($command);

        // error if command fails
        if($response === null)
        {
            throw new Exception('An error occurred running Java command');
        }

        // metadata response
        if($file !== null && in_array(preg_replace('/\/.+/', '', $type), ['meta', 'rmeta']))
        {
            // fix for invalid? json returned only with images
            $response = str_replace(basename($file) . '"}{', '", ', $response);

            // on Windows, response must be encoded to UTF8
            if(version_compare($this->getVersion(), '2.1.0', '<'))
            {
                $response = $this->platform == 'win' ? utf8_encode($response) : $response;
            }
        }

        // cache certain responses
        if($file !== null && $this->isCacheable($type))
        {
            $this->cacheResponse($type, $response, $file);
        }

        return $this->filterResponse($response);
    }

    /**
     * Run the command and return its results
     *
     * @throws \Exception
     */
    public function exec(string $command): ?string
    {
        // get env variables for proc_open()
        $env = empty($this->envVars) ? null : array_merge(getenv(), $this->envVars);

        // run command
        $exit = -1;
        $logfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tika-error.log';
        $descriptors = [['pipe', 'r'], ['pipe', 'w'], ['file', $logfile, 'a']];
        $process = proc_open($command, $descriptors, $pipes, null, $env);
        $callback = $this->callback;

        // get output if command runs ok
        if(is_resource($process))
        {
            fclose($pipes[0]);
            $this->response = '';
            while($chunk = stream_get_line($pipes[1], $this->chunkSize))
            {
                if(!is_null($callback))
                {
                    $callback($chunk);
                }

                if($this->callbackAppend === true)
                {
                    $this->response .= $chunk;
                }
            }
            fclose($pipes[1]);
            $exit = proc_close($process);
        }

        // exception if exit value is not zero
        if($exit > 0)
        {
            throw new Exception("Unexpected exit value ($exit) for command $command");
        }

        return $this->filterResponse($this->response);
    }

    /**
     * Get the arguments to run the command
     *
     * @throws  Exception
     */
    protected function getArguments(string $type, string $file = null): array
    {
        $arguments = $this->encoding ? ["--encoding={$this->encoding}"] : [];

        switch($type)
        {
            case 'html':
                $arguments[] = '--html';
                break;

            case 'lang':
                $arguments[] = '--language';
                break;

            case 'mime':
                $arguments[] = '--detect';
                break;

            case 'meta':
                $arguments[] = '--metadata --json';
                break;

            case 'text':
                $arguments[] = '--text';
                break;

            case 'text-main':
                $arguments[] = '--text-main';
                break;

            case 'mime-types':
                $arguments[] = '--list-supported-types';
                break;

            case 'detectors':
                $arguments[] = '--list-detectors';
                break;

            case 'parsers':
                $arguments[] = '--list-parsers';
                break;

            case 'version':
                $arguments[] = '--version';
                break;

            case 'rmeta/ignore':
                $arguments[] = '--metadata --jsonRecursive';
                break;

            case 'rmeta/html':
                $arguments[] = '--html --jsonRecursive';
                break;

            case 'rmeta/text':
                $arguments[] = '--text --jsonRecursive';
                break;

            case 'xhtml':
                $arguments[] = '--xml';
                break;

            default:
                throw new Exception($file ? "Unknown type $type for $file" : "Unknown type $type");
        }

        return $arguments;
    }
}
