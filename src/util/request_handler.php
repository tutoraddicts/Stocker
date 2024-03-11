<?php

global $routes;
global $requestUrl;
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
    global $routes;

    global $Controllers;

    // Parse requested URL and queries
    // $tempRequest = explode("?", $requestUrl);
    $tempRequest = explode("?", getRelativeUrl($requestUrl));
    $relativeRequestUrl = $tempRequest[0];
    $relativeQuery = isset($tempRequest[1]) ? getQueries($tempRequest[1]) : getQueries();

    // Match routes and call associated controller methods
    if (array_key_exists($relativeRequestUrl, $routes)) { // Check if the requested URL matches any defined route
        $routeParts = explode('@', $routes[$relativeRequestUrl]); // Split the controller and method name
        $controllerName = $routeParts[0]; // Get the controller name
        $actionName = $routeParts[1]; // Get the method name

        // Check if the controller class exists
        if (class_exists($controllerName)) { // Check if the controller class exists
            $controller = $Controllers->{$controllerName}; // Create an instance of the controller

            // Check if the controller method exists
            if (method_exists($controller, $actionName)) { // Check if the method exists in the controller
                call_user_func_array(array($controller, $actionName), $relativeQuery); // Call the method with the query parameters
            } else {
                // If the method does not exist, return a 404 error
                http_response_code(404);
                echo "404 Method Does not exist: ($actionName)";
            }
        } else {
            // If the controller class does not exist, return a 404 error
            http_response_code(404);
            echo "404 Controller Does not exist: ($controllerName)";
        }
    } else {
        // If the requested URI is not present in the route table, return a 404 error
        http_response_code(404);
        echo "
        404 Requested URI is not present in Route Table: No Controller Found ($relativeRequestUrl)</br>
        There are few Solutions are there </br>
        1. Check the routes.joson for any mistake </br>
        2. If you are running the server from root of the src then comment out line no.26 and uncomment line no.25";
    }
}