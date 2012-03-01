<?php
/**
 * Master BackOffice System (MBS)
 *
 * This file belongs to LexisNexis MBS
 *
 * PHP Version 5.3
 *
 * @author     Joshua Johnston <joshua.johnston@lexisnexis.com>
 * @copyright  2011 LexisNexis
 * @category   Example
 * @package    Example
 * @version    $Id:$
 */

/**
 * @namespace
 */
namespace Example;

/**
 *
 * @package    Example
 * @author     jjohnston
 */
class IndexResource extends \RestPHP\Resource\Resource
{
    public function get()
    {
        $this->getResponse()->greeting = "hello";
    }

    public function post()
    {
        $this->getResponse()->setData($this->getRequest()->getBody());
    }
}