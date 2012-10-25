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
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */

namespace RestPHP\Request;

use \RestPHP\Config,
    \RestPHP\Request\Header\HeaderFactory,
    \RestPHP\Request\Unmarshaller\UnmarshallerFactory,
    \RestPHP\Request\Unmarshaller\IUnmarshaller;

/**
 * Request
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Request
{
    /**
     * Convenience method for instantiating a \RestPHP\Request\Request based
     * off of PHP's $_SERVER array when run via mod_php or fcgi
     *
     * @param \RestPHP\Config $config
     * @return \RestPHP\Request\Request
     */
    public static function getDefaultRequest(Config $config = null)
    {
        $request = new static($config);

        foreach ($_SERVER as $header => $value) {
            if (strpos($header, 'HTTP_') === 0) {
                $header = substr($header, 5);
                $header = static::formatHeader($header);
                $request->setHeader($header, $value);

            } else {

                switch ($header) {

                    case 'CONTENT_TYPE':
                    case 'CONTENT_LENGTH':
                        $header = static::formatHeader($header);
                        $request->setHeader($header, $value);
                        break;
                }
            }
        }

        $request->setHttpMethod($_SERVER['REQUEST_METHOD']);

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($config) {

            $baseUri = $config->application->baseUri;

            if (strlen($baseUri) && strpos($requestUri, $baseUri) === 0) {
                $requestUri = substr($requestUri, strlen($baseUri));
            }
        }

        $request->setRequestUri($requestUri);

        return $request;
    }

    protected static function formatHeader($header) {
        $header = strtolower($header);
        $header = explode('_', $header);
        $header = array_map('ucfirst', $header);
        $header = implode('-', $header);
        return $header;
    }

    /**
     * Raw body of the HTTP request
     *
     * @var string
     */
    protected $body;

    /**
     * The URI requested
     *
     * @var string
     */
    protected $requestUri;

    /**
     * HTTP Headers of the request
     *
     * @todo Figure out if I need to store an array of standard headers or not
     * @var array
     */
    protected $headers = array(
        'Accept' => null,
        'Accept-Charset' => null,
        'Accept-Encoding' => null,
        'Accept-Language' => null,
        'Authorization' => null,
        'Cache-Control' => null,
        'Connection' => null,
        'Cookie' => null,
        'Content-Length' => null,
        'Content-MD5' => null,
        'Content-Type' => null,
        'Date' => null,
        'Expect' => null,
        'From' => null,
        'Host' => null,
        'If-Match' => null,
        'If-Modified-Since' => null,
        'If-None-Match' => null,
        'If-Range' => null,
        'If-Unmodified-Since' => null,
        'Max-Forwards' => null,
        'Pragma' => null,
        'Proxy-Authorization' => null,
        'Range' => null,
        'Referer' => null,
        'TE' => null,
        'Upgrade' => null,
        'User-Agent' => null,
        'Via' => null,
        'Warning' => null,
        'Origin' => null
     );

    /**
     *
     * @var string
     */
    protected $httpMethod;

    /**
     *
     * @var \RestPHP\Config
     */
    protected $config;

    /**
     *
     * @var \RestPHP\Request\Unmarshaller\IUnmarshaller
     */
    protected $unmarshaller;

    /**
     * Creates the instance
     *
     * @param \RestPHP\Config $config
     */
    public function __construct(Config $config = null)
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
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Gets the HTTP request method
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * Sets the HTTP request method
     *
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = strtoupper($httpMethod);
    }

    /**
     * Gets the requested URI
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * Sets the requested URI
     *
     * @param string $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
        return $this;
    }

    /**
     * Gets the unmarshaller
     *
     * @return \RestPHP\Request\Unmarshaller\IUnmarshaller
     */
    public function getUnmarshaller() {

        if (null === $this->unmarshaller) {
            $this->unmarshaller = UnmarshallerFactory::factory($this->getHeader('Content-Type'));
        }

        return $this->unmarshaller;
    }

    /**
     * Sets the unmarshaller
     *
     * @param \RestPHP\Request\Unmarshaller\IUnmarshaller $unmarshaller
     */
    public function setUnmarshaller(IUnmarshaller $unmarshaller) {
        $this->unmarshaller = $unmarshaller;
    }

    /**
     * Gets the specified HTTP header
     *
     * @param string $header HTTP Header requested
     * @return RestPHP\Request\Header\IHeader or NULL if not set
     */
    public function getHeader($header)
    {
        if (array_key_exists($header, $this->headers)) {
            return $this->headers[$header];
        }

        return null;
    }

    /**
     * Sets the HTTP header
     *
     * @param string $header
     * @param string $value
     * @param boolean $allowMultiple
     * @return \RestPHP\Request\Request
     * @throws \InvalidArgumentException
     */
    public function setHeader($header, $value, $allowMultiple = false)
    {
        /* @var $headerObj \RestPHP\Request\Header\IHeader */
        $headerObj = HeaderFactory::factory($header);

        $headerObj->parse($value);

        if ($allowMultiple) {
            if (!isset($this->headers[$header])) {
                $this->headers[$header] = array();
            }

            if (!is_array($this->headers[$header])) {
                $this->headers[$header] = array($this->headers[$header]);
            }

            $this->headers[$header][] = $headerObj;
        }
        else {
            $this->headers[$header] = $headerObj;
        }

        return $this;
    }

    /**
     * Gets the raw body of the HTTP request
     *
     * @return string|false Raw body, or false if not present
     */
    public function getBody()
    {
        if ($this->body === null) {


            switch ($this->getHttpMethod()) {

                // request body is only allowed for
                // the following http methods
                case 'POST':
                case 'PUT':
                case 'DELETE':

                    $body = file_get_contents('php://input');
                    $body = $this->getUnmarshaller()->unmarshall($body);
                    break;

                default:
                    $body = array();
                    break;
            }

            $this->body = $body;
        }

        return $this->body;
    }

    /**
     * Sets the request body
     *
     * @param string $body
     * @return \RestPHP\Request\Request
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Gets the named param from the query string or from the unmarshalled body
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null) {

        switch ($this->getHttpMethod()) {

            case 'GET':
            case 'DELETE':

                if (array_key_exists($name, $_GET)) {
                    return $_GET[$name];
                }
                return $default;

                break;

            case 'POST':
            case 'PUT':

                // priority is request body, then query string
                if (array_key_exists($name, $this->getBody())) {
                    $body = $this->getBody();
                    return $body[$name];
                } elseif (array_key_exists($name, $_GET)) {
                    return $_GET[$name];
                } else {
                    return $default;
                }

                break;

        }
    }

    public function getParams() {
        return array_merge($_GET, $this->getBody());
    }
}
