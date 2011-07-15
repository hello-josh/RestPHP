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
namespace RestPHP\Request\Header;

/**
 * AcceptTest - Tests the Accept header behaves as documented in RFC 2616
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
class AcceptTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \RestPHP\Request\Header\Accept
     */
    protected $accept;

    public function setUp()
    {
        $this->accept = new \RestPHP\Request\Header\Accept();
    }

    /**
     * Make sure a single accept header is parsed properly
     */
    public function testParseSingleType()
    {
        // basic mime
        $this->accept->parse('text/html');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(array('text/html'),
                array_values($acceptTypes));

        // mime with a quality flag
        $this->accept->parse('text/html; q=0.2');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(array('text/html'),
                array_values($acceptTypes));

        // mine with an extension parameter named level
        // with a value of 1
        $this->accept->parse('text/html;level=1');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(array('text/html;level=1'),
                array_values($acceptTypes));

        // mime with an extension parameter of josh with a value
        // of hello and a quality of 0.1
        $this->accept->parse('text/html;josh="hello";q=0.1');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(array('text/html;josh="hello"'),
                array_values($acceptTypes));

        // mime with an extension parameter of josh with a value
        // of hello and a quality of 0.1 but space separated
        $this->accept->parse('text/html;josh="hello"; q=0.1');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(array('text/html;josh="hello"'),
                array_values($acceptTypes));
    }

    /**
     * Test accept headers with multiple items parse properly
     */
    public function testParseMultipleTypes()
    {
        $this->accept->parse('text/html, text/xml;level=1, text/*');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(
            array(
                'text/html',
                'text/xml;level=1',
                'text/*'
            ),
            array_values($acceptTypes)
        );

        $this->accept->parse('audio/*; q=0.2, audio/basic');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(
            array(
                'audio/basic',
                'audio/*'
            ),
            array_values($acceptTypes)
        );

        $this->accept->parse('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(
            array(
                'text/html',
                'text/x-c',
                'text/x-dvi',
                'text/plain'
            ),
            array_values($acceptTypes)
        );

        $this->accept->parse('text/*, text/html, text/html;level=1, */*');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(
            array(
                'text/html;level=1',
                'text/html',
                'text/*',
                '*/*'
            ),
            array_values($acceptTypes)
        );

        $this->accept->parse('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8,application/json');

        $acceptTypes = $this->accept->getTypes();

        $this->assertEquals(
            array(
                'text/html',
                'application/xhtml+xml',
                'application/json',
                'application/xml',
                '*/*'

            ),
            array_values($acceptTypes)
        );
    }

    /**
     * Tests that the preferred type is always first
     */
    public function testGetPreferredType()
    {
        $this->accept->parse('audio/*; q=0.2, audio/basic');

        $preferredType = $this->accept->getPreferredType();

        $this->assertEquals('audio/basic', $preferredType);

        $this->accept->parse('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c');

        $preferredType = $this->accept->getPreferredType();

        $this->assertEquals('text/html', $preferredType);

        $this->accept->parse('text/*, text/html, text/html;level=1, */*');

        $preferredType = $this->accept->getPreferredType();

        $this->assertEquals('text/html;level=1', $preferredType);
    }

    /**
     * Tests that mime types are found or not
     */
    public function testIsAccepted()
    {
        $this->accept->parse('audio/*; q=0.2, audio/basic');

        $this->assertTrue(
            $this->accept->isAccepted('audio/basic'),
            'Exact match failed'
        );

        $this->assertTrue(
            $this->accept->isAccepted('audio/mpeg'),
            'Wildcard audio did not match'
        );

        $this->assertFalse(
            $this->accept->isAccepted('video/mpeg'),
            'Wrong type matched'
        );

        $this->accept->parse('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8,application/json');

        $this->assertTrue(
            $this->accept->isAccepted('application/xhtml+xml'),
            'Did not accept application/xhtml+xml'
        );

        $this->assertTrue(
            $this->accept->isAccepted('application/json'),
            'Did not accept application/json'
        );
    }
}
