<?php

global $routes;
global $requestUrl;



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

// var_dump($requestUrl);

/**
 * Handles incoming server requests by routing them to the appropriate controller methods.
 *
 * This function parses the incoming request URL, matches it with predefined routes, and calls the corresponding
 * controller method. It loads all controller files, matches the requested URL with the defined routes, and executes
 * the associated controller method if the route is found. If the route is not found, it returns a 404 error.
 *
 * @return void
 */
function handleServerRequestes(): void
{
    // Global variables to store request URL and routes
    global $requestUrl;
    global $config;

    // Parse requested URL and queries
    $tempRequest = explode("?", $requestUrl);
    $relativeRequestUrl = &$tempRequest[0];
    $relativeQuery = isset($tempRequest[1]) ? getQueries($tempRequest[1]) : getQueries(); // if any request came from url that means that is Get query else it is POST

    // echo $relativeRequestUrl;
    logconsole("Relative requested url is $relativeRequestUrl");

    $reuquested_methods = explode('/', $relativeRequestUrl); // if I am sending /login/failed then it will devide it in three arrays
    // By default the class will be in index number 1 later on it will be functions means index 2 will be function name


    $controllerName = $reuquested_methods[1] != "" ? $reuquested_methods[1] : ($config->defaultController != "" ? $config->defaultController : ""); // Get the controller name
    $controllerClassName = "{$controllerName}Controller";
    $actionName =(isset($reuquested_methods[2]) && $reuquested_methods[2] != "") ? $reuquested_methods[2] : ($controllerName != "" ? $controllerName : ( $config->defaultController != "" ? $config->defaultController : "Controller") );
    // $test = isset($reuquested_methods[2]) && $reuquested_methods[2] != "";
    // logconsole("user requested action is : ({$reuquested_methods[2]}) -- $test -- $actionName" );


    if (class_exists($controllerClassName)) { // Check if the controller class exists
        $controller = new $controllerClassName(); // Create an instance of the controller
        logconsole("Controller Exist $controllerClassName");
        // Check if the controller method exists
        if (method_exists($controller, $actionName)) { // Check if the method exists in the controller
            call_user_func_array([$controller, $actionName], $relativeQuery); // Call the method with the query parameters
            logconsole("loading $controllerClassName->$actionName");
        } else {
            // If the method does not exist, return a 404 error
            http_response_code(404);
            echo "404 Method Does not exist: ($actionName) in class ($controllerClassName)";
        }
    } else {
        // If the controller class does not exist, return a 404 error
        http_response_code(404);
        echo "
            404 Requested Controller is not present in the application: No Controller Found ($controllerClassName)</br>
            There are few Solutions are there </br>
            1. Recheck if the Route Table is there";
    }
}

// Function to rediredct 
function redirect($url, $statusCode = 302): void
{
    global $requestUrl;
    header('Location: ' . $url);
    die();
}