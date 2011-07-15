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
 * HTTP Request header containing a list of all Character Sets that are
 * acceptable similar to Accept header
 *
 * An example header would be
 * <code>Accept-Charset: iso-8859-5, unicode-1-1;q=0.8</code>
 *
 * The special value "*", if present in the Accept-Charset field, matches every
 * character set (including ISO-8859-1) which is not mentioned elsewhere in the
 * Accept-Charset field. If no "*" is present in an Accept-Charset field, then
 * all character sets not explicitly mentioned get a quality value of 0, except
 * for ISO-8859-1, which gets a quality value of 1 if not explicitly mentioned.
 *
 * If no Accept-Charset header is present, the default is that any character
 * set is acceptable. If an Accept-Charset header is present, and if the server
 * cannot send a response which is acceptable according to the Accept-Charset
 * header, then the server SHOULD send an error response with the 406
 * (not acceptable) status code, though the sending of an unacceptable response
 * is also allowed.
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Section 14
 */
class AcceptCharset implements Header
{
    /**
     * Sorted list of charsets the client will accept
     *
     * @var array
     */
    protected $charsets = array();

    /**
     * Parses the HTTP Accept-Charset header
     *
     * @see \RestPHP\Request\Header\Accept::parse
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     *
     * @param string $header the value of the Accept-Charset header after the colon
     */
    public function parse($header)
    {
        $this->charsets = array();

        $accept = array();

        $hasISO88591 = false;

        foreach (preg_split('/\s*,\s*/', $header) as $i => $term) {

            $o = new \stdclass;

            $o->pos = $i;

            if (preg_match(",^(\S+)\s*;\s*q=([0-9\.]+),i", $term, $M)) {

                $o->type = $M[1];
                $o->q = (double) $M[2];
            }
            else {

                $o->type = $term;
                $o->q = 1;
            }

            if (strtolower($o->type) == 'iso-8859-1' || $o->type == '*') {
                $hasISO88591 = true;
            }

            $accept[] = $o;
        }

        // see note on ISO-8859-1 and * in class comment
        // insert iso-8859-1 as the last item before q<1
        if (!$hasISO88591) {
            $o = new \stdclass;
            $o->type = 'iso-8859-1';
            $o->q = 1;
            $o->pos = count($accept);
            $accept[] = $o;
        }

        if ($header == '') {
            var_dump($accept);
        }

        // weighted sort
        usort($accept, function ($a, $b) {

            // first tier: highest q factor wins
            $diff = $b->q - $a->q;

            if ($diff > 0) {

                $diff = 1;
            }
            else if ($diff < 0) {

                $diff = -1;
            }
            else {

                // tie-breaker: first listed item wins
                $diff = $a->pos - $b->pos;
            }

            return $diff;
        });

        foreach ($accept as $a) {

            $a->type = strtolower($a->type);
            $this->charsets[$a->type] = $a;
        }
    }

    /**
     * Gets all charsets that this user agent accepts
     *
     * @return array
     */
    public function getCharsets()
    {
        return array_keys($this->charsets);
    }

    /**
     * Gets the preferred charset of the user-agent. Usually the first item
     * listed in the header unless all charsets are weighted
     *
     * @return string
     */
    public function getPreferredCharset()
    {
        return $this->charsets[key($this->charsets)]->type;
    }

    /**
     * Does the User-Agent accept this charset?
     *
     * If the agent has a wildcard (*) or no Accept-Charset header this will
     * return true per the RFC
     *
     * @param string $charset the charset to check
     *
     * @return boolean
     */
    public function isAccepted($charset)
    {
        if (count($this->charsets) == 0) {
            return true;
        }

        // set and q>0 is ok, but q=0 means do not use
        if (isset($this->charsets[strtolower($charset)])) {

            return ($this->charsets[strtolower($charset)]->q > 0);
        }

        return (isset($this->charsets['*']));
    }
}