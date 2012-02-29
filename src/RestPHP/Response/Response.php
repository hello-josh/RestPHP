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
 * @subpackage Response
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */

/**
 * @namespace
 */
namespace RestPHP\Response;

/**
 * Response
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Response
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Response
{
    /**
     * Is this FCGI?
     *
     * @var boolean
     */
    protected $isFgci = false;
    /**
     * HTTP Status
     *
     * Default status is 200 OK
     *
     * @var integer
     */
    protected $status = 200;
    /**
     * Headers that need to be sent
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Data that will be outputted via the API
     *
     * @var array
     */
    protected $outputData = array();

    const FCGI_STATUS = 'Status: ';
    const HTTP_STATUS = 'HTTP/1.1 ';

    const HTTP_100 = "100 Continue";
    const HTTP_101 = "101 Switching Protocols";
    const HTTP_200 = "200 OK";
    const HTTP_201 = "201 Created";
    const HTTP_202 = "202 Accepted";
    const HTTP_203 = "203 Non-Authoritative Information";
    const HTTP_204 = "204 No Content";
    const HTTP_205 = "205 Reset Content";
    const HTTP_206 = "206 Partial Content";
    const HTTP_300 = "300 Multiple Choices";
    const HTTP_301 = "301 Moved Permanently";
    const HTTP_302 = "302 Found";
    const HTTP_303 = "303 See Other";
    const HTTP_304 = "304 Not Modified";
    const HTTP_305 = "305 Use Proxy";
    const HTTP_306 = "306 (Unused)";
    const HTTP_307 = "307 Temporary Redirect";
    const HTTP_400 = "400 Bad Request";
    const HTTP_401 = "401 Unauthorized";
    const HTTP_402 = "402 Payment Required";
    const HTTP_403 = "403 Forbidden";
    const HTTP_404 = "404 Not Found";
    const HTTP_405 = "405 Method Not Allowed";
    const HTTP_406 = "406 Not Acceptable";
    const HTTP_407 = "407 Proxy Authentication Required";
    const HTTP_408 = "408 Request Timeout";
    const HTTP_409 = "409 Conflict";
    const HTTP_410 = "410 Gone";
    const HTTP_411 = "411 Length Required";
    const HTTP_412 = "412 Precondition Failed";
    const HTTP_413 = "413 Request Entity Too Large";
    const HTTP_414 = "414 Request-URI Too Long";
    const HTTP_415 = "415 Unsupported Media Type";
    const HTTP_416 = "416 Requested Range Not Satisfiable";
    const HTTP_417 = "417 Expectation Failed";
    const HTTP_500 = "500 Internal Server Error";
    const HTTP_501 = "501 Not Implemented";
    const HTTP_502 = "502 Bad Gateway";
    const HTTP_503 = "503 Service Unavailable";
    const HTTP_504 = "504 Gateway Timeout";
    const HTTP_505 = "505 HTTP Version Not Supported";

    /**
     * Sets data to be output via the API
     *
     * @param string $name
     * @param mixed $value
     */
    public function setData($name, $value)
    {
        $this->outputData[$name] = $value;
    }

    /**
     * Sets data to be output via the API
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        return $this->setData($name, $value);
    }

    /**
     * Gets the data point that will be outputted via the API
     *
     * @param string $name
     * @return mixed
     */
    public function &getData($name)
    {
        return $this->outputData[$name];
    }

    /**
     * Gets the data point that will be outputted via the API
     *
     * @param string $name
     * @return mixed
     */
    public function &__get($name)
    {
        return $this->getData($name);
    }

        /**
     * Is this response handled via fcgi?
     *
     * @see \RestPHP\Response::isHttp()
     * @return boolean
     */
    public function isFgci()
    {
        return $this->isFgci;
    }

    /**
     * Sets if the response should be handled like fcgi
     *
     * @param boolean $isFgci
     */
    public function setIsFgci($isFgci)
    {
        $this->isFgci = (bool) $isFgci;
    }

    /**
     * Is this response handled via HTTP?
     *
     * @see \RestPHP\Response::isFcgi()
     * @return boolean
     */
    public function isHttp()
    {
        return!$this->isFgci();
    }

    /**
     * Sets if the response should be handled like HTTP
     *
     * @param boolean $isHttp
     */
    public function setIsHttp($isHttp)
    {
        $this->isFgci = !(bool) $isHttp;
    }

    /**
     * Gets the HTTP status code
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the HTTP status code
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;
    }

    /**
     * Adds a response header to be sent
     *
     * Arguments 2-N are imploded with '; ' to allow easier
     * setting of headers like:
     *
     * <code>
     * $response->addHeader('Content-Disposition',
     *                           'attachment', 'filename=fname.ext');
     * </code>
     *
     * Which is output as:
     * <code>
     * Content-Disposition: attachment; filename=fname.ext
     * </code>
     *
     * @param string $header
     * @param string $value
     * @param string $values variadic arguments
     */
    public function addHeader($header, $value, $values=null)
    {
        $this->headers[] = func_get_args();
    }

    /**
     * Formats the Status header for FGCI or HTTP
     *
     * @return string
     */
    public function makeStatus()
    {
        $status = ($this->isFgci ? self::FCGI_STATUS : self::HTTP_STATUS);
        $status .= constant('self::HTTP_' . $this->status);

        return $status;
    }

    /**
     * Formats a header based off an array item
     *
     * @param array $header
     * @return string
     */
    public function makeHeader(array $header)
    {
        return array_shift($header) . ': ' . implode('; ', $header);
    }

    /**
     * Creates and sends any needed headers
     */
    public function sendHeaders()
    {
        foreach ($this->headers as $header) {
            header($this->makeHeader($header));
        }

        header($this->makeStatus(), true, $this->status);
    }

    /**
     * Echos the response
     */
    public function output()
    {
        $this->sendHeaders();
        echo $this->__toString();
    }

    public function __toString()
    {
        return var_export($this->outputData, 1);
    }
}