<?php

use AppModule\AppModule;
use GPDCore\Library\GPDApp;
use AppModule\Services\AppRouter;
use GPDCore\Services\ContextService;
use GPDEmailManager\GPDEmailManagerModule;
use Laminas\ServiceManager\ServiceManager;

require_once __DIR__ . "/../../vendor/autoload.php";
$configFile = __DIR__ . "/../config/doctrine.local.php";
$cacheDir = __DIR__ . "/../data/DoctrineORMModule";
$enviroment = getenv("APP_ENV");
$serviceManager = new ServiceManager();
$context = new ContextService($serviceManager);
$context->setConfigFile($configFile);
$context->setCacheDir($cacheDir);
$router = new AppRouter();
$app = new GPDApp($context, $router, $enviroment);
$app->addModules([
    GPDEmailManagerModule::class,
    AppModule::class,
]);
$localConfig = require __DIR__ . "/../config/local.config.php";
$context->getConfig()->add($localConfig);
$app->run();
