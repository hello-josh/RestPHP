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

/**
 * Router
 *
 * Handles routing of URLs to the proper resource classes
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Router
{
    /**
     *
     * @var \RestPHP\Config
     */
    protected $config;

    /**
     * The requested resource
     *
     * @var string
     */
    protected $requestedResource;

    /**
     * Additional arguments for the resource
     *
     * @var array
     */
    protected $routeArguments = array();

    /**
     * Creates the instance
     *
     * @param \RestPHP\Config $config
     */
    public function __construct(\RestPHP\Config $config = null)
    {
        if ($config) {
            $this->setConfig($config);
        }
    }

    /**
     * Gets the current config
     *
     * @return \RestPHP\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the current config
     *
     * @param \RestPHP\Config $config
     */
    public function setConfig(\RestPHP\Config $config)
    {
        $this->config = $config;
    }

    /**
     * Creates a valid
     *
     * @param string $requestUri The requested URI
     */
    public function route($requestUri = null)
    {
        $requestUri = parse_url($requestUri, PHP_URL_PATH);

        $baseUrl = $this->getConfig()->get('baseUrl', '');

        if (strpos($requestUri, $baseUrl) === 0) {
            $requestUri = substr($requestUri, 0, strlen($baseUrl));
        }

        $this->parsePath($requestUri);
    }

    /**
     * Parses a given URI into it's requested resource and arguments
     *
     * @param string $path
     * @return array Contains the keys 'resource' for the resource requested
     *               and 'arguments' for the URL args
     */
    protected function parsePath($path)
    {
        $parts = explode('/', ltrim($path, '/'));

        if (!isset($parts[0])) {
            throw new \InvalidArgumentException('No parts found in ' . $path);
        }

        $this->setRequestedResource(array_shift($parts));

        if (count($parts)) {
            $this->setRouteArguments($parts);
        }
    }

    public function getRequestedResource()
    {
        return $this->requestedResource;
    }

    public function setRequestedResource($requestedResource)
    {
        $this->requestedResource = $requestedResource;
    }

    public function getRouteArguments()
    {
        return $this->routeArguments;
    }

    public function setRouteArguments(array $routeArguments)
    {
        $this->routeArguments = $routeArguments;
    }


}