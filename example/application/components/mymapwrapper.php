<?php
/* @var $this \RestPHP\Application */

// use another component!
$mymap = $this->getComponent('mymap');
$this->setComponent('mymapwrapper', new \SplObjectStorage($mymap));