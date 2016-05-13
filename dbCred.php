<?php

$db_host = "localhost";
$db_name = "fap";
$db_charset = "utf8mb4";
$db_username = "root";
$db_password = "";

$dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=" . $db_charset;

//$db_opt = [];

$db_opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

?>