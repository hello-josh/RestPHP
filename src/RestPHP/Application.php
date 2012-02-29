<?php
/**
 * RestPHP Framework
 *
 * PHP Version 5.3
 *
 * Copyright (c) 2011, RestPHP Framework
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * Neither the name of the RestPHP Framework nor the names of its contributors
 * may be used to endorse or promote products derived from this software
 * without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */

/**
 * @namespace
 */
namespace RestPHP;

use \RestPHP\Request\Request;

/**
 * Application
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Application
{
    /**
     * The current environment
     *
     * @var Environment
     */
    protected $environment;

    /**
     * The application config
     *
     * @var Config
     */
    protected $config;

    /**
     * Creates the application
     *
     * @param Environment $environment
     * @param Config $config
     */
    public function __construct(Environment $environment, Config $config)
    {
        $this->setEnvironment($environment);

        $this->setConfig($config);
    }

    /**
     * Gets the Environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Sets the environment
     *
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Gets the current config
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the current config
     *
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        if ($config->application->resources->basePath) {

            Autoloader::getInstance()->addIncludePath(
                    $config->application->resources->basePath);
        }
    }

    /**
     * Dispatches the request and returns the response
     *
     * @param \RestPHP\Request\Request $request
     * @return \RestPHP\Response\Response
     */
    public function handle(\RestPHP\Request\Request $request)
    {

        $dispatcher = new Dispatcher($this->getConfig());

        $response = $dispatcher->dispatch($request);

        return $response;
    }

    /**
     * Convenience method for instantiating a \RestPHP\Request\Request based
     * off of PHP's $_SERVER array when run via
     *
     * @param \RestPHP\Config $config
     * @return \RestPHP\Request\Request
     */
    public static function getDefaultRequest(\RestPHP\Config $config = null)
    {
        $request = new \RestPHP\Request\Request($config);

        foreach ($_SERVER as $header => $value) {
            if (strpos($header, 'HTTP_') === 0) {
                $header = strtolower(substr($header, 5));
                $header = explode('_', $header);
                $header = array_map('ucfirst', $header);
                $header = implode('-', $header);
                $request->setHeader($header, $value);
            }
        }

        $request->setHttpMethod($_SERVER['REQUEST_METHOD']);

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($config) {

            $baseUri = $request->getConfig()->application->baseUri;

            if (strlen($baseUri) && strpos($requestUri, $baseUri) === 0) {
                $requestUri = substr($requestUri, strlen($baseUri));
            }
        }

        $request->setRequestUri($requestUri);

        return $request;
    }
}