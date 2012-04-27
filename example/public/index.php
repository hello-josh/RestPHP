<?php
// include path to %APP%/src/library
set_include_path(
    dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'library'
    . PATH_SEPARATOR . get_include_path());

include 'RestPHP/Autoloader.php';

\RestPHP\Autoloader::getInstance()->register();

$application = new \RestPHP\Application(
    getenv('RESTPHP_ENV') ?: \RestPHP\Environment::DEVELOPMENT,
    '../config/settings.ini');

$application->handle()->output();