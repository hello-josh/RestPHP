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

namespace RestPHP\Request\Header;

/**
 * HeaderFactory - Request header factory
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Section 14
 */
class HeaderFactory
{
    private static $XHEADER = 'XHeader';

    protected static $headers = array(
        'Accept' => 'Accept',
        'Accept-Charset' => 'AcceptCharset',
        'Accept-Encoding' => 'AcceptEncoding',
        'Accept-Language' => 'AcceptLanguage',
        'Authorization' => 'Authorization',
        'Cache-Control' => 'CacheControl',
        'Connection' => 'Connection',
        'Cookie' => 'Cookie',
        'Content-Length' => 'ContentLength',
        'Content-MD5' => 'ContentMd5',
        'Content-Type' => 'ContentType',
        'Date' => 'Date',
        'Expect' => 'Expect',
        'From' => 'From',
        'Host' => 'Host',
        'If-Match' => 'IfMatch',
        'If-Modified-Since' => 'IfModifiedSince',
        'If-None-Match' => 'IfNoneMatch',
        'If-Range' => 'IfRange',
        'If-Unmodified-Since' => 'IfUnmodifiedSince',
        'Max-Forwards' => 'MaxForwards',
        'Pragma' => 'Pragma',
        'Proxy-Authorization' => 'ProxyAuthorization',
        'Range' => 'Range',
        'Referer' => 'Referer',
        'TE' => 'TE',
        'Upgrade' => 'Upgrade',
        'User-Agent' => 'UserAgent',
        'Via' => 'Via',
        'Warning' => 'Warning',
        'Origin' => 'Origin'
    );

    /**
     * @param string $header
     * @return \RestPHP\Request\Header\IHeader
     * @throws \InvalidArgumentException
     */
    public static function factory($header) {

        if (isset(static::$headers[$header])) {
            $headerClass = static::$headers[$header];
        } elseif (strpos($header, 'X-') !== 0) {
            $headerClass = static::$XHEADER;
        } else {
            throw new \InvalidArgumentException('Unknown header: ' . $header);
        }

        $headerClass = __NAMESPACE__ . '\\' . $headerClass;
        return new $headerClass();
    }
}