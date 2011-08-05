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
 * @subpackage Test
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */

/**
 * @namespace
 */
namespace RestPHP\Request\Header;

/**
 * AcceptEncodingTest - Tests the Accept-Encoding header behaves as documented
 * in RFC 2616 Section 14
 *
 * This test will verify based off the RFC examples:
 * <ul>
 * <li>Accept-Encoding:</li>
 * <li>Accept-Encoding: *</li>
 * <li>Accept-Encoding: gzip</li>
 * <li>Accept-Encoding: compress, gzip</li>
 * <li>Accept-Encoding: compress;q=0.5, gzip;q=1.0</li>
 * <li>Accept-Encoding: gzip;q=1.0, identity; q=0.5, *;q=0</li>
 * </ul>
 *
 * Valid Encodings are:
 * <dl>
 * <dt>gzip (x-gzip)</dt>
 * <dd>An encoding format produced by the file compression program "gzip"
 * (GNU zip) as described in RFC 1952 [25]. This format is a Lempel-Ziv coding
 * (LZ77) with a 32 bit CRC.</dd>
 *
 * <dt>compress (x-compress)</dt>
 * <dd>The encoding format produced by the common UNIX file compression
 * program "compress". This format is an adaptive Lempel-Ziv-Welch coding (LZW).
 *
 *      <blockquote>
 *      Use of program names for the identification of encoding formats
 *      is not desirable and is discouraged for future encodings. Their
 *      use here is representative of historical practice, not good
 *      design. For compatibility with previous implementations of HTTP,
 *      applications SHOULD consider "x-gzip" and "x-compress" to be
 *      equivalent to "gzip" and "compress" respectively.
 *      </blockquote>
 * </dd>
 *
 * <dt>deflate</dt>
 * <dd>The "zlib" format defined in RFC 1950 [31] in combination with the
 * "deflate" compression mechanism described in RFC 1951 [29].</dd>
 *
 * <dt>identity</dt>
 * <dd>The default (identity) encoding; the use of no transformation
 * whatsoever. This content-coding is used only in the Accept- Encoding header,
 * and SHOULD NOT be used in the Content-Encoding header.</dd>
 * </dl>
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Test
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Sec 14
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.5 RFC 2616 Sec 3.5
 */
class AcceptEncodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RestPHP\Request\Header\AcceptEncoding
     */
    protected $acceptEncoding;

    public function setUp()
    {
        $this->acceptEncoding = new \RestPHP\Request\Header\AcceptEncoding();
    }

    /**
     * Make sure a single Accept-Encoding header is parsed properly
     *
     * If the content-coding is one of the content-codings listed in
     * the Accept-Encoding field, then it is acceptable, unless it is
     * accompanied by a qvalue of 0. (As defined in section 3.9, a
     * qvalue of 0 means "not acceptable.")
     */
    public function testAcceptsValidEncoding()
    {
        $acceptEncoding = $this->acceptEncoding;

        // Accept-Encoding: gzip
        $acceptEncoding->parse('gzip');

        $this->assertTrue(
            $acceptEncoding->isAccepted('gzip'),
            'Did not accept gzip when gzip was explicitly mentioned'
        );

        // Accept-Encoding: compress, gzip
        $acceptEncoding->parse('compress, gzip');

        $this->assertTrue(
            $acceptEncoding->isAccepted('gzip'),
            'Did not accept gzip when gzip was explicitly mentioned'
        );

        $this->assertTrue(
            $acceptEncoding->isAccepted('compress'),
            'Did not accept compress when compress was explicitly mentioned'
        );

        $this->assertFalse(
            $acceptEncoding->isAccepted('deflate'),
            'Accepted deflate when deflate was not provided'
        );

        // Accept-Encoding: compress;q=0.5, gzip;q=1.0
        $acceptEncoding->parse('compress;q=0.5, gzip;q=1.0');

        $this->assertTrue(
            $acceptEncoding->isAccepted('gzip'),
            'Did not accept gzip when gzip was explicitly mentioned'
        );

        $this->assertTrue(
            $acceptEncoding->isAccepted('compress'),
            'Did not accept compress when compress was explicitly mentioned'
        );

        $this->assertFalse(
            $acceptEncoding->isAccepted('deflate'),
            'Accepted deflate when deflate was not provided'
        );

        // Accept-Encoding: gzip;q=1.0, identity; q=0.5, deflate;q=0
        $acceptEncoding->parse('gzip;q=1.0, identity; q=0.5, deflate;q=0');

        $this->assertTrue(
            $acceptEncoding->isAccepted('gzip'),
            'Did not accept gzip when gzip was explicitly mentioned'
        );

        $this->assertFalse(
            $acceptEncoding->isAccepted('deflate'),
            'Accepted deflate when deflate had a quality of 0'
        );

    }

    /**
     * The special "*" symbol in an Accept-Encoding field matches any
     * available content-coding not explicitly listed in the header field.
     */
    public function testAcceptsWithWildcard()
    {
        $acceptEncoding = $this->acceptEncoding;

        // Accept-Encoding: *
        $acceptEncoding->parse('*');

        $this->assertTrue(
            $acceptEncoding->isAccepted('gzip'),
            'Did not accept gzip when a wildcard was given'
        );

        // Accept-Encoding: *;q=0
        // should never happen in real life or else we can never send anything!
        $acceptEncoding->parse('*;q=0');

        $this->assertFalse(
            $acceptEncoding->isAccepted('deflate'),
            'Accepted deflate when deflate was not mentioned and wildcard had a quality of 0'
        );

        // Accept-Encoding: gzip;q=1.0, identity; q=0.5, *;q=0
        $acceptEncoding->parse('gzip;q=1.0, identity; q=0.5, *;q=0');

        $this->assertFalse(
            $acceptEncoding->isAccepted('deflate'),
            'Accepted deflate when deflate was not mentioned and wildcard had a quality of 0'
        );
    }

    /**
     * If multiple content-codings are acceptable, then the acceptable
     * content-coding with the highest non-zero qvalue is preferred.
     */
    public function testPreferredHighestNonZeroQuality()
    {
        $acceptEncoding = $this->acceptEncoding;

        // Accept-Encoding: compress;q=0.5, gzip;q=1.0
        $acceptEncoding->parse('compress;q=0.5, gzip;q=1.0');

        $this->assertEquals(
            'gzip',
            $acceptEncoding->getPreferredEncoding()
        );

        // Accept-Encoding: gzip;q=1.0, identity; q=0.5, *;q=0
        $acceptEncoding->parse('gzip;q=1.0, identity; q=0.5, *;q=0');

        $this->assertEquals(
            'gzip',
            $acceptEncoding->getPreferredEncoding()
        );

        // Accept-Encoding: *;q=0
        // should never happen in real life or else we can never send anything!
        $acceptEncoding->parse('*;q=0');

        $this->assertEmpty(
            $acceptEncoding->getPreferredEncoding()
        );

        // Accept-Encoding:
        $acceptEncoding->parse();

        $this->assertEquals(
            'identity',
            $acceptEncoding->getPreferredEncoding(),
                print_r($acceptEncoding, 1)
        );
    }

    /**
     * The "identity" content-coding is always acceptable, unless
     * specifically refused because the Accept-Encoding field includes
     * "identity;q=0", or because the field includes "*;q=0" and does
     * not explicitly include the "identity" content-coding. If the
     * Accept-Encoding field-value is empty, then only the "identity"
     * encoding is acceptable.
     */
    public function testIdentityAlwaysValidUnlessPassedQualityZero()
    {
        $acceptEncoding = $this->acceptEncoding;

        // Accept-Encoding:
        $acceptEncoding->parse();

        $this->assertTrue(
            $acceptEncoding->isAccepted('identity'),
            'Refused identity when given an empty list'
        );

         // Accept-Encoding: *
        $acceptEncoding->parse('*');

        $this->assertTrue(
            $acceptEncoding->isAccepted('identity'),
            'Refused identity when given a wildcard with no qvalue'
        );

         // Accept-Encoding: gzip
        $acceptEncoding->parse('gzip');

        $this->assertTrue(
            $acceptEncoding->isAccepted('identity'),
            'Refused identity when given just gzip'
        );

         // Accept-Encoding: compress, gzip
        $acceptEncoding->parse('compress, gzip');

        $this->assertTrue(
            $acceptEncoding->isAccepted('identity'),
            'Refused identity when given two encodings but no wildcard or identity'
        );

         // Accept-Encoding: compress;q=0.5, gzip;q=1.0
        $acceptEncoding->parse('compress;q=0.5, gzip;q=1.0');

        $this->assertTrue(
            $acceptEncoding->isAccepted('identity'),
            'Refused identity when given two encodings with qvalues but no wildcard or identity'
        );

        // Accept-Encoding: gzip;q=1.0, identity; q=0.5, *;q=0
        $acceptEncoding->parse('gzip;q=1.0, identity; q=0.5, *;q=0');

        $this->assertTrue(
            $acceptEncoding->isAccepted('identity'),
            'Refused identity when given a wildcard with a 0 qvalue but identity was specified'
        );

        // Accept-Encoding: gzip;q=1.0, *;q=0
        $acceptEncoding->parse('gzip;q=1.0, *;q=0');

        $this->assertFalse(
            $acceptEncoding->isAccepted('identity'),
            'Accepted identity when given a wildcard with a 0 qvalue and identity WAS NOT specified'
        );
    }
}
