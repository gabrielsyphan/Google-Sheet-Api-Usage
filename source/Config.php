<?php

define("ROOT", "https://localhost/sheetsApi");
define("THEMES", __DIR__."/../themes");
define('GOOGLE_CLIENT_ID', 'YOUR GOOGLE PROJECT CLIENT ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR GOOGLE PROJECT CLIENT SECRET');

/**
 * Database config with table data
 */
define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "",
    "username" => "",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

/**
 * @param string|null $uri
 * @return string
 */
function url(string $uri = null): string
{
    if ($uri) {
        return ROOT . "/{$uri}";
    }

    return ROOT;
}
