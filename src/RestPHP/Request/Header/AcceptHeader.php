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

namespace RestPHP\Request\Header;

/**
 * HTTP Request header containing a list of all Content-Types that are
 * acceptable
 *
 * An example header would be
 * <code>Accept: text/html,application/xhtml+xml,application/xml;q=0.9,* /*;q=0.8,application/json;q=0.0</code>
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Request
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  Copyright (c) 2011, RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Section 14
 */
class AcceptHeader implements RequestHeader
{
    /**
     * Sorted list of mime-types the client will accept
     *
     * @var array
     */
    protected $mimeTypes = array();

    /**
     * Parses the HTTP Accept header
     *
     * Code provided by Wez Furlong
     * @link http://shiflett.org/blog/2011/may/the-accept-header
     * @link http://shiflett.org/blog/2011/may/the-accept-header#comment-7
     *
     * @param string $header the value of the Accept header after the colon
     */
    public function parse($header)
    {
        $this->mimeTypes = array();

        $accept = array();

        foreach (preg_split('/\s*,\s*/', $header) as $i => $term) {

            $o = new \stdclass;

            $o->pos = $i;

            if (preg_match(",^(\S+)(?:;[^q]\S*=\S+)*\s*;\s*q=([0-9\.]+),i",
                           $term, $M)) {

                $o->type = $M[1];
                $o->q = (double) $M[2];
            }
            else {

                $o->type = $term;
                $o->q = 1;
            }

            $accept[] = $o;
        }

        // weighted sort
        usort($accept, function ($a, $b) {

            // first tier: highest q factor wins
            $diff = $b->q - $a->q;

            if ($diff > 0) {

                $diff = 1;
            }
            elseif ($diff < 0) {

                $diff = -1;
            }
            else {

                // TODO:
                // Media ranges can be overridden by more specific media ranges
                // or specific media types. If more than one media range
                // applies to a given type, the most specific reference has
                // precedence.
                //
                // tie-breaker: first listed item wins
                $diff = $a->pos - $b->pos;
            }

            return $diff;
        });

        foreach ($accept as $a) {

            $this->mimeTypes[strtolower($a->type)] = $a->type;
        }
    }

    /**
     * Gets all mime types that this user agent accepts
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->mimeTypes;
    }

    /**
     * Gets the preferred mime type of the user-agent. Usually the first item
     * listed in the header unless all mimes are weighted
     *
     * @return string
     */
    public function getPreferredType()
    {
        return $this->mimeTypes[key($this->mimeTypes)];
    }

    /**
     * Does the User-Agent accept this mime type?
     *
     * If the agent has a wildcard in accept * /* this will return true
     *
     * @todo Handle wildcard mimes like audio/*
     *
     * @param string $mimeType the mime type to check
     *
     * @return boolean
     */
    public function isAccepted($mimeType)
    {
        if (isset($this->mimeTypes[strtolower($mimeType)]) ||
                isset($this->mimeTypes['*/*'])) {

            return true;
        }

        return false;
    }

}