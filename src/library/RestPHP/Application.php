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

use \RestPHP\Request\Request,
    \RestPHP\Response\Response;

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
     * Request Instance
     *
     * @var \RestPHP\Request\Request
     */
    protected $request;

    /**
     * Response Instance
     *
     * @var \RestPHP\Response\Response
     */
    protected $response;

    /**
     * @var \RestPHP\Router
     */
    protected $router;

    /**
     * @var \RestPHP\Dispatcher
     */
    protected $dispatcher;

    /**
     * Loaded and configured components
     * @var array
     */
    protected $components = array();

    /**
     * Creates the application
     *
     * @param string|\RestPHP\Environment $environment
     * @param string|\RestPHP\Config $config
     */
    public function __construct($environment, $config) {
        ob_start();
        if (is_string($environment)) {
            $environment = new \RestPHP\Environment($environment);
        }

        $this->setEnvironment($environment);

        if (is_string($config)) {
            $config = new \RestPHP\Config($config, $environment);
        }

        $this->setConfig($config);
    }

    /**
     * Gets the Environment
     *
     * @return Environment
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * Sets the environment
     *
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment) {
        $this->environment = $environment;
    }

    /**
     * Gets the current config
     *
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Sets the current config
     *
     * @param Config $config
     */
    public function setConfig(Config $config) {
        $this->config = $config;

        if ($config->application->resources->basePath) {

            Autoloader::getInstance()->addIncludePath(
                $config->application->resources->basePath);
        }
    }

    /**
     * @return Request
     */
    public function getRequest() {
        if (null === $this->request) {
            $this->request = Request::getDefaultRequest($this->getConfig());
        }

        return $this->request;
    }

    /**
     *
     * @param \RestPHP\Request\Request $request
     */
    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * @return \RestPHP\Response\Response
     */
    public function getResponse() {
        if (!isset($this->response)) {
            $this->response = new \RestPHP\Response\Response($this->getRequest());
        }

        return $this->response;
    }

    /**
     * @param \RestPHP\Response\Response $response
     */
    public function setResponse(Response $response) {
        $this->response = $response;
    }

    /**
     * @return \RestPHP\Router
     */
    public function getRouter() {
        if (null === $this->router) {
            $this->router = new Router($this->getConfig());
        }
        return $this->router;
    }

    /**
     * @param \RestPHP\Router $router
     */
    public function setRouter(Router $router) {
        $this->router = $router;
    }

    /**
     * @return \RestPHP\Dispatcher
     */
    public function getDispatcher() {
        if (null === $this->dispatcher) {
            $this->dispatcher = new Dispatcher($this->getConfig());
        }
        return $this->dispatcher;
    }

    /**
     * @param \RestPHP\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Gets an initialized component from application.components.basePath/$name.php
     *
     * @param string $name
     * @return mixed
     * @throws \RestPHP\Error\Exception
     */
    public function getComponent($name) {

        $this->componentNameSecurityCheck($name);

        if (!isset($this->components[$name])) {

            $path = $this->getConfig()->application->components->basePath;
            $path .= DIRECTORY_SEPARATOR . strtolower($name).'.php';

            if (!file_exists($path)) {
                throw new \RestPHP\Error\Exception("Component $name does not exist in $path");
            }

            include_once $path;
        }

        return $this->components[$name];
    }

    /**
     *
     * @param string $name
     * @param mixed $component
     */
    public function setComponent($name, $component) {
        $this->componentNameSecurityCheck($name);
        $this->components[$name] = $component;
    }

    /**
     * Dispatches the request and returns the response
     *
     * @return \RestPHP\Response\Response
     */
    public function handle() {

        try {

            $request = $this->getRequest();
            $router = $this->getRouter();

            $resource = $router->route($request);

            $resource->setRequest($request);
            $resource->setResponse($this->getResponse());
            $resource->setApplication($this);

            $dispatcher = $this->getDispatcher();

            $response = $dispatcher->dispatch($resource);

        } catch (\RestPHP\Resource\ResourceNotFoundException $e) {

            $response = new Response($this->getRequest());
            $response->setStatus(Response::HTTP_404);
            $response->message = 'The requested resource could not be found';

        } catch (\Exception $e) {

            // return ErrorResponse?
            $response = new \RestPHP\Response\ErrorResponse($this->getRequest(), $e);
        }

        if ($this->getEnvironment()->getEnv() == Environment::DEVELOPMENT) {
            if ($response instanceof \RestPHP\Response\ErrorResponse) {
                $response->exception = $response->getException()->getMessage();
            }
        }

        $this->setResponse($response);

        return $response;
    }

    /**
     * Security check for component name since it will be used in include
     *
     * @param string $name
     * @throws \RestPHP\Error\Exception
     */
    protected function componentNameSecurityCheck($name) {
        // security check
        if (!ctype_alnum($name)) {
            throw new \RestPHP\Error\Exception("Component $name contains invalid characters");
        }
    }
}