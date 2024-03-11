<?php
// Include demo file if it exists (might be deprecated in the future)
if (file_exists("demo.php")) {
    include_once "demo.php";
}


// Set development environment details if available
if (isset($devEnviourment)) {
    $devEnviourment = $config["devEnviourment"];
}

// Disable build flag if set
if (isset($buildFlag)) {
    global $buildFlag;
    $buildFlag = false;
}

// Get the requested URL
$requestUrl = $_SERVER['REQUEST_URI'];

// Route the request
// Read route mappings from JSON file
$routes = json_decode(file_get_contents('routes.json'), true)["routes"];

/* If buildFlag is true, redirect to Bin.Directory */
if ($buildFlag == true) {
    logConsole("Heading towards $requestUrl/Bin");
    header("$requestUrl/Bin");
    exit;
}

/* 
    Load a view based on provided parameters.
    If build mode is off, process HTML file to PHP and include.
    Otherwise, include PHP file directly.
*/
function loadView($viewName, $data = array(), $args = array()): void
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
        logConsole("Getting the View Live because Build mode is off");
        $processedHtml = processHTMLFile("Static/View/$viewName.html");

        createFolder("Temp View Folder", "./.tempviews");

        file_put_contents("./.tempviews/$viewName.php", $processedHtml);

        include "./.tempviews/$viewName.php";
    } else {
        include_once "Static/View/$viewName.php";
    }

}

// Log message to browser console
function logConsole($message): void
{
    echo "<script>console.log(\"$message\"); </script>";
}

// Get relative URL from absolute URL
function getRelativeUrl($url): string
{
    return str_replace($_SERVER['DOCUMENT_ROOT'], "", str_replace("\\", "/", __DIR__));
}

// this function will return array of queries
function getQueries($stringofQueries = null): array
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

/* 
    Handle server requests by including controller files,
    parsing requested URL and queries, and calling associated controller methods.
*/
function handleServerRequestes(): void
{
    global $requestUrl;
    global $routes;

    // Include all controller files
    foreach (glob("Controllers/*.php") as $file) {
        include_once $file;
    }

    // Parse requested URL and queries
    $tempRequest = explode("?", str_replace(getRelativeUrl($requestUrl), "", $requestUrl));
    $relativeRequestUrl = $tempRequest[0];

    $relativeQuery = isset($tempRequest[1]) ? getQueries($tempRequest[1]) : getQueries(null);

    // var_dump($tempRequest[1]);
    // var_dump($relativeQuery);

    // Match routes and call associated controller methods
    if (array_key_exists($relativeRequestUrl, $routes)) {
        $routeParts = explode('@', $routes[$relativeRequestUrl]);

        $controllerName = $routeParts[0];
        $actionName = $routeParts[1];

        if (class_exists($controllerName)) {
            $controller = new $controllerName();

            if (method_exists($controller, $actionName)) {
                call_user_func_array(array($controller, $actionName), $relativeQuery);
            } else {
                http_response_code(404);
                echo "404 Method Does not exists: ($actionName)";
            }
        } else {
            http_response_code(404);
            echo "404 Controller Does not exists: ($controllerName)";
        }

    } else {
        http_response_code(404);
        echo "404 Requested URI is not present in Route Table : No Controller Found ($relativeRequestUrl)";
    }
}

/* 
    Process an HTML file by converting {{}} to PHP tags.
    Returns the converted content.
*/
function processHTMLFile($filePath)
{
    // $patterns = array(
    //     "globalcode"=> "/{{(.*?)}}/", // Define patterns for {{}}
    //     "if" => '/@if\((.*?)\) {(.*?)}/s', // Define the pattern for matching @if(condition) { ... }@ blocks
    //     "elseif" => "/@elseif\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @elseif(condition) { ... }@ blocks
    //     "else" => "/@else {(.+?)}@/s", // Define the pattern for matching @else { ... }@ blocks
    //     "while" => "/@while\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @while(condition) { ... }@ blocks
    //     "for" => "/@for\((.*?);(.*?);(.*?)\) {(.+?)}@/s", // Define the pattern for matching @for(init; condition; increment) { ... }@ blocks
    //     "switch" => "/@switch\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @switch(expression) { ... }@ blocks
    // );

    $patterns = array(
        '/{{(.*?)}}/' => "<?php $1 ?>", // Define patterns for {{}}
        '/@if\((.*?)\) {(.*?)/s' => "<?php if($1) { $2 ?>", // Define the pattern for matching @if(condition) { ... }@ blocks
        '/(.*?)}@/s' => "<?php $1 } ?>",
        // "elseif" => "/@elseif\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @elseif(condition) { ... }@ blocks
        // "else" => "/@else {(.+?)}@/s", // Define the pattern for matching @else { ... }@ blocks
        // "while" => "/@while\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @while(condition) { ... }@ blocks
        // "for" => "/@for\((.*?);(.*?);(.*?)\) {(.+?)}@/s", // Define the pattern for matching @for(init; condition; increment) { ... }@ blocks
        // "switch" => "/@switch\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @switch(expression) { ... }@ blocks
    );

    $content = "?>" . file_get_contents($filePath);

    // Loop through each pattern and convert the brackets to PHP syntax
    foreach ($patterns as $pattern => $replacement) {
        $content = convertToPHP($pattern,$replacement, $content);
    }

    return $content;
}

// Handle server requests
handleServerRequestes();
