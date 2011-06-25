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
namespace RestPHP\Test;

/**
 * AcceptHeaderTest
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Test
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  Copyright (c) 2011, RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class AcceptHeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \RestPHP\Request\Header\AcceptHeader
     */
    protected $acceptHeader;

    public function setUp()
    {
        $this->acceptHeader = new \RestPHP\Request\Header\AcceptHeader();
    }

    /**
     * Make sure a single accept header is parsed properly
     */
    public function testParseSingleType()
    {
        // basic mime
        $this->acceptHeader->parse('text/html');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(array('text/html'), array_values($acceptTypes));

        // mime with a quality flag
        $this->acceptHeader->parse('text/html; q=0.2');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(array('text/html'), array_values($acceptTypes));

        // mine with an extension parameter named level
        // with a value of 1
        $this->acceptHeader->parse('text/html;level=1');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(array('text/html;level=1'), array_values($acceptTypes));

        // mime with an extension parameter of josh with a value
        // of hello and a quality of 0.1
        $this->acceptHeader->parse('text/html;josh="hello";q=0.1');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(array('text/html;josh="hello"'), array_values($acceptTypes));

        // mime with an extension parameter of josh with a value
        // of hello and a quality of 0.1 but space separated
        $this->acceptHeader->parse('text/html;josh="hello"; q=0.1');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(array('text/html;josh="hello"'), array_values($acceptTypes));
    }

    /**
     * Test accept headers with multiple items parse properly
     */
    public function testParseMultipleTypes()
    {
        $this->acceptHeader->parse('audio/*; q=0.2, audio/basic');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(
            array(
                'audio/basic',
                'audio/*'
            ),
            array_values($acceptTypes)
        );

        $this->acceptHeader->parse('text/plain; q=0.5, text/html, text/x-dvi; q=0.8, text/x-c');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(
            array(
                'text/html',
                'text/x-c',
                'text/x-dvi',
                'text/plain'
            ),
            array_values($acceptTypes)
        );

        $this->acceptHeader->parse('text/*, text/html, text/html;level=1, */*');

        $acceptTypes = $this->acceptHeader->getTypes();

        $this->assertEquals(
            array(
                'text/html;level=1',
                'text/html',
                'text/*',
                '*/*'
            ),
            array_values($acceptTypes)
        );
    }
}