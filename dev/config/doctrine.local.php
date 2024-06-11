<?php
return [
    "driver" => [
        'user'     =>   getenv("GQLPDSSEMAIL_DBPASSWORD") ? getenv("GQLPDSSEMAIL_DBUSER") : 'root',
        'password' =>   getenv("GQLPDSSEMAIL_DBPASSWORD") ? getenv("GQLPDSSEMAIL_DBPASSWORD") : 'dbpassword',
        'dbname'   =>   getenv("GQLPDSSEMAIL_DBNAME") ? getenv("GQLPDSSEMAIL_DBNAME") : 'gqlpdss_emaildb',
        'driver'   =>   'pdo_mysql',
        'host'   =>     getenv("GQLPDSSEMAIL_DBHOST") ? getenv("GQLPDSSEMAIL_DBHOST") : 'gqlpdsemail-mysql',
        'charset' =>    'utf8mb4'
    ],
    "entities" => require __DIR__ . "/doctrine.entities.php"
];
