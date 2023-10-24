<?php

use GPDCore\Library\GPDApp;
use AppModule\Services\AppRouter;
use GPDCore\Services\ContextService;
use Laminas\ServiceManager\ServiceManager;
use GPDEmailManager\Library\EmialPassworEncoder;

require_once __DIR__ . "/../vendor/autoload.php";

date_default_timezone_set('America/Mexico_City');

$enviroment = GPDApp::ENVIROMENT_DEVELOPMENT; // Cambiar a producción al publicarlo
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
$serviceManager = new ServiceManager();
$context = new ContextService($serviceManager);
$entityConfig = __DIR__ . "/../dev/config/doctrine.local.php";
$entityCache = __DIR__ . "/../dev/data/DoctrineORMModule";
$context->setDoctrineConfigFile($entityConfig);
$context->setDoctrineCacheDir($entityCache);
$router = new AppRouter();
$app = new GPDApp($context, $router, $enviroment);

$localConfig = require __DIR__ . "/../dev/config/local.config.php";
$context->getConfig()->add($localConfig);
$encriptPassword = $context->getConfig()->get("gpd_email_manager__secret_key");
$iv = $context->getConfig()->get("gql_email_manager__iv");
$password = readline("\nEnter a password to encode: \n");
$encodedPassword = EmialPassworEncoder::encrypt($password, $encriptPassword, $iv);
$decodePassword = EmialPassworEncoder::decrypt($encodedPassword, $encriptPassword, $iv);
echo "\nThe encoded password is {$encodedPassword}\n";
echo "\nThe decoded password is {$decodePassword}\n";
