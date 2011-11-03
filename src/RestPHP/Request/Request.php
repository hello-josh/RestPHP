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
/**
 * @namespace
 */

namespace RestPHP\Request;

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
        'Warning' => null
    );

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
     * Gets the specified HTTP header
     *
     * @param string $header HTTP Header requested
     *
     * @return string|null
     */
    public function getHeader($header)
    {
        $header = 'HTTP_' . strtoupper(str_replace('-', '_', $header));

        if (array_key_exists($header, $_SERVER)) {
            return $_SERVER[$header];
        }

        return null;
    }

    public function setHeader($header, $value)
    {
        if (!array_key_exists($header, $this->headers)
            && strpos($header, 'X-') !== 0) {

            throw new \InvalidArgumentException(
                    'Non-standard headers must be prefixed with an X- per HTTP spec');
        }
        $this->headers[$header] = $value;
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

            $this->body = false;

            $body = file_get_contents('php://input');

            if (strlen($body)) {
                $this->body = $body;
            }
        }

        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

}
