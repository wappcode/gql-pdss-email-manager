<?php
ini_set("display_error", 1);
error_reporting(E_ALL);
echo "\n Preparando para insertar datos en la  base de datos \n";
$user = getenv("GQLPDSSEMAIL_DBUSER") ? getenv("GQLPDSSEMAIL_DBUSER") : 'root';
$pass = getenv("GQLPDSSEMAIL_DBPASSWORD") ?  getenv("GQLPDSSEMAIL_DBPASSWORD") : 'dbpassword';
$host = "gqlpdsemail-mysql";
$databasename = getenv("GQLPDSSEMAIL_DBNAME") ?  getenv("GQLPDSSEMAIL_DBNAME") : 'gqlpdss_emaildb';
$pdo = new PDO("mysql:host={$host};dbname={$databasename}", $user, $pass);

$sql = file_get_contents(__DIR__ . "/gqlpdss_emaildb.sql");
if (empty($sql)) {
    echo "\n No hay datos que insertar";
    exit;
}
echo "\n Insertando datos {$databasename};\n";
echo $sql;
try {
    $pdo->query($sql);
    echo "\n Datos insertados\n";
} catch (Exception $e) {
    echo $e->getMessage();
}
