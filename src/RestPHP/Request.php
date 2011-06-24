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
 * @copyright  Copyright (c) 2011, RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */

/**
 * @namespace
 */
namespace RestPHP;

/**
 * Request
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  Copyright (c) 2011, RestPHP Framework
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

    /**
     * Parses the HTTP Accept: header
     *
     * @author Wez Furlong
     * @link   http://shiflett.org/blog/2011/may/the-accept-header#comment-7
     */
    protected function parseAcceptHeader()
    {

        $hdr = $this->headers['Accept'];

        $accept = array();

        foreach (preg_split('/\s*,\s*/', $hdr) as $i => $term) {

            $o = new \stdclass;

            $o->pos = $i;

            if (preg_match(",^(\S+)\s*;\s*(?:q|level)=([0-9\.]+),i", $term, $M)) {

                $o->type = $M[1];

                $o->q = (double) $M[2];
            }
            else {

                $o->type = $term;

                $o->q = 1;
            }

            $accept[] = $o;
        }

        usort($accept, function ($a, $b) {

            /* first tier: highest q factor wins */
            $diff = $b->q - $a->q;

            if ($diff > 0) {

                $diff = 1;
            }
            else if ($diff < 0) {

                $diff = -1;
            }
            else {

                /* tie-breaker: first listed item wins */

                $diff = $a->pos - $b->pos;
            }

            return $diff;
        });

        $this->headers['Accept'] = array();

        foreach ($accept as $a) {

            $this->headers['Accept'][$a->type] = $a->type;
        }
    }

}