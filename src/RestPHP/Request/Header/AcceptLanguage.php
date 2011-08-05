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
 * HTTP Request header containing a list of all languages the user-agent accepts
 *
 * The Accept-Language request-header field is similar to Accept, but restricts
 * the set of natural languages that are preferred as a response to the request.
 *
 * An example header would be
 * <code>
 * Accept-Language: da, en-gb;q=0.8, en;q=0.7
 * </code>
 * Which means: "I prefer Danish, but will accept British English and other
 * types of English." A language-range matches a language-tag if it exactly
 * equals the tag, or if it exactly equals a prefix of the tag such that the
 * first tag character following the prefix is "-". The special range "*", if
 * present in the Accept-Language field, matches every tag not matched by any
 * other range present in the Accept-Language field.
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Section 14
 */
class AcceptLanguage implements Header
{
    /**
     * Sorted list of languages the client will accept
     *
     * @var array
     */
    protected $language = array();

    /**
     * Parses the HTTP Accept-Language header
     *
     * @see \RestPHP\Request\Header\Accept::parse
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     *
     * @param string $header the value of the Accept-Language header after the colon
     */
    public function parse($header)
    {
        $this->language = array();

        $this->accept = array();

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

            $this->accept[strtolower($o->type)] = $o;
        }

        // weighted sort
        uasort($this->accept, function ($a, $b) {

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

        foreach ($this->accept as $a) {

            $this->language[strtolower($a->type)] = $a->type;
        }
    }

    /**
     * Gets all languages that this user agent accepts
     *
     * @return array
     */
    public function getLanguages()
    {
        return $this->language;
    }

    /**
     * Gets the preferred langauges of the user-agent. Usually the first item
     * listed in the header unless all languages are weighted
     *
     * @return string
     */
    public function getPreferredLanguage()
    {
        if (count($this->language) == 0) {
            return '*';
        }

        foreach ($this->language as $k => $lang) {
            if ($this->accept[$k]->q) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * Does the User-Agent accept this language?
     *
     * @param string $language the language to check
     *
     * @return boolean
     */
    public function isAccepted($language)
    {
        $k = strtolower($language);

        // set and a non-zero quality
        if (isset($this->language[$k])) {
            return (bool) $this->accept[$k]->q;
        }

        // or wildcard and a non-zero quality
        if (isset($this->language['*'])) {
            return (bool) $this->accept['*']->q;
        }

        // handle the acceptance of language without range
        if (strpos('-', $k)) {

            list($type, $subType) = explode('-', $k, 2);

            if (isset($this->language[$type])) {
                return (bool) $this->accept[$type]->q;
            }
        }

        return false;
    }
}