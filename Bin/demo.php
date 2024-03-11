<?php

/* Global Variables */
$devEnviourment = $config["devEnviourment"]; // enviourment details for the developent

$buildFlag = $config["-build"]; // determine is it going to be build then test or run directly in our development area

/* Coping our website to bin and build it */
function Build()
{
    global $devEnviourment;

    buildBinFolder();

    copyDirectory(".", "../Bin");

    // // Copy all the Static Files 
    // // Copy our Rute.json table         ./Bin                       /   route.json
    // copy($devEnviourment["routeFile"], getBuildDir($devEnviourment["Root"] . "/" . $devEnviourment["routeFile"]) );
    // // Copy our config.json table         ./Bin                       /   route.json
    // copy($devEnviourment["configFile"], getBuildDir($devEnviourment["Root"] . "/" . $devEnviourment["configFile"]) );

    // // Copy all Css Files
    // copyDirectory($devEnviourment["stylesFolder"], getBuildDir($devEnviourment["stylesFolder"]) );
    // // Copy View Folder
    // copyDirectory($devEnviourment["viewFolder"], getBuildDir($devEnviourment["viewFolder"]) );

    // // Copy all Controllers
    // copyDirectory($devEnviourment["controllersFolder"], getBuildDir($devEnviourment["controllersFolder"]) );

    /*  Process all Html Files to change {{}} to <?php ?> */
    processHTMLFiles();

    processConfig();

}


// Changing the not required values in Config
function processConfig()
{
    global $devEnviourment;

    $buildConfigPath = getBuildDir($devEnviourment['configFile']);
    $_config = json_decode(file_get_contents($buildConfigPath), true);

    // modify the build flag
    $_config["-build"] = false;

    file_put_contents($buildConfigPath, json_encode($_config), );
}


function getBuildDir($devPath)
{
    global $devEnviourment;
    return $devEnviourment["BinRoot"] . "/" . $devPath;
}
// This function will create out bin folder and create other folder needed inside it
function buildBinFolder()
{
    global $devEnviourment;
    // Deleting the Folder if it is there
    deleteFolderContents(getBuildDir($devEnviourment["Root"]));

    // Create new Bin folder if it is not there
    foreach ($devEnviourment as $_x => $_path) {
        if (is_dir(getBuildDir($devEnviourment[$_x]))) {
            createFolder($_x, $_path);
        }
    }

    // create our index.php
    // file_put_contents(getBuildDir($devEnviourment["indexFile"]), 'include "route.php"') ;
}

function generateJavascriptFromHTML($htmlFile)
{
    // Read the HTML file
    $htmlContent = file_get_contents($htmlFile);

    // Replace all occurrences of {{ }} with appropriate JavaScript code
    $jsContent = preg_replace_callback("/{{(.*?)}}/", function ($matches) {
        $jsCode = trim($matches[1]);
        return "<script>{$jsCode}</script>";
    }, $htmlContent);

    return $jsContent;
}

function convertToPHP($pattern, $replacement, $input)
{
    $output = preg_replace($pattern, $replacement, $input);
    return $output;
}

// Function to process HTML files
function processHTMLFiles()
{
    global $devEnviourment;

    $patterns = array(
        "globalcode" => "/{{(.*?)}}/", // Define patterns for {{}}
        "if" => '/@if\((.*?)\) {(.*?)}/s', // Define the pattern for matching @if(condition) { ... }@ blocks
        "elseif" => "/@elseif\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @elseif(condition) { ... }@ blocks
        "else" => "/@else {(.+?)}@/s", // Define the pattern for matching @else { ... }@ blocks
        "while" => "/@while\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @while(condition) { ... }@ blocks
        "for" => "/@for\((.*?);(.*?);(.*?)\) {(.+?)}@/s", // Define the pattern for matching @for(init; condition; increment) { ... }@ blocks
        "switch" => "/@switch\((.*?)\) {(.+?)}@/s", // Define the pattern for matching @switch(expression) { ... }@ blocks
    );

    $directory = getBuildDir($devEnviourment["viewFolder"]);

    // Get all HTML files in the directory
    $files = glob($directory . "/*.html");

    // Process each file
    foreach ($files as $file) {
        // Read the content of the HTML file
        $content = "?>" . file_get_contents($file);

        // Loop through each pattern and convert the brackets to PHP syntax
        foreach ($patterns as $pattern) {
            $content = convertToPHP($pattern,  $content);
        }

        // Change file extension to .php
        $newFileName = str_replace(".html", ".php", $file);

        // Write the modified content to the new PHP file
        file_put_contents($newFileName, $content);

        deleteFile($file);
        // Optionally, you can delete the original HTML file
        // unlink($file);
    }
}

function deleteFolderContents($folderPath)
{
    // Get list of all files and directories in the folder

    if (!is_dir($folderPath)) {
        logConsole("Folder does not exist.");
        return;
    }

    $files = glob($folderPath . "/*");

    // Loop through each item
    foreach ($files as $file) {
        // If it"s a file, delete it
        if (is_file($file)) {
            unlink($file);
        }
        // If it"s a directory, recursively call deleteFolderContents function
        elseif (is_dir($file)) {
            deleteFolderContents($file);
        }
    }
}

/* 
Foldername - is to just keep tract of the log
FolderPath - is to create the folder
*/
function createFolder($folderName, $folderPath)
{
    // Check if the Bin folder exists
    if (!file_exists($folderPath) || !is_dir($folderPath)) {
        // If it doesn"t exist, create it
        if (mkdir($folderPath)) {
            logConsole(" $folderName folder created successfully.</br>");
        } else {
            logConsole("Failed to create Bin folder.</br>");
        }
    } else {
        logConsole(" $folderName folder already exists.</br>");
    }
}

/* 
$source: source Directory,
$destination: Destination Directory,
$contents: a content which I want each file to have in the begining
*/
function copyDirectory($source, $destination, $content = "")
{
    // Check if the source directory exists
    if (!is_dir($source)) {
        return false;
    }

    // Create the destination directory if it doesn"t exist
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    // Open the source directory
    $dir = opendir($source);

    // Loop through all files and subdirectories in the source directory
    while (($file = readdir($dir)) !== false) {
        // Skip "." and ".." directories
        if ($file == "." || $file == ".." || $file == "Bin") {
            continue;
        }

        $sourceFile = $source . "/" . $file;
        $destinationFile = $destination . "/" . $file;

        // If the current item is a directory, recursively copy it
        if (is_dir($sourceFile)) {
            copyDirectory($sourceFile, $destinationFile);
        } else {
            // If it"s a file, copy it
            file_put_contents($destinationFile, $content . file_get_contents($sourceFile));
        }
    }

    // Close the source directory
    closedir($dir);

    return true;
}

function deleteFile($filePath)
{
    // Check if the file exists before attempting to delete it
    if (file_exists($filePath)) {
        // Attempt to delete the file
        if (unlink($filePath)) {
            logConsole("File deleted successfully.");
        } else {
            logConsole("Error deleting file.");
        }
    } else {
        logConsole("File does not exist.");
    }
}
// // Directory containing HTML files
// $htmlDirectory = "/path/to/your/html/files";

// // Call the function to process HTML files
// processHTMLFiles($htmlDirectory);

// echo "Conversion completed successfully!";

if ($buildFlag == true) {
    Build();
}
