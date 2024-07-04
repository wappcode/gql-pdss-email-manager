<?php

use AppModule\AppModule;
use AppModule\Services\AppRouter;
use GQLBasicClient\GQLClient;
use GPDCore\Factory\EntityManagerFactory;
use GPDCore\Library\GPDApp;
use GPDCore\Services\ContextService;
use GPDEmailManager\GPDEmailManagerModule;
use Laminas\ServiceManager\ServiceManager;

require_once __DIR__ . "/../vendor/autoload.php";

global $entityManager;
global $graphqlClient;
global $context;

$options =  __DIR__ . "/../dev/config/doctrine.local.php";
$cacheDir = __DIR__ . "/../dev/data/DoctrineORMModule";

$enviroment = GPDApp::ENVIROMENT_TESTING;
$serviceManager = new ServiceManager();
$context = new ContextService($serviceManager);
$context->setDoctrineConfigFile($options);
$context->setDoctrineCacheDir($cacheDir);
$router = new AppRouter();
$app = new GPDApp($context, $router, $enviroment);
$app->addModules([
    GPDEmailManagerModule::class,
    AppModule::class,
]);
$localConfig = require __DIR__ . "/../dev/config/local.config.php";
$context->getConfig()->add($localConfig);

$entityManager = $context->getEntityManager();
$app_port = getenv("GQLPDSSEMAIL_APP_PORT") ? getenv("GQLPDSSEMAIL_APP_PORT") : "8080";
$graphqlApi = "http://localhost:{$app_port}/index.php/api";
$graphqlClient = new GQLClient($graphqlApi);
