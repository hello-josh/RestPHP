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
    \RestPHP\Response\Response,
    \RestPHP\Request\Unmarshaller\UnmarshallerFactory,
    \RestPHP\Response\Marshaller\MarshallerFactory;

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
     * Creates the application
     *
     * @param string|\RestPHP\Environment $environment
     * @param string|\RestPHP\Config $config
     */
    public function __construct($environment, $config)
    {
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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \RestPHP\Request\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \RestPHP\Response\Response
     */
    public function getResponse()
    {
        if (!isset($this->response)) {
            $this->response = new Response();
            $this->response->setStatus(Response::HTTP_404);
            $this->response->setBody('The requested resource could not be found');
        }
        return $this->response;
    }

    /**
     * @param \RestPHP\Response\Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Marshalls the response to the proper Accept type from the client
     *
     * @param \RestPHP\Response\Response $response
     * @return \RestPHP\Response\Response
     */
    public function getMarshalledResponse(Response $response)
    {
        try {

            $marshaller = MarshallerFactory::factory($this->getRequest()->getHeader('Accept'));
            $response = $marshaller->marshall($response);

        } catch (NoValidMarshallerException $e) {
            $response->setStatus(Response::HTTP_406);
            $response->setBody("The application does not know how to respond to any of your Accept types: " . $this->getRequest()->getHeader('Accept')->getRawValue());
        }

        $this->response = $response;
        return $response;
    }

    /**
     * Unmarshalls the request into an associative array
     *
     * @param \RestPHP\Request\Request $request
     * @return \RestPHP\Request\Request $request
     */
    public function getUnmarshalledRequest(Request $request)
    {
        $this->request = $request;

        switch ($request->getHttpMethod()) {

            case 'POST':
            case 'PUT':
            case 'DELETE':

                $unmarshaller = UnmarshallerFactory::factory($request->getHeader('Content-Type'));
                $request = $unmarshaller->unmarshall($request);

                break;
        }

        return $request;
    }

    /**
     * Dispatches the request and returns the response
     *
     * @param \RestPHP\Request\Request $request
     * @return \RestPHP\Response\Response
     */
    public function handle(Request $request = null)
    {
        if (null === $request) {
            $request = Request::getDefaultRequest($this->getConfig());
        }

        try {

            $request = $this->getUnmarshalledRequest($request);

            $router = new Router($this->getConfig());

            $resource = $router->route($request);

            $resource->setRequest($request);
            $resource->setResponse(new \RestPHP\Response\Response());

            $dispatcher = new Dispatcher($this->getConfig());

            $response = $dispatcher->dispatch($resource);

            return $this->getMarshalledResponse($response);
        }
        catch (\Exception $e) {

            // return ErrorResponse?
            return new \RestPHP\Response\ErrorResponse($e);
        }
    }
}