README
======

RestPHP Framework (http://restphp.com)

What is RestPHP?
----------------

RestPHP is a php framework for creating RESTful applications
and APIs that rely on what HTTP already provides us:

1. A simple way to refer to resources

2. An even simpler way to announce our intent using HTTP
   verbs (GET, PUT, POST, DELETE)

3. A full list of response codes to inform the client of the
   status of their request

4. Content negotiation via the Accept header as well as Character Set, Encoding
   and Language


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

4. Implement the following \RestPHP\Request\Header\Header
   subclasses and tests

    * Accept-Language
    * Authorization
    * Cache-Control
    * Connection
    * Cookie
    * Content-Length
    * Content-MD5
    * Content-Type
    * Date
    * Expect
    * From
    * Host
    * If-Match
    * If-Modified-Since
    * If-None-Match
    * If-Range
    * If-Unmodified-Since
    * Max-Forwards
    * Pragma
    * Proxy-Authorization
    * Range
    * Referer
    * TE
    * Upgrade
    * User-Agent
    * Via
    * Warning

5. Implement the following \RestPHP\Response\Header\Header
   subclasses and tests

    * Accept-Ranges
    * Age
    * Allow
    * Cache-Control
    * Connection
    * Content-Encoding
    * Content-Language
    * Content-Length
    * Content-Location
    * Content-MD5
    * Content-Disposition
    * Content-Range
    * Content-Type
    * Date
    * ETag
    * Expires
    * Last-Modified
    * Link
    * Location
    * P3P
    * Pragma
    * Proxy-Authenticate
    * Refresh
    * Retry-After
    * Server
    * Set-Cookie
    * Strict-Transport-Security
    * Trailer
    * Transfer-Encoding
    * Vary
    * Via
    * Warning
    * WWW-Authenticate
