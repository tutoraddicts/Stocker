<?php
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
        logconsole("Getting the View Live because Build mode is off");
        logconsole("Getting Dynamic View");
        createFolder("./.tempviews");

        $processedHtml = processHTMLFile("Static/View/$viewName.html");
        file_put_contents("./.tempviews/$viewName.php", $processedHtml);

        include_once "./.tempviews/$viewName.php";
    } else {
        include_once "Static/View/$viewName.html.php";
    }

}