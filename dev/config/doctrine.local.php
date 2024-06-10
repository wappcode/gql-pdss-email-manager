<?php
return [
    "driver" => [
        'user'     =>   'root',
        'password' =>   'dbpassword',
        'dbname'   =>   'gqlpdss_emaildb',
        'driver'   =>   'pdo_mysql',
        'host'   =>     'gqlpdsemail-mysql',
        'charset' =>    'utf8mb4'
    ],
    "entities" => require __DIR__ . "/doctrine.entities.php"
];
