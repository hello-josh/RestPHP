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
 * @subpackage Response
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
namespace RestPHP\Response\Marshaller;

/**
 *
 * @package    RestPHP
 * @subpackage Response
 * @author     Joshua Johnston <johnston.joshua@gmail.com>
 * @copyright  2011 RestPHP Framework
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 */
class Xml implements IMarshaller
{

    /**
     * @var \DomDocument
     */
    private $xml;

    private $rootNode;

    /**
     * Converts an associative array to XML wrapped in the root node &gt;response&gt;
     *
     * @param \RestPHP\Response\Response $response
     * @return string
     */
    public function marshall(\RestPHP\Response\Response $response) {
        $this->xml = new \DomDocument('1.0', 'UTF-8');
        $this->xml->formatOutput = true;
        $this->rootNode = $this->xml->appendChild($this->xml->createElement('response'));
        $this->convert($response->getData(), $this->rootNode);
        return $this->xml->saveXML();
    }

    /**
     * @return string
     */
    public function getContentType() {
        return 'application/xml';
    }

    /**
     * Converts the given data into a suitable xml representation
     *
     * @param mixed $data
     * @param \DOMNode $attachTo
     */
    protected function convert($data, \DOMNode $attachTo) {

        if (is_array($data) || $data instanceof \Traversable) {
            foreach ($data as $k => $datum) {
                if (is_int($k)) {
                    if ($attachTo === $this->rootNode) {
                        $name = 'item';
                    } else {
                        $name = $this->depluralize($attachTo->nodeName);
                    }
                    $k = $this->xml->createElement($name);
                } else {
                    $k = $this->xml->createElement($k);
                }

                $this->convert($datum, $attachTo->appendChild($k));
            }
        } elseif (is_object($data) && method_exists($data, 'toArray')) {

            $this->convert($data->toArray(), $attachTo);
        } else {
            $attachTo->appendChild($this->xml->createTextNode($data));
        }
    }

    /**
     * Converts a plural word to it's singular form
     *
     * @link https://sites.google.com/site/chrelad/notes-1/pluraltosingularwithphp Unknown author - comments removed for brevity
     * @param string $word
     * @return string
     */
    protected function depluralize($word) {
        $rules = array(
            // plural => singular map
            'ss'  => false,
            'os'  => 'o',
            'ies' => 'y',
            'xes' => 'x',
            'oes' => 'o',
            'ies' => 'y',
            'ves' => 'f',
            's'   => '');
        foreach (array_keys($rules) as $key) {

            if (substr($word, (strlen($key) * -1)) != $key) {
                continue;
            }

            if ($key === false) {
                return $word;
            }

            return substr($word, 0, strlen($word) - strlen($key)) . $rules[$key];
        }
        return $word;
    }
}