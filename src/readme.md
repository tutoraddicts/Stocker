# Application Documentation

## Table of Contents
- [Application Documentation](#application-documentation)
  - [Table of Contents](#table-of-contents)
  - [Folder Tree](#folder-tree)
  - [Routing](#routing)
    - [How Routing Works](#how-routing-works)
  - [Controller](#controller)
    - [Default Controller](#default-controller)
    - [How to create a controller](#how-to-create-a-controller)
    - [How to Delete a Controller](#how-to-delete-a-controller)
  - [Setup Database](#setup-database)
    - [1. Create Database and Create Tables](#1-create-database-and-create-tables)
    - [2. Update the Table Structure according to the update](#2-update-the-table-structure-according-to-the-update)
  - [Syntax of the View](#syntax-of-the-view)
  - [How To](#how-to)
    - [How to create a controller](#how-to-create-a-controller-1)
    - [How to Delete a Controller](#how-to-delete-a-controller-1)
    - [Create a Table File and reocrd in Databse](#create-a-table-file-and-reocrd-in-databse)
    - [Remove Table File and reocrd in Databse](#remove-table-file-and-reocrd-in-databse)
    - [Alter a Table in Databse](#alter-a-table-in-databse)
    - [Initialise Databse and Appication](#initialise-databse-and-appication)
    - [Build the Apllication](#build-the-apllication)
    - [Get Data From Data base](#get-data-from-data-base)

## Folder Tree

```plaintext
src
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
```

## Routing

### How Routing Works

if you hit a URL basically it will look for a controller in the controllers folder if it did not find the controller it throw an exception into it.
**Example** : 
    if you hit hostname/home/product
    then home is the controller and product is the function you have written inside the controller.

## Controller

### Default Controller
Default Cntroller Name is **Controller** you can find that under Controller Folder and it will be called when we hit the **/** Root URL. you can change the default configaration by specifying the controller name in config.json in the attribute **defaultController** you just have to pass the name of the controller example for home controller you have to mention home

### How to create a controller

Replace TableName with the actual name of your table, and define the columns and their data types in the $TableName property. Additionally, you can add any custom methods or functions specific to this table within the class.

This template provides a basic structure for defining a table class in your application. Make sure to customize it according to your actual table schema and requirements.

```bash
php .\app_setup.php -new_controller <controller_name>
```

### How to Delete a Controller
```bash
php .\app_setup.php -remove_controller <controller_name>
```

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

### How to create a controller
```bash
php .\app_setup.php -new_controller <controller_name>
```

### How to Delete a Controller
```bash
php .\app_setup.php -remove_controller <controller_name>
```

### Create a Table File and reocrd in Databse

create a new table record in code under databases and create the table

```bash
php .\app_setup.php -create_db_table <table_name>
```

### Remove Table File and reocrd in Databse

remove a table record in code under databases and drop the table in databse

```bash
php .\app_setup.php -remove_db_table <table_name>
```

### Alter a Table in Databse

```bash
php .\app_setup.php -init_db
```

### Initialise Databse and Appication

```bash
php .\app_setup.php -init_db
```

### Build the Apllication

buid application you can find in the build directory outside of the src directory
```bash
php .\app_setup.php -build
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

What if you want o add AND operation Between that **weight** Attribute then you have to do this
```php
$DB->Products->Get(array(
    "products_tag" => array (
        "glass" => "=",
        "bottle" => "="
    ),
    "weight" => array (
        24 => ">",
        100 => array(
            "AND" => "<" 
        ),
        75 => "!="
        // weight > 24 AND weight < 100> weight != 75
    )
));
```
The Query of the above code will be **SELECT * FROM Products WHERE ( products_tag = glass OR products_tag = bottle ) AND ( weight > 24 AND weight < 100 OR weight != 75 )**


