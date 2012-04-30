<?php
/* @var $this \RestPHP\Application */
$array = new ArrayObject($this->getConfig()->application->components->mymap->toArray());
$this->setComponent('mymap', $array);