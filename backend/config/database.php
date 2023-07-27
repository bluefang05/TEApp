<?php
function getPDO() {
    $host = 'localhost';
    $db   = 'aspierdc_TEA';
    $user = 'aspierdc_admin';
    $pass = 'UnoDosTresCuatroCinco12345...';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $user, $pass, $opt);
}
?>
