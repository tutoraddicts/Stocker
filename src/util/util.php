<?php

require_once "file_handeler.php";

// session_start();

/*  Some nessesery functions for use  */
// Get the requested URL
$requestUrl = $_SERVER['REQUEST_URI'];
$routes = json_decode(file_get_contents('routes.json'), true)["routes"];
$config = json_decode(file_get_contents("config.json"), false);


// Log message to browser console
function logconsole($message): void
{
    echo "<script>console.log(\"$message\"); </script>";
}

// Function to rediredct 
function redirect($url, $statusCode = 302): void
{
    global $requestUrl;
    header('Location: ' . $url);
    die();
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

// Get relative URL from absolute URL
function getRelativeUrl(&$url): string
{
    // echo $_SERVER['DOCUMENT_ROOT'];
    // echo str_replace("/Stocker/src/", "/", $url);
    // echo "</br>";
    return str_replace("/Stocker/src/", "/", $url);
}

function getQueries(&$stringofQueries = null): array
{

    $result = [];

    if ($stringofQueries == null) {
        // Loop through each key-value pair in the $_POST superglobal array
        foreach ($_POST as $key => $value) {
            // Add the key-value pair to the $postData array
            $decodedString = urldecode($value);
            $result[$key] = $decodedString;
        }
    } else {
        // Decode the URL-encoded string
        // $decodedString = urldecode($stringofQueries);
        // Parse the string into an array
        parse_str($stringofQueries, $result);
    }

    return $result;
}


require_once "Controllers.php";
require_once "DB.php";

// Starting the Session after including all the files
session_start();

if (!array_key_exists("Controllers", $_SESSION)) {
    $_SESSION['Controllers'] = new Controllers();
} else if ($_SESSION['Controllers'] === null || $config->holdSession === false) {
    $_SESSION['Controllers'] = new Controllers();
} else {
    logconsole("Controllers Object is already created");
}

if (!array_key_exists("DB", $_SESSION)) {
    $_SESSION['DB'] = new DB();
} else if ($_SESSION['DB'] === null || $config->holdSession === false) {
    $_SESSION['DB'] = new DB();
} else {
    logconsole("DB Object is already created");
}


$Controllers = &$_SESSION['Controllers'];
$DB = &$_SESSION['DB'];



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

function GetClassName($path)
{
    $patten = '/(.*?)\/(.*?).php/';
    return preg_replace($patten, "\$2", $path);
    // $ClassName = str_replace("controllers/", "", $path);
    // $ClassName = str_replace(".php", "", $ClassName);

    // return $ClassName;
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
function createDatabses(): void
{
    global $DB;
    global $devEnviourment;

    $files = glob("databases/*.php");

    foreach ($files as $file) {
        // $replacePattern = . '//(.*?).php/';
        $databaseName = str_replace("databases/", "", $file);
        $databaseName = str_replace(".php", "", $databaseName);

        ////logconsole("Creating Object of Database : $databaseName");
        // $dabaseTables = json_decode(file_get_contents($file), true);
        $DB->{$databaseName} = new $databaseName();
        // var_dump( $DB->{$databaseName});
        // foreach ($dabaseTables as $k => $v) {
        //     //logconsole("Registering Database : $k");
        //     $DB->{$k} = $v;
        // }
        // Setting Table Attributes
    }
}

/* 
    Load a view based on provided parameters.
    If build mode is off, process HTML file to PHP and include.
    Otherwise, include PHP file directly.
*/
function loadView($viewName, &$data = array(), &$args = array()): void
{
    global $buildFlag;

    // Extracting all the data into a variable
    if ($data != array()) {
        extract($data);
    }
    if ($args != array()) {
        // var_dump($args);
        extract($args);
    }

    if ($buildFlag == false) {
        //logconsole("Getting the View Live because Build mode is off");

        createFolder("./.tempviews");

        $processedHtml = processHTMLFile("Static/View/$viewName.html");
        file_put_contents("./.tempviews/$viewName.php", $processedHtml);

        include_once "./.tempviews/$viewName.php";
    } else {
        include_once "Static/View/$viewName.php";
    }

}

require_once "db_tables.php";

// Strore array("className" => $Object)
// if (!array_key_exists('C', $_SESSION)) {
//     $seesion_controller_data = array();
//     foreach (glob("controllers/*.php") as $Controllerfile) {
//         include_once $Controllerfile; // This line includes each PHP file from the Controllers directory
//         $className = GetClassName($Controllerfile);
//         if (class_exists($className)) {
//             ${$className} = $instance = new $className();
//             // var_dump($instance);
//             if (!empty ($instance)) {
//                 $seesion_controller_data["$Controllerfile"] = array(
//                     "$className" => $instance
//                 );
//                 logconsole("Creating Object of $className");
//             } else {
//                 logconsole("Failed to create the Class of $className | because the object is empty");
//             }
//         } else {
//             logconsole("Failed to create the Class of $className | because the class does not exist");
//         }
//         // var_dump($_SESSION['C']["$Controllerfile"]);
//         // echo "\n";

//     }

//     $_SESSION['C'] = $seesion_controller_data;
// } else {
//     $controllers = $_SESSION['C'];
//     foreach ($controllers as $Controllerfile => $classObjects) {
//         include_once $Controllerfile;
//         // logconsole("including File $Controllerfile");
//         // var_dump($classObjects);
//         foreach ($classObjects as $name => $object) {
//             ${$name} = $object;
//             var_dump(${$name});
//             logconsole("Getting the Controller Object $name");
//         }

//     }
// }
// var_dump($_SESSION['C']);

foreach (glob("controllers/*.php") as $Controllerfile) {
    include_once $Controllerfile;    
}

foreach (glob("databases/*.php") as $Controllerfile) {
    include_once $Controllerfile; // This line includes each PHP file from the Controllers directory
}

// Include all controller files
require_once "request_handler.php";
