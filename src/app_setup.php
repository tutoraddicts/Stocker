<?php

require_once ("./util/DB.php");
require_once ("./util/file_handeler.php");

/**
 * This File will handle other Processes which are needed to be done before running the application
 */

$FLAGS = array(
    "__UPDATE_DATABASE__" => false
);

$config = json_decode(file_get_contents("config.json"), false);

if ($argc > 1) {
    // access command line arguments starting from index 1
    for ($i = 1; $i < $argc; $i++) {
        switch ($argv[$i]) {
            case "-get_all_db_data":
                get_all_db_data();
                break;
            case "-init_db":
                init_db();
                break;
            case "-update_tables":
                update_tables();
                break;
            case "-build":
                printf("building the application");
                build();
                break;
            case "-start":
                echo "Started Server Creation Process";
                $i++;
                start_server($argv[$i]);
                // $argv[($i + 1)]
                break;
            case "-help":
                print_help();
                break;
            case "-newController":
                CreateController($argv[++$i]);
                break;
            case "-removeController":
                RemoveController($argv[++$i]);
                break;
            case "-create_db_table":
                CreateDBTable($argv[++$i]);
                break;
            case "-remove_db_table":
                RemoveDBTable($argv[++$i]);
                break;
            default:
                echo "The value is neither 1, 2, nor 3 you inserted $argv[$i]";
                print_help();
                break;
        }
    }
} else {
    echo "No command line arguments provided.\n";
}

function waitForEnter() {
    // Read user input from the command line
    fgets(STDIN);
}

/**
 * Connect to the database
 * @return mixed mysqli|null
 */
function connect_to_db()
{

    global $config;
    // Check connection
    return new mysqli($config->database->host, $config->database->userName, $config->database->password, $config->database->db);
}

function alter_db_tables(&$db_connection, &$table_name, &$table_columns)
{
    /**
     * This function will alter the existing table if there are new colums added in the table
     * @param mysqli $db_connection - send the connection of the DB
     * @param string $table_name - name of the table
     * @param array $table_columns - associative array of the table attributes "attribute" => "defination of the attribute"
     * 
     * @return bool - true if successfully atltered the table or not required to alter the table false if not
     */

    echo "Altering : $table_name :\n";
    // Get all columns which are already create in the database
    $all_created_columns = array();
    $result = $db_connection->query("DESCRIBE $table_name;");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["Field"] == "id") {
                continue;
            }
            print_r($row);
            $all_created_columns[] = $row["Field"];
        }
    }
    // Done Getting all the columns now have to check which column is not there in the table and alter it
    $object_colum_names = array_keys($table_columns);
    $colums_to_add = array_diff($object_colum_names, $all_created_columns);

    if (!empty($colums_to_add)) {
        foreach ($colums_to_add as $colum_name) {
            // create add the column
            $column_type = &$table_columns[$colum_name];
            $alterSql = "ALTER TABLE $table_name ADD COLUMN $colum_name $column_type";
            if ($db_connection->query($alterSql) === TRUE) {
                echo "Added column: $colum_name in the table $table_name\n";
            } else {
                echo "Error adding column  $colum_name in  table $table_name: " . $db_connection->error . "\n";
            }
        }
    } else {
        echo "Nothing to add in the Table $table_name \n";
    }



    $colums_to_remove = array_diff($all_created_columns, $object_colum_names);
    if (!empty($colums_to_remove)) {
        foreach ($colums_to_remove as $colum_name) {
            // delete the column from the table
            $alterSql = "ALTER TABLE $table_name DROP COLUMN $colum_name";
            if ($db_connection->query($alterSql) === TRUE) {
                echo "Removing column: $colum_name in the table $table_name \n";
            } else {
                echo "Error Removing column  $colum_name in  table $table_name: " . $db_connection->error . "\n";
            }
        }
    } else {
        echo "Nothing to remove in the Table $table_name \n";
    }


    unset($result);

    return true;

    // foreach ($table_columns as $attribute => $attribute_type) {
    //     global $all_created_columns;
    //     // Logic - if the element is not in the array(which has existed table attributes) then will alter the table with new element 
    //     if (!in_array($attribute, $all_created_columns)) {
    //         $alterSql = "ALTER TABLE $table_name ADD COLUMN $attribute $attribute_type";

    //     }
    // }

}


function setup_db(&$db_connection = null, $db_name): bool
{
    global $config;

    /**
     * Create Database if not there already
     * @param mysqli $db_connection - connection object if the db
     * @param string $db_name - database name which to create
     */
    if ($db_connection == null) {
        $db_connection = new mysqli($config->database->host, $config->database->userName, $config->database->password);
    }


    if ($db_connection->connect_error) {
        echo "database connection failed: $db_connection->connect_error";
        return false;
    }

    echo "database connection established\n";


    $sql_create_database = "CREATE DATABASE IF NOT EXISTS $db_name";

    if ($db_connection->query($sql_create_database) === TRUE) {
        $db_connection->select_db($db_name);
        return true;
    } else {
        return false;
    }
}

function create_db_table(&$db_connection, &$table_name, &$table_columns): bool
{
    /**
     * @param $db_connection - take the connection object of the Db where you want to create the table
     * @param $table_name - name of the table
     * @param $table_object - object of all the table class
     * 
     * @return bool - true if the table created successfully false if not
     */

    // echo print_r($table_columns);

    $temp_query = "CREATE TABLE IF NOT EXISTS $table_name ( id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY";

    // k for key v for value
    foreach ($table_columns as $k => $v) {
        $temp_query .= ",$k $v";
    }

    $temp_query .= " )";
    echo "Query to create table $temp_query";

    if ($db_connection->query($temp_query)) {
        echo "Table ($table_name) is successfully created\n";
        return true;
    }
    return false;
}

function get_db_tables(): array
{
    /**
     * Create objects of all the databases and store them in a associative array with the table_name => object of the table
     * @return array - array of table structure with "table_name" = "array(table_columns/ attributes)"
     */

    $tables = glob("databases/*.php");

    $array_of_tables = [];

    // table is the path of the database.php files
    foreach ($tables as $table) {
        global $array_of_tables;
        // $replacePattern = . '//(.*?).php/';
        $table_name = str_replace("databases/", "", $table);
        $table_name = str_replace(".table.php", "", $table_name);

        include_once ($table);

        $temp_table = new $table_name();
        $array_of_tables[$table_name] = $temp_table->{$table_name};
        echo "succecfully ($table_name) table Object is created\n";
    }

    return $array_of_tables;
}

function init_db(): void
{
    /**
     * This function will do all the task with the db 
     * example it will create database if there are non
     * then it will create tables if there are non 
     * if there are table existed then it will alter that table if needed
     */

    // $databse_connection = connect_to_db();
    // Connect and Create the Database

    global $config;

    if (!setup_db($db_connection, $config->database->db)) {
        echo "Failed on DB Creation : (" . $config->database->db . ")";
        return;
    }

    $db_connection = connect_to_db();

    if ($db_connection) {
        echo "SuccessFully Connect to the Databse";
    }else {
        echo "Some error happens not able to connect to databse";
        return;
    }

    // Getting array of tables and table objects
    $tables = get_db_tables();
    var_dump($tables);

    foreach ($tables as $table_name => $table_columns) {
        create_db_table($db_connection, $table_name, $table_columns);
    }

    foreach ($tables as $table_name => $table_columns) {
        alter_db_tables($db_connection, $table_name, $table_columns);
    }

}

function update_tables()
{
    global $config;

    $db_connection = connect_to_db();
    ;

    if ($db_connection == null) {
        echo "Failed to setup DataBase\n";
        return;
    }

    if (!setup_db($db_connection, $config->database->db)) {
        echo "Failed on DB Creation : (" . $config->database->db . ")";
        return;
    }


    // Getting array of tables and table objects
    $tables = get_db_tables();
    foreach ($tables as $table_name => $table_columns) {
        alter_db_tables($db_connection, $table_name, $table_columns);
    }
}

function get_all_db_data()
{
    global $config;

    $db_connection = connect_to_db();

    if ($db_connection == null) {
        echo "Failed to setup DataBase\n";
        return;
    }

    if (!setup_db($db_connection, $config->database->db)) {
        echo "Failed on DB Creation : (" . $config->database->db . ")";
        return;
    }

    // Getting array of tables and table objects
    $tables = get_db_tables();

    foreach ($tables as $table_name => $table_columns) {
        echo "Printing Data of $table_name: \n";
        $result = $db_connection->query("SELECT * FROM $table_name");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                print_r($row);
            }
        }
    }
}

/**
 * Check if a file exists and delete it.
 *
 * @param string $filePath The path to the file to be checked and deleted.
 * @return bool Returns true if the file was successfully deleted, false otherwise.
 */
function deleteFileIfExists($filePath) {
    // Check if the file exists
    if (file_exists($filePath)) {
        // Attempt to delete the file
        if (unlink($filePath)) {
            // File successfully deleted
            return true;
        } else {
            // File deletion failed
            return false;
        }
    } else {
        // File does not exist
        return false;
    }
}

function copyDirectory($source, $destination)
{
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $destPath = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ($item->isDir()) {
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
        } else {
            copy($item, $destPath);
        }
    }
}

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return false;
    }

    if (!is_dir($dir)) {
        return unlink($dir); // Delete the file if it's not a directory
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }

    return rmdir($dir); // Remove the parent directory
}

function build(): void
{
    global $config;

    if ($config->build == true) {
        echo "You are currently in build directory | Skipping build process";
        exit - 1;
    }
    $build_dir = "../build";
    // This Function will build the application

    deleteDirectory($build_dir);

    // Create build folder outside the src directory
    if (!file_exists($build_dir)) {
        mkdir($build_dir); // Creates a new directory with the specified name
        echo "Directory '$build_dir' created successfully.";
    } else {
        echo "Directory '$build_dir' already exists.";
    }

    // Copy all the files and folder in to build directory
    $sourceDir = getcwd(); // Current directory
    copyDirectory($sourceDir, $build_dir);

    // Generate php files
    $iterator = new DirectoryIterator("$build_dir/Static/view");

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isDot()) {
            $processedHtml = processHTMLFile("$build_dir/Static/view/" . $fileInfo->getFilename());
            unlink("$build_dir/Static/View/" . $fileInfo->getFilename());
            file_put_contents("$build_dir/Static/view/" . $fileInfo->getFilename() . ".php", $processedHtml);
        }
    }

    deleteDirectory("$build_dir/.tempviews");

    //  Change Build Flag
    $config = json_decode(file_get_contents("$build_dir/config.json"), false);
    $config->build = true;
    file_put_contents("$build_dir/config.json", json_encode($config));

}

function start_server($port): void
{
    //  Start the server in port $port
    echo "Determining Port number: $port";
    // Set the address and port
    $address = 'localhost';
    if ($port == null) {
        global $config;

        if ($config->build == true) {
            $port = 80;
        } else {
            $port = 8080;
        }
    }

    echo "Port number Determined: $port \n";
    // Command to start the PHP built-in server
    echo "Server started at http://$address:$port \n";

    // Execute the command
    $pid = shell_exec("php -S $address:$port ./util/util.php > server.log 2>&1");

    echo "Server started at http://$address:$port with PID $pid\n";


}

/**
 * Create a new controller with basic needs of a controller and created a view for it
 */
function CreateController(string $controllerName)
{
    $file_name = "{$controllerName}Controller.php";
    $controllerLocation = "controllers";

    $controller_content = "<?php
    class {$controllerName}Controller
    {
        public function $controllerName(...\$args)
        {
        // do the action here 
        // rather than \$args you can also mention variables which you are passing via URL POST or GET both will work example you are passing a product name then you can memtiom
        // public function $controllerName(\$product_name, ...\$args)

        \$data = [
            \"sampledata\" => \"sample data content of $controllerName\",
        ];

        loadView(
            \"$controllerName\",
            \$data,
            \$args
        );
        }

    }
    ";

    if (file_put_contents("$controllerLocation/$file_name", $controller_content)) {
        echo "Successfully Created the Cntroller in $controllerLocation/$file_name";
    }

    $view_file_name = "{$controllerName}.html";
    $view_location = "Static/view";

    $view_content = "
<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{echo \"$controllerName\"}}</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"Static/Styles/$controllerName.css\">
</head>

<body>
{{ echo \$sampledata }}
</body>

<script src=\"Static/js/$controllerName.js\"></script>

</html>
    ";

    if (file_put_contents("$view_location/$view_file_name", $view_content)) {
        echo "Successfully Created the View for $controllerName \n";
    }

    $css_file_name = "{$controllerName}.css";
    $css_file_location = "Static/styles";

    $css_content = "
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #ffffff; /* white background */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
    ";

    if (file_put_contents("$css_file_location/$css_file_name", $css_content)) {
        echo "Successfully Created the Css for $controllerName \n";
    }

    $js_file_name = "{$controllerName}.js";
    $js_file_location = "Static/js";

    $js_content = "
    console.log(\"$controllerName Controller Js\");
    ";

    if (file_put_contents("$js_file_location/$js_file_name", $js_content)) {
        echo "Successfully Created the js for $controllerName \n";
    }

}

/**
 * Function to Delete all the Controller Related Files
 */
function RemoveController(string $controllerName) {

    echo "Removing Controller - $controllerName\n";
    $file_name = "{$controllerName}Controller.php";
    $controllerLocation = "controllers";
    deleteFileIfExists("$controllerLocation/$file_name");

    echo "Removing View - $controllerName\n";
    $view_file_name = "{$controllerName}.html";
    $view_location = "Static/view";
    deleteFileIfExists("$view_location/$view_file_name");

    echo "Removing Style - $controllerName\n";
    $css_file_name = "{$controllerName}.css";
    $css_file_location = "Static/styles";
    deleteFileIfExists("$css_file_location/$css_file_name");

    echo "Removing js - $controllerName\n";
    $js_file_name = "{$controllerName}.js";
    $js_file_location = "Static/js";
    deleteFileIfExists("$js_file_location/$js_file_name");
}

/**
 * Function to Create a basic table file in the Databses folder and ask the user to update that and update the databse
 */
function CreateDBTable (string $table_name) {
    $table_file_name = "$table_name.table.php";
    $table_location = "databases";

    $table_content = "
<?php
class $table_name extends DB
{
    public \$$table_name = array(
        \"{$table_name}_name\" => \"VARCHAR(30)\",
    );
}

    ";

    if (file_put_contents("$table_location/$table_file_name", $table_content)) {
        echo "Successfully Created the DB Table Record in $table_location/$table_file_name \n";
    }

    echo "Please check the file $table_location/$table_file_name and Update Table Structure as needed. in the varaible $table_name\n";
    echo " Then Press Enter to continue....\n";
    waitForEnter();
    echo "Upating Databse Tables \n";

    init_db();

}

function RemoveDBTable (string $table_name) {

    echo "Are you sure to delete the Table - $table_name\n";
    echo "Be aware that all the the data will be removed from the databse\n";
    echo "Press Enter if you want to continue......\n";
    waitForEnter();

    echo "Removing DB Table - $table_name\n";
    $table_file_name = "$table_name.table.php";
    $table_location = "databases";
    deleteFileIfExists("$table_location/$table_file_name");

    $databse = connect_to_db();

    $databse->query("DROP TABLE $table_name");
}

function print_help()
{
    echo "Help:\n";

    echo "--------------------------\n";
    echo "-get_all_db_data : get all the Database of tables and Print them in console |  this command was introduced for testing perpose only\n";
    echo "-init_db : Initialise DB and create out Databses\n";
    echo "-update_tables : Update all the tables attributes\n";
    echo "-build : build the appliation | this build mode will improve the application performance for bigger websites\n";
    echo "-start : Start a Server in the port user mentioned | Example -start <port_number>\n";
    echo "-newController <controller_name> | create a new controller under controllers folder";
    echo "-removeController <controller_name> | to remove all the controller files";
    echo "-help : Print Aavailable arguments\n";
}