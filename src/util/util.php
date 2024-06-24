<?php

require_once "file_handeler.php";
require_once "DB.php";
require_once "view_handler.php";

session_start();

/*  Some nessesery functions for use  */
// Get the requested URL
$requestUrl = $_SERVER['REQUEST_URI'];
$routes = json_decode(file_get_contents('routes.json'), true)["routes"];
$config = json_decode(file_get_contents("config.json"), false);

$buildFlag = $config->build;


// Log message to browser console
function logconsole($message): void
{
    echo "<script>console.log(\"$message\"); </script>";
}

// Get our current URL
function getCurrentUrl()
{
    return $_SESSION['REQUEST_URI'];
}

function isUserLoggedin()
{
    return isset ($_SESSION['user_name']);
}

function createControllers(): void
{
    global $Controllers;
    global $devEnviourment;

    // var_dump($Controllers);

    $files = glob("controllers/*.php");

    foreach ($files as $file) {
        // $replacePattern = . '//(.*?).php/';
        $controllerName = str_replace("controllers/", "", $file);
        $controllerName = str_replace(".php", "", $controllerName);

        // //logconsole("Registering controller : $controllerName");
        $Controllers->{$controllerName} = $controllerName;
    }
}

/**
 * Create Object of the controller and databases
 * @param string $path - Path of the php file
 * @return stdclass - object of the class
 */
function createObject($ClassName)
{
    return new $ClassName();
}

if ($_SESSION['DB'] != null || $config->holdSession === true) {
    $DB = &$_SESSION['DB'];
}
else if ($_SESSION['DB'] === null || $config->holdSession === false) {
    $_SESSION['DB'] = $DB = new DB();
}
else if (!array_key_exists("DB", $_SESSION)) {
    $_SESSION['DB'] = $DB = new DB();
} else {
    logconsole("DB Object is already created");
    $DB = &$_SESSION['DB'];
}



require_once "db_tables.php";
foreach (glob("controllers/*.php") as $Controllerfile) { include_once $Controllerfile; }
foreach (glob("databases/*.php") as $Controllerfile) { include_once $Controllerfile; }
require_once "request_handler.php";


// echo "testing";
handleServerRequestes();

