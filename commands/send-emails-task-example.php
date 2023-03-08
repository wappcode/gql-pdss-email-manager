<?php

use GPDCore\Services\ContextService;
use Laminas\ServiceManager\ServiceManager;
use GPDEmailManager\Services\EmailProcessQueue;

require_once __DIR__ . "/../vendor/autoload.php";

$appEnv = getenv("APP_ENV");
$production = is_string($appEnv) && strtolower($appEnv) === 'production'; // bool
$serviceManager = new ServiceManager();
$context = new ContextService($serviceManager, $production);

date_default_timezone_set('America/Mexico_City');
$localConfig = require __DIR__ . "/../config/local-config.php";
$context->getConfig()->add($localConfig);

EmailProcessQueue::proccessAll($context);
