<?php
echo "\n Preparando para inicializar base de datos \n";
$user = getenv("GQLPDSSEMAIL_DBUSER") ? getenv("GQLPDSSEMAIL_DBUSER") : 'root';
$pass = getenv("GQLPDSSEMAIL_DBPASSWORD") ?  getenv("GQLPDSSEMAIL_DBPASSWORD") : 'dbpassword';
$host = "gqlpdsemail-mysql";
$databasename = getenv("GQLPDSSEMAIL_DBNAME") ?  getenv("GQLPDSSEMAIL_DBNAME") : 'gqlpdss_emaildb';
$pdo = new PDO("mysql:host={$host}", $user, $pass);
echo "\n Limpiando base de datos {$databasename} \n";
$pdo->exec("DROP DATABASE IF EXISTS {$databasename};");
echo "\n Creando base de datos {$databasename};";
$pdo->exec("CREATE DATABASE IF NOT EXISTS {$databasename};");
