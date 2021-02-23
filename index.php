<?php
// error_reporting(0);
header('Access-Control-Allow-Origin: *');
session_start();

require __DIR__ . "/vendor/autoload.php";

use Stonks\Router\Router;

$router = new Router(ROOT);

/*
 * Contorllers
 */

$router->namespace("Source\App");

/*
 * Web
 */
$router->group(null);
$router->get("/", "Web:home", "web.home");
$router->get("/exportData/{data}/{sheetId}/{agent}/{team}", "Web:exportData", "web.exportData");
$router->get("/filter/{data}/{agent}/{team}", "Web:filter", "web.filter");
$router->get("/date/{date1}/{date2}/{agent}/{team}", "Web:date", "web.date");

$router->get("/sheet", "Web:sheet", "web.sheet");
$router->get("/createsheet/{sheetName}", "Web:createSheet", "web.createSheet");
$router->get("/writesheet", "Web:writeSheet", "web.writeSheet");
$router->get("/appendsheet", "Web:appendSheet", "web.appendSheet");
$router->get("/signout", "Web:signout", "web.signout");

/*
 * ERROS
 */
$router->group("ooops");
$router->get("/{errcode}", "Web:error", "web.error");

/**
 * PROCESS
 */
$router->dispatch();

if ($router->error()) {
    $router->redirect("/ooops/{$router->error()}");
}
