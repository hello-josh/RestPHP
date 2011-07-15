README
======

RestPHP Framework (http://www.restphp.com)

What is RestPHP?
----------------

RestPHP is a php framework for creating RESTful applications
and APIs that rely on what HTTP already provides us:

1. A simple way to refer to resources

2. An even simpler way to announce our intent using HTTP
   verbs (GET, PUT, POST, DELETE)

3. A full list of response codes to inform the client of the
   status of their request


Features
--------

Requirements
------------

PHP 5.3

TODO
----

1. Handle Config

    * basedir
    * baseurl
    * Resource class namespace / prefix / etc

2. Handle URL to Resource routing

3. Handle Resource method call based on the HTTP REQUEST_METHOD

4. Implement the following \RestPHP\Request\Header\RequestHeader
   subclasses and tests

    * Accept -class- -test-
    * Accept-Charset -class- -test-
    * Accept-Encoding -class- -test-
    * Accept-Language -class- test
    * Authorization class test
    * Cache-Control class test
    * Connection class test
    * Cookie class test
    * Content-Length class test
    * Content-MD5 class test
    * Content-Type class test
    * Date class test
    * Expect class test
    * From class test
    * Host class test
    * If-Match class test
    * If-Modified-Since class test
    * If-None-Match class test
    * If-Range class test
    * If-Unmodified-Since class test
    * Max-Forwards class test
    * Pragma class test
    * Proxy-Authorization class test
    * Range class test
    * Referer class test
    * TE class test
    * Upgrade class test
    * User-Agent class test
    * Via class test
    * Warning class test

5. Implement the following \RestPHP\Response\Header\ResponseHeader
   subclasses and tests

    * Accept-Ranges class test
    * Age class test
    * Allow class test
    * Cache-Control class test
    * Connection class test
    * Content-Encoding class test
    * Content-Language class test
    * Content-Length class test
    * Content-Location class test
    * Content-MD5 class test
    * Content-Disposition class test
    * Content-Range class test
    * Content-Type class test
    * Date class test
    * ETag class test
    * Expires class test
    * Last-Modified class test
    * Link class test
    * Location class test
    * P3P class test
    * Pragma class test
    * Proxy-Authenticate class test
    * Refresh class test
    * Retry-After class test
    * Server class test
    * Set-Cookie class test
    * Strict-Transport-Security class test
    * Trailer class test
    * Transfer-Encoding class test
    * Vary class test
    * Via class test
    * Warning class test
    * WWW-Authenticate class test