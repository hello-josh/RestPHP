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
 * @subpackage Resource
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
/**
 * @namespace
 */

namespace RestPHP\Resource;

/**
 * RestPHP Base Resource class
 *
 * @category   RestPHP
 * @package    RestPHP
 * @subpackage Resource
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Resource
{
    /**
     * Request Instance
     *
     * @var \RestPHP\Request\Request
     */
    protected $request;

    /**
     * Response Instance
     *
     * @var \RestPHP\Response\Response
     */
    protected $response;

    /**
     *
     * @var \RestPHP\Application
     */
    protected $application;

    /**
     *
     * @return \RestPHP\Request\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \RestPHP\Request\Request $request
     */
    public function setRequest(\RestPHP\Request\Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \RestPHP\Response\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     *
     * @param \RestPHP\Response\Response $response
     */
    public function setResponse(\RestPHP\Response\Response $response)
    {
        $this->response = $response;
    }

    /**
     *
     * @return \RestPHP\Application
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     *
     * @param \RestPHP\Application $application
     */
    public function setApplication(\RestPHP\Application $application) {
        $this->application = $application;
    }

    /**
     *
     * @return \RestPHP\Response\Response
     */
    public function execute()
    {
        $method = strtolower($this->getRequest()->getHttpMethod());

        if ($this->before()) {
            $this->$method();
            $this->after();
        }
        return $this->getResponse();
    }

    /**
     * Called before executing the proper resource method.
     * Return false to cancel the action
     */
    public function before() {
        return true;
    }

    /**
     * Called after executing the proper resource method. This is not
     * Called if before() returns false
     */
    public function after() {}

    /**
     * Called for OPTIONS requests
     */
    public function options()
    {
        $this->defaultNotImplemented();
    }

    /**
     * Called for GET requests
     */
    public function get()
    {
        $this->defaultNotImplemented();
    }

    /**
     * Called for HEAD requests
     */
    public function head()
    {
        $this->defaultNotImplemented();
    }

    /**
     * Called for POST requests
     */
    public function post()
    {
        $this->defaultNotImplemented();
    }

    /**
     * Called for PUT requests
     */
    public function put()
    {
        $this->defaultNotImplemented();
    }

    /**
     * Called for TRACE requests
     */
    public function trace()
    {
        $this->defaultNotImplemented();
    }

    private function defaultNotImplemented()
    {
        $method = strtoupper($this->getRequest()->getHttpMethod());
        $response = $this->getResponse();
        $response->setStatus(501);
        $response->message = sprintf(
                'The HTTP Method %s is not implemented for this resource',
                $method);
    }
}