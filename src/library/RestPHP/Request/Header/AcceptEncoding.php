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
 * HTTP Request header containing a list of all content-codings that are
 * acceptable similar to Accept header
 *
 * An example header would be
 * <code>
 * Accept-Encoding: compress, gzip
 * Accept-Encoding:
 * Accept-Encoding: *
 * Accept-Encoding: compress;q=0.5, gzip;q=1.0
 * Accept-Encoding: gzip;q=1.0, identity; q=0.5, *;q=0
 * </code>
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Section 14
 */
class AcceptEncoding extends Header
{
    /**
     * Sorted list of content-codings the client will accept
     *
     * @var array
     */
    protected $encoding = array();

    /**
     * Parses the HTTP Accept-Encoding header
     *
     * @see \RestPHP\Request\Header\Accept::parse
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     *
     * @param string $header the value of the Accept-Encoding header after the colon. An empty header implies identity only
     */
    public function parse($header = 'identity') {
        $this->resetEncodingQualityValues();

        foreach (preg_split('/\s*,\s*/', $header) as $i => $term) {

            $o = new \stdclass;

            $o->pos = $i;

            if (preg_match(",^(\S+)\s*;\s*q=([0-9\.]+),i", $term, $M)) {

                $o->type = strtolower($M[1]);
                $o->q = (double) $M[2];
            } else {

                $o->type = strtolower($term);
                $o->q = 1;
            }

            switch ($o->type) {

                case 'x-gzip':
                    $this->encoding['gzip'] = $o->q;
                    break;

                case 'x-compress':
                    $this->encoding['compress'] = $o->q;
                    break;

                case '*':
                    foreach ($this->encoding as $enc => $q) {
                        if (null === $this->encoding[$enc]) {
                            $this->encoding[$enc] = $o->q;
                        }
                    }
                    break;

                default:
                    $this->encoding[$o->type] = $o->q;
                    break;
            }
        }

        // anything left as null means it is not accepted
        // so we set it to 0.0 for sort / isAccepted
        foreach ($this->encoding as $enc => $q) {
            if (null === $this->encoding[$enc]) {
                // The "identity" content-coding is always acceptable, unless
                // specifically refused because the Accept-Encoding field includes
                // "identity;q=0", or because the field includes "*;q=0" and does
                // not explicitly include the "identity" content-coding.
                if ($enc === 'identity') {
                    $this->encoding[$enc] = 1.0;
                } else {
                    $this->encoding[$enc] = 0.0;
                }
            }
        }

        arsort($this->encoding);
    }

    /**
     * Gets all content-codings that this user agent accepts
     *
     * @return array
     */
    public function getEncodings() {
        return $this->encoding;
    }

    /**
     * Gets the preferred content-codings of the user-agent. Usually the first item
     * listed in the header unless all content-codings are weighted
     *
     * @return string
     */
    public function getPreferredEncoding() {
        reset($this->encoding);
        $k = key($this->encoding);

        if ($this->encoding[$k] > 0) {
            return $k;
        }

        return null;
    }

    /**
     * Does the User-Agent accept this content-codings?
     *
     * The "identity" content-coding is always acceptable, unless
     * specifically refused because the Accept-Encoding field includes
     * "identity;q=0", or because the field includes "*;q=0" and does
     * not explicitly include the "identity" content-coding. If the
     * Accept-Encoding field-value is empty, then only the "identity"
     * encoding is acceptable.
     *
     * @param string $encoding the content-codings to check
     *
     * @return boolean
     */
    public function isAccepted($encoding) {
        $k = strtolower($encoding);

        // set and a non-zero quality means yes
        if (isset($this->encoding[$k])) {
            return (bool) $this->encoding[$k];
        }

        // any unknown encoding is treated as an identity
        return (bool) $this->encoding['identity'];
    }

    /**
     * Resets the qvalue for each accepted encoding
     */
    protected function resetEncodingQualityValues() {
        $this->encoding = array(
            'gzip' => null,
            'compress' => null,
            'deflate' => null,
            'identity' => null
        );
    }
}