<?php
// include path to src
set_include_path(
    realpath(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'src')
    . PATH_SEPARATOR . get_include_path());

include 'RestPHP/Autoloader.php';

$autoloader = new \RestPHP\Autoloader();
$autoloader->register();

$environment = getenv('RESTPHP_ENV') ?: \RestPHP\Environment::DEVELOPMENT;
$config = new \RestPHP\Config('../config/settings.ini', $environment);
$environment = new \RestPHP\Environment($environment);

$application = new \RestPHP\Application($environment, $config);

$application->run()->output();