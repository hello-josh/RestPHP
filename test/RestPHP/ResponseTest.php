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
 * @copyright  2011 RestPHP Framework
 * @package    RestPHP
 * @namespace  RestPHP
 * @subpackage
 */

namespace RestPHP\Test;

/**
 * RestPHP\Response
 */
require 'RestPHP/Response.php';

/**
 * ResponseTest
 *
 * @author     "Joshua Johnston" <johnston.joshua@gmail.com>
 * @namespace  RestPHP
 * @package    RestPHP
 * @version    $Id:$
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * \RestPHP\Response
	 *
	 * @var \RestPHP\Response
	 */
	protected $response;

	/**
	 * Creates \RestPHP\Response instance
	 */
	protected function setUp()
	{
		$this->response = new \RestPHP\Response();
	}

	/**
	 * Tests that the Status header for FCGI is generated properly
	 */
	public function testFcgiStatusGeneratedProperly()
	{
		$response = $this->response;
		$response->setIsFgci(true);
		$this->assertEquals('Status: 200 OK', $response->makeStatus());
	}

	/**
	 * Tests that the Status header for HTTP is generated properly
	 */
	public function testHttpStatusGeneratedProperly()
	{
		$response = $this->response;
		$response->setIsHttp(true);
		$this->assertEquals('HTTP/1.1 200 OK', $response->makeStatus());
	}

	/**
	 * Provides for testContentType
	 *
	 * @return array
	 */
	public static function providerContentType()
	{
		return array(
			array(
				array('Content-Type', 'text/html'),
				'Content-Type: text/html'
			),
			array(
				array('Content-Type', 'application/json'),
				'Content-Type: application/json'
			),
			array(
				array('Content-Type', 'text/html', 'charset=utf-8'),
				'Content-Type: text/html; charset=utf-8'
			)
		);
	}

	/**
	 * @dataProvider providerContentType
	 */
	public function testContentType($header, $expected)
	{
		$response = $this->response;

		$actual = $response->makeHeader($header);

		$this->assertEquals($expected, $actual);
	}
}