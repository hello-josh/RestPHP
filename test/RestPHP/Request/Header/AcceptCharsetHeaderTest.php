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
 * @copyright  Copyright (c) 2011, RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */

/**
 * @namespace
 */
namespace RestPHP\Test\Request\Header;

/**
 * AcceptCharsetHeaderTest - Tests the Accept-Charset header behaves as documented in RFC 2616
 * Section 14
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Test
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  Copyright (c) 2011, RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html RFC 2616 Sec 14
 */
class AcceptCharsetHeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \RestPHP\Request\Header\AcceptCharsetHeader
     */
    protected $acceptCharsetHeader;

    public function setUp()
    {
        $this->acceptCharsetHeader = new \RestPHP\Request\Header\AcceptCharsetHeader();
    }

    /**
     * Make sure a single accept-charset header is parsed properly
     */
    public function testParseSingleType()
    {
        // basic
        $this->acceptCharsetHeader->parse('ISO-8859-1');

        $charsets = $this->acceptCharsetHeader->getCharsets();

        $this->assertEquals(array('iso-8859-1'),
                array_values($charsets));

        // with a quality flag
        $this->acceptCharsetHeader->parse('ISO-8859-1; q=0.2');

        $charsets = $this->acceptCharsetHeader->getCharsets();

        $this->assertEquals(array('iso-8859-1'),
                array_values($charsets));

        // The special value "*", if present in the Accept-Charset field,
        // matches every character set (including ISO-8859-1) which is not
        // mentioned elsewhere in the Accept-Charset field. If no "*" is present
        // in an Accept-Charset field, then all character sets not explicitly
        // mentioned get a quality value of 0, except for ISO-8859-1, which gets
        // a quality value of 1 if not explicitly mentioned.
        $this->acceptCharsetHeader->parse('UTF-8');

        $charsets = $this->acceptCharsetHeader->getCharsets();

        $this->assertEquals(array('iso-8859-1', 'utf-8'),
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
        $this->acceptCharsetHeader->parse('iso-8859-5, unicode-1-1;q=0.8');

        $charsets = $this->acceptCharsetHeader->getCharsets();

        // no * so implies iso-8859-1
        $this->assertEquals(
            array(
                'iso-8859-1',
                'iso-8859-5',
                'unicode-1-1'
            ),
            array_values($charsets)
        );

        $this->acceptCharsetHeader->parse('UTF-8,*');

        $charsets = $this->acceptCharsetHeader->getCharsets();

        // has a * so no iso-8859-1
        $this->assertEquals(
            array(
                'utf-8',
                '*'
            ),
            array_values($charsets)
        );
    }

    /**
     * Tests that the preferred type is always first
     */
    public function testgetPreferredCharset()
    {
        $this->markTestIncomplete('implement me');
        $this->acceptCharsetHeader->parse('audio/*; q=0.2, audio/basic');

        $preferredType = $this->acceptCharsetHeader->getPreferredCharset();

        $this->assertEquals('audio/basic', $preferredType);
    }

    /**
     * Tests that mime types are found or not
     */
    public function testIsAccepted()
    {
        $this->markTestIncomplete('implement me');
        $this->acceptCharsetHeader->parse('audio/*; q=0.2, audio/basic');

        $this->assertTrue(
            $this->acceptCharsetHeader->isAccepted('audio/basic'),
            'Exact match failed'
        );

        $this->assertTrue(
            $this->acceptCharsetHeader->isAccepted('audio/mpeg'),
            'Wildcard audio did not match'
        );

        $this->assertFalse(
            $this->acceptCharsetHeader->isAccepted('video/mpeg'),
            'Wrong type matched'
        );

        // If no Accept-Charset header is present, the default is that any
        // character set is acceptable.
        $this->acceptCharsetHeader->parse();

        $this->assertTrue(
            $this->acceptCharsetHeader->isAccepted('utf-8'),
            'Did not accept random charset'
        );

        $this->assertTrue(
            $this->acceptCharsetHeader->isAccepted('Big-5'),
            'Did not accept random charset'
        );
    }
}
