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
 * AcceptCharsetTest - Tests the Accept-Charset header behaves as documented in RFC 2616
 * Section 14
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Test
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Sec 14
 */
class AcceptCharsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \RestPHP\Request\Header\AcceptCharset
     */
    protected $acceptCharset;

    public function setUp()
    {
        $this->acceptCharset = new \RestPHP\Request\Header\AcceptCharset();
    }

    /**
     * Make sure a single accept-charset header is parsed properly
     */
    public function testParseSingleType()
    {
        // basic
        $this->acceptCharset->parse('ISO-8859-1');

        $charsets = $this->acceptCharset->getCharsets();

        $this->assertEquals(array('iso-8859-1'),
                array_values($charsets));

        // with a quality flag
        $this->acceptCharset->parse('ISO-8859-1; q=0.2');

        $charsets = $this->acceptCharset->getCharsets();

        $this->assertEquals(array('iso-8859-1'),
                array_values($charsets));

        // The special value "*", if present in the Accept-Charset field,
        // matches every character set (including ISO-8859-1) which is not
        // mentioned elsewhere in the Accept-Charset field. If no "*" is present
        // in an Accept-Charset field, then all character sets not explicitly
        // mentioned get a quality value of 0, except for ISO-8859-1, which gets
        // a quality value of 1 if not explicitly mentioned.
        $this->acceptCharset->parse('UTF-8');

        $charsets = $this->acceptCharset->getCharsets();

        $this->assertEquals(array('utf-8', 'iso-8859-1'),
                array_values($charsets));
    }

    /**
     * Test accept headers with multiple items parse properly
     *
     * The special value "*", if present in the Accept-Charset field,
     * matches every character set (including ISO-8859-1) which is not
     * mentioned elsewhere in the Accept-Charset field. If no "*" is present
     * in an Accept-Charset field, then all character sets not explicitly
     * mentioned get a quality value of 0, except for ISO-8859-1, which gets
     * a quality value of 1 if not explicitly mentioned.
     */
    public function testParseMultipleTypes()
    {
        $this->acceptCharset->parse('iso-8859-5, unicode-1-1;q=0.8');

        $charsets = $this->acceptCharset->getCharsets();

        // no * so implies iso-8859-1
        $this->assertEquals(
            array(
                'iso-8859-5',
                'iso-8859-1',
                'unicode-1-1'
            ),
            $charsets
        );

        $this->acceptCharset->parse('UTF-8,*');

        $charsets = $this->acceptCharset->getCharsets();

        // has a * so no iso-8859-1
        $this->assertEquals(
            array(
                'utf-8',
                '*'
            ),
            $charsets
        );
    }

    /**
     * Tests that the preferred type is always first
     */
    public function testGetPreferredCharset()
    {
        $this->acceptCharset->parse('iso-8859-5, unicode-1-1;q=0.8');

        $preferred = $this->acceptCharset->getPreferredCharset();

        $this->assertEquals('iso-8859-5', $preferred);

        // kinda odd that none are q=1 but just in case . ...
        $this->acceptCharset->parse('unicode-1-1;q=0.8');

        $preferred = $this->acceptCharset->getPreferredCharset();

        $this->assertEquals('iso-8859-1', $preferred);
    }

    /**
     * Tests that mime types are found or not
     */
    public function testIsAccepted()
    {
        $this->acceptCharset->parse('*');

        $this->assertTrue(
                $this->acceptCharset->isAccepted('utf-8'),
                'Did not accept UTF-8 when given a wildcard *');

        $this->acceptCharset->parse('iso-8859-5, unicode-1-1;q=0.8');

        $this->assertTrue(
                $this->acceptCharset->isAccepted('unicode-1-1'),
                'Did not accept unicode-1-1 when given as an accepted type');

        $this->assertTrue(
                $this->acceptCharset->isAccepted('iso-8859-5'),
                'Did not accept iso-8859-5 when given as an accepted type');

        $this->assertTrue(
                $this->acceptCharset->isAccepted('iso-8859-1'),
                'Did not accept iso-8859-1 when not explicitly forbidden');
    }
}
