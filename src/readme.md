# Application Documentation

## Table of Contents
- [Application Documentation](#application-documentation)
  - [Table of Contents](#table-of-contents)
  - [Folder Tree](#folder-tree)
  - [Setup Database](#setup-database)
    - [1. Create Database and Create Tables](#1-create-database-and-create-tables)
    - [2. Update the Table Structure according to the update](#2-update-the-table-structure-according-to-the-update)
  - [Syntax of the View](#syntax-of-the-view)
  - [How To](#how-to)
    - [Create a Database File](#create-a-database-file)
    - [Create Constructure File](#create-constructure-file)
    - [Get Data From Data base](#get-data-from-data-base)

## Folder Tree

└───src

    │   .htaccess
    │   app_setup.php
    │   config.json
    │   index.php
    │   readme.md
    │   routes.json
    │
    ├───.tempviews
    │       index.php
    │       login.php
    │       register.php
    │
    ├───controllers
    │       HomeController.php
    │       LoginController.php
    │       RegisterController.php
    │
    ├───databases
    │       Products.php
    │       Users.php
    │
    ├───static
    │   ├───styles
    │   │       login.css
    │   │       main.css
    │   │
    │   └───view
    │           index.html
    │           login.html
    │           register.html
    │
    └───util
            Controllers.php
            DB.php
            db_tables.php
            demo.php.backup
            file_handeler.php
            request_handler.php
            util.php


## Setup Database

### 1. Create Database and Create Tables 

Run the app_setup.php script with the -init_db option. This script will connect to the database server specified in the config.json file and create the necessary database schema if it does not already exist. It will also create all the required tables defined in the databases folder.
```bash
php .\app_setup.php -init_db
```
This command will initialize the database and create the required tables for the application to function properly. Make sure to review the config.json file to ensure that the database connection details are correct before running this command.

### 2. Update the Table Structure according to the update 

If any changes have been made to the table structure or if new tables need to be added, follow these steps to update the database schema:

Run the app_setup.php script with the -update_tables option. This script will compare the existing table structure in the database with the definitions provided in the databases folder and make any necessary alterations to synchronize them.

```bash
php .\app_setup.php -update_tables
```

This command will update the table structure in the database to reflect any changes made to the table definitions in the application. It's important to run this command after making any modifications to the table schema to ensure data integrity and consistency.


## Syntax of the View

1. Variables: To print a variable value, use {{ echo $variableName; }}.
For example, {{ echo $PageName; }} will print the value of $PageName.
2. Conditional Statements: To include conditional statements, use @if and @endif.
For example, @if ($products) checks if the $products variable is set.
You can use @else and @endif for an else block.
3. Loops: To loop through an array, use @foreach and @endforeach.
For example, @foreach ($products as $product_name => $data) loops through each product in the $products array.
Inside the loop, you can print the value using {{ echo $product_name; }}.
4. HTML Structure: The HTML structure remains the same, and you can integrate Blade syntax where needed.
For example, <h1>{{ echo $PageName; }}</h1> prints the value of $PageName within an <h1> tag.
Comments:

Blade comments can be added using {{-- Comment here --}}.
For example, {{-- Add more products here --}} is a comment.


## How To

### Create a Database File

```php 
<?php

/**
 * Define a class for the TableName table extending the DBTables class
 */
class TableName extends DBTables
{
    /**
     * Define a public property with the same name as the class for the table structure
     * The first element of the array should be the primary key attribute
     */
    public $TableName = array(
        "column1" => "data_type", // The first element is the primary key attribute
        "column2" => "data_type",
        // Add more columns as needed
    );

    /**
     * Define any additional methods or functions related to the TableName table
     */
}

?>

```

Replace TableName with the actual name of your table, and define the columns and their data types in the $TableName property. Additionally, you can add any custom methods or functions specific to this table within the class.

This template provides a basic structure for defining a table class in your application. Make sure to customize it according to your actual table schema and requirements.

### Create Constructure File

1. Create a new PHP file in the controllers directory of your application.
2. Define a new class for the controller and give it a meaningful name.
3. Add functions within the class to handle different functionalities related to the controller's purpose.
4. Customize the functions with your specific logic for handling each functionality.
4. Save the file and make sure it follows the naming convention (ControllerName.php) and is placed in the controllers directory.
5. Once you've created the controller class, you can use it to define routes in your routes.json file and implement the corresponding logic for your application.

```php 
<?php

/**
 * Controller responsible for basic functionalities
 */
class BasicController
{
    /**
     * Example function to display a welcome message
     */
    function welcome()
    {
        echo "Welcome to our application!";
    }

    /**
     * Example function to handle user input
     * @param string $name - The name provided by the user
     */
    function greet($name, ...$aditionalInfo)
    {
        echo "Hello, $name!";
    }
}

?>

```

`$name` and `$age` are explicitly defined arguments.
Any additional parameters passed to the function will be collected in ...$additionalInfo.
If the user passes processUser('John', 30, 'Male', 'Programmer'), then 'Male' and 'Programmer' will be collected in ...$additionalInfo.

```php
/**
 * Example function to demonstrate argument handling
 * @param string $name - The name provided by the user
 * @param int $age - The age provided by the user
 * @param string ...$additionalInfo - Additional information passed as variable arguments
 */
function processUser($name, $age, ...$additionalInfo)
{
    echo "Name: $name, Age: $age\n";
    echo "Additional Info: " . implode(', ', $additionalInfo);
}

```

### Get Data From Data base

Let's take example of the User Table ---------
Assume we need to have a user with certain user name and password
Let's see how can we get that Data

```php
$DB->Users->Get(array(
    "user_name" => array (
        "$username" => "="
    ),
    "password" => array (
        "$password" => "="
    )
));

if ( $check_user ){
    echo "Successfully Loggedin";
    session_start();
    $_SESSION['user_name'] = $username;
    redirect("./");
    }else {
        echo "Not able tologin</br>";
    }
```

Here we written a logic for Login Function 

**Explaination** : There is a function called ***Get*** in Each DAtabase Class we created inside the folder **database** 
we have to pass a array with (
    Attribute_in_table => array(
        attribute_value => "comparison operators",
        // you can add multiple attribute_value which will be concidered as OR operation
    )
)

Let's See Another Example and how that will look in the Query | here we are taking Products Table

```php
$DB->Products->Get(array(
    "products_tag" => array (
        "glass" => "=",
        "bottle" => "="
    ),
    "weight" => array (
        24 => ">"
    )
));
```

The Query of the above code will be **SELECT * FROM Products WHERE ( products_tag = glass OR products_tag = bottle ) AND ( weight > 24 )**

