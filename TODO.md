TODO
======

1. Implement the following \RestPHP\Request\Header\Header
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

1. Implement the following \RestPHP\Response\Header\Header
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

1. Automatic content negotiation and Accept based output serialization.
   application/json is json_encode()'d, application/xml is turned into XML, etc