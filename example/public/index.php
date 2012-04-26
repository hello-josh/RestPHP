<?php
// include path to src
set_include_path(
    realpath(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'library')
    . PATH_SEPARATOR . get_include_path());

include 'RestPHP/Autoloader.php';

\RestPHP\Autoloader::getInstance()->register();

$environment = new \RestPHP\Environment(
        getenv('RESTPHP_ENV') ? : \RestPHP\Environment::DEVELOPMENT);

$config = new \RestPHP\Config('../config/settings.ini', $environment);

$application = new \RestPHP\Application($environment, $config);

$application->handle(\RestPHP\Application::getDefaultRequest($config))->output();