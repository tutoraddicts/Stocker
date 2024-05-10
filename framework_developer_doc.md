# Application Documentation For Framework Developers Only

## Table of Contents
- [Application Documentation For Framework Developers Only](#application-documentation-for-framework-developers-only)
  - [Table of Contents](#table-of-contents)
  - [Folder Structure](#folder-structure)
  - [Project Structure Documentation](#project-structure-documentation)
    - [1. `src/` Directory](#1-src-directory)
      - [1.1 `src/.tempviews/`](#11-srctempviews)
      - [1.2 `src/controllers/`](#12-srccontrollers)
      - [1.3 `src/databases/`](#13-srcdatabases)
      - [1.4 `src/static/`](#14-srcstatic)
        - [1.4.1 `src/static/styles/`](#141-srcstaticstyles)
        - [1.4.2 `src/static/view/`](#142-srcstaticview)
      - [1.5 `src/util/`](#15-srcutil)
    - [2. `.htaccess`](#2-htaccess)
  - [Detailed Summery of Files and their work](#detailed-summery-of-files-and-their-work)
    - [Controllers.php](#controllersphp)
    - [db\_tables.php](#db_tablesphp)
    - [DB.php](#dbphp)
    - [file\_handeler.php](#file_handelerphp)
    - [handleServerRequests.php](#handleserverrequestsphp)
    - [Util.php](#utilphp)
    - [Sources of Data:](#sources-of-data)
  - [App Setup Instructions](#app-setup-instructions)
  - [Configuring the Application](#configuring-the-application)
  - [Understanding `app_setup.php`](#understanding-app_setupphp)
    - [`connect_to_db()`](#connect_to_db)
    - [`alter_db_tables(&$db_connection, &$table_name, &$table_columns)`](#alter_db_tablesdb_connection-table_name-table_columns)
    - [`setup_db(&$db_connection = null, $db_name)`](#setup_dbdb_connection--null-db_name)
    - [`create_db_table(&$db_connection, &$table_name, &$table_columns)`](#create_db_tabledb_connection-table_name-table_columns)
    - [`get_db_tables()`](#get_db_tables)
    - [`init_db()`](#init_db)
    - [`update_tables()`](#update_tables)
  - [Frequently Asked Questions (F\&Q)](#frequently-asked-questions-fq)
    - [1. What is the purpose of the entire application?](#1-what-is-the-purpose-of-the-entire-application)
    - [2. How is the application structured?](#2-how-is-the-application-structured)
    - [3. Where are the main configuration settings stored?](#3-where-are-the-main-configuration-settings-stored)
    - [4. How does the application handle routing?](#4-how-does-the-application-handle-routing)

## Folder Structure

Here's the directory structure of the framework:


## Project Structure Documentation

This document outlines the structure of the project, including the directories, files, and their purposes.

### 1. `src/` Directory

This directory contains all the source code and configuration files for the project. Here I am mentioning the basic Functionality of the Files To know more you can check files or the this document.

- `app_setup.php`: This file is responsible for initializing the application.
- `config.json`: Configuration settings for the application.
- `index.php`: Entry point of the application.
- `readme.md`: Readme file containing information about the project.
- `routes.json`: Defines the routes for the application.

#### 1.1 `src/.tempviews/`

Temporary view files used during development.

- `index.php`: Temporary view for the index page.
- `login.php`: Temporary view for the login page.
- `register.php`: Temporary view for the register page.

#### 1.2 `src/controllers/`

Contains controller classes responsible for handling various requests.

- `HomeController.php`: Controller for handling home-related requests.
- `LoginController.php`: Controller for handling login-related requests.
- `RegisterController.php`: Controller for handling registration-related requests.

#### 1.3 `src/databases/`

Includes classes related to database interaction.

- `Products.php`: Class for handling product-related database operations.
- `Users.php`: Class for handling user-related database operations.

#### 1.4 `src/static/`

Contains static assets like CSS stylesheets and HTML templates.

##### 1.4.1 `src/static/styles/`

CSS stylesheets for the application.

- `login.css`: Styles for the login page.
- `main.css`: Main stylesheet for the application.

##### 1.4.2 `src/static/view/`

HTML templates for different views of the application.

- `index.html`: HTML template for the index page.
- `login.html`: HTML template for the login page.
- `register.html`: HTML template for the register page.

#### 1.5 `src/util/`

Utility files used across the application.

- `Controllers.php`: Utility functions related to controllers.
- `DB.php`: Database connection and interaction utility.
- `db_tables.php`: Defines the structure of database tables.
- `demo.php.backup`: Backup file for demo purposes.
- `file_handler.php`: Utility for handling files.
- `request_handler.php`: Utility for handling HTTP requests.
- `util.php`: General-purpose utility functions.

### 2. `.htaccess`

The `.htaccess` file contains Apache server configuration directives. It's used for URL rewriting and other server-related configurations.




## Detailed Summery of Files and their work
### Controllers.php

The `Controllers.php` file contains a class named `Controllers`, which manages controller objects in the application. Here's a summary of its functionality:

- The class maintains a private array called `$Controllers` to store instances of controller objects.
- It includes a constructor `__construct()` and a destructor `__destruct()` which are currently commented out. These typically handle initialization and cleanup tasks.
- Implements magic methods `__set()` and `__get()` to dynamically set and get controller objects respectively:
  - In `__set()`, a new controller object is instantiated and stored in the `$Controllers` array when assigned to a dynamic property.
  - In `__get()`, it retrieves the controller object from the `$Controllers` array based on the dynamic property name.
- Additionally, it provides a method `RegisterController()` to manually register controller objects with the `Controllers` class.

This class serves as a registry for controller objects, facilitating easy access throughout the application. It offers dynamic property access to controller objects, simplifying their usage in various parts of the codebase.

### db_tables.php

The `db_tables.php` file contains an abstract class named `DBTables` designed to hold database data and perform basic operations. Here's a summary of its functionality:

- The class implements the `ArrayAccess` interface, allowing it to behave like an array.
- It includes magic methods `__set()` and `__get()` to set and get values for database tables respectively.
- Provides methods for inserting data into database tables:
  - `Insert(array $table_content)`: Inserts data into the corresponding table using the provided table content array.
- Additionally, it includes a method `Get_All_Data()` to retrieve all data stored in the table.

This abstract class serves as a foundation for creating specific database table classes. It provides functionality for interacting with database tables in a structured manner, such as setting and getting values, as well as inserting data.

### DB.php

The `DB.php` file contains a class called `DB` which handles database connections and operations. Here's a summary of its functionality:

- The class includes properties for holding the database connection, table attributes, and table data.
- It has a constructor `__construct()` which establishes a connection to the database.
- Implements magic methods `__set()` and `__get()` to set and get table objects respectively.
- Provides methods for updating database tables (`Update_DB()`), fetching table data (`get_table_datas()`), and running SQL queries (`RunQuery()`).
- It includes a private method `connect_to_DB()` to establish a connection to the database server.
- Additionally, it includes a method `check_db_connection()` to check the status of the database connection.

This class acts as a wrapper for database operations, providing methods to interact with database tables, execute queries, and manage database connections.

### file_handeler.php

The `file_handeler.php` file contains functions for processing HTML files by converting certain patterns to PHP tags, as well as other file-related tasks. Here's a summary of its functionality:

- Defines an array `$patterns` which contains regular expression patterns and their replacements for converting specific patterns in HTML files to PHP tags.
- Includes a function `processHTMLFile($filePath)` to process an HTML file by applying the defined patterns and converting them to PHP syntax.
- Provides a function `convertToPHP($pattern, $replacement, &$input)` to perform the actual conversion of HTML content to PHP syntax based on the given pattern and replacement.
- Defines a function `createFolder($directory)` to create a folder/directory if it doesn't already exist. It recursively creates each folder in the specified directory path.

This file serves as a utility for processing HTML files and performing file-related tasks such as creating directories.

### handleServerRequests.php

The `handleServerRequests.php` file contains a function named `handleServerRequests()` responsible for routing incoming server requests to the appropriate controller methods. Here's a summary of its functionality:

- Parses the incoming request URL and queries.
- Matches the requested URL with predefined routes and calls the associated controller methods.
- If the route is found:
  - Splits the controller and method name.
  - Checks if the controller class exists and creates an instance of it.
  - Calls the method with the query parameters if it exists in the controller.
  - Returns a 404 error if the method or controller class does not exist.
- If the route is not found, returns a 404 error with suggestions for resolution.

This function acts as the central mechanism for handling incoming server requests, routing them to the appropriate controller methods based on predefined routes.

### Util.php

The `index.php` file serves as the entry point of the application. It includes necessary files, initializes session, and provides functions for handling server requests, loading views, creating controllers, and managing databases. Here's a summary of its functionality along with the sources of data it uses:

- Includes all controller files and database files.
- Defines global variables such as `$routes`, `$requestUrl`, and `$config`.
- Provides functions for logging messages to the browser console, redirecting URLs, and getting relative URLs.
- Initializes session and creates instances of the `Controllers` and `DB` classes.
- Defines functions for creating controllers and databases, loading views, and handling server requests.
- Loads views based on provided parameters and handles server requests by routing them to the appropriate controller methods.

### Sources of Data:
- **$routes**: Loaded from `routes.json` file. Contains predefined routes for routing server requests.
- **$requestUrl**: Retrieved from `$_SERVER['REQUEST_URI']`. Contains the requested URL.
- **$config**: Loaded from `config.json` file. Contains configuration settings for the application.
- **Controllers**: Stored in session variable `$_SESSION['Controllers']`. Contains instances of controller classes.
- **DB**: Stored in session variable `$_SESSION['DB']`. Contains an instance of the `DB` class.

This file acts as the main controller of the application, orchestrating the loading of resources, handling server requests, and managing session data.


## App Setup Instructions

To set up and configure the application, follow these steps:

1. **Database Initialization:**
   - Run the command `-init_db` to initialize the database and create required tables.
   - This command creates the necessary database if it doesn't exist and sets up the required tables.
    ```bash 
    php app_setup.php -init_db
    ```

1. **Update Tables:**
   - Run the command `-update_tables` to update all table attributes.
   - This command alters existing tables if new columns are added or removes columns if they are no longer needed.
   ```bash
   php app_setup.php -update_tables
   ```

2. **Get All Database Data (Testing Purpose Only):**
   - Run the command `-get_all_db_data` to retrieve data from all tables in the database. This command is for testing purposes only.
   ```bash
   php app_setup.php -get_all_db_data
   ```

3. **Help:**
   - Run the command `-help` to display available command-line arguments and their descriptions.
   ```
   php app_setup.php -help
   ```

4. **Additional Notes:**
   - Ensure that the `config.json` file is properly configured with database credentials and other necessary settings before running the setup commands.
   - Review the output messages for any errors or notifications during the setup process.

Follow these instructions to configure and initialize the application database and tables according to your requirements.

## Configuring the Application

To set up and configure the application, follow these steps:

1. Open the `config.json` file in your text editor.
2. Set the value of the `"build"` key to `false` if you want to process HTML files to PHP on-the-fly. If you prefer to include PHP files directly, set it to `true`.
3. Configure your database settings under the `"database"` key:
    - `"host"`: Specify the hostname of your database server.
    - `"db"`: Provide the name of the database you want to use for the application.
    - `"userName"`: Enter the username for accessing the database.
    - `"password"`: Set the password for the specified database user.
4. Adjust the `"holdSession"` key:
    - Set it to `true` if you want to maintain sessions across multiple requests.
    - Set it to `false` if you prefer not to hold sessions.

Save the `config.json` file after making the necessary changes. Ensure that the configuration matches your environment and database setup.


## Understanding `app_setup.php`

The `app_setup.php` script is crucial for initializing and configuring the application's database and handling command line arguments. Let's delve into the functionality of each function and how the logic operates:

### `connect_to_db()`

This function establishes a connection to the MySQL database using the credentials specified in the `config.json` file. It returns a `mysqli` object representing the connection.

### `alter_db_tables(&$db_connection, &$table_name, &$table_columns)`

This function is responsible for altering existing database tables to match the structure defined in the PHP files located in the `databases/` directory. It compares the columns present in the database with those defined in the PHP files and adds or removes columns as necessary.

### `setup_db(&$db_connection = null, $db_name)`

The `setup_db()` function creates the specified database if it does not already exist. It utilizes the connection object passed as a parameter or creates a new one if not provided. Once the database is created, it selects it for subsequent operations.

### `create_db_table(&$db_connection, &$table_name, &$table_columns)`

This function creates a database table with the specified name and columns. It generates a SQL query to create the table based on the attributes defined in the PHP file corresponding to the table.

### `get_db_tables()`

The `get_db_tables()` function retrieves all database tables defined in the PHP files within the `databases/` directory. It instantiates objects for each table and stores them in an associative array with the table name as the key.

### `init_db()`

This function initializes the database by creating it if it does not exist and then creating all defined tables within it. It ensures that the database environment is properly set up for the application to function.

### `update_tables()`

The `update_tables()` function is responsible for updating existin


## Frequently Asked Questions (F&Q)
### 1. What is the purpose of the entire application?
`Answer`: The application, named "Stocker," is designed to serve a specific purpose, which could include managing inventory, tracking sales, or providing analytics for a business.

### 2. How is the application structured?
`Answer`: The application likely follows the MVC (Model-View-Controller) architecture, where logic, data, and presentation are separated into different components. Controllers handle requests and generate responses, models interact with the database, and views display the user interface.

### 3. Where are the main configuration settings stored?
`Answer`: Configuration settings, such as database credentials and application flags, are typically stored in a `config.json` file at the root of the project directory.

### 4. How does the application handle routing?
`Answer`: Routing is handled by a central router or dispatcher, which maps incoming URLs to controller methods. This routing logic is often defined in a `routes.json` file or directly within the router code.
Example
```json
{
    "routes": {
        "/": "HomeController@index",
        "/about": "HomeController@index",
        "/contact": "HomeController@index",
        "/login": "LoginController@login",
        "/login/failed": "LoginController@failedlogin",
        "/register": "RegisterController@registerUser",
        "/logout": "LoginController@logout"
    }
}

```

5. What is the purpose of the file_handeler.php file?
`Answer`: The file_handeler.php file likely contains functions for processing HTML files and converting certain patterns into PHP code. This functionality might be used for template processing or dynamic content generation.

6. How are controllers and database interactions managed?
`Answer`: Controllers are typically defined in individual PHP files within a controllers/ directory. They handle incoming requests, perform necessary actions, and interact with models to fetch or manipulate data. Database interactions are managed through dedicated classes or functions defined in databases/ files.

7. How does the application handle command-line arguments?
`Answer`: Command-line arguments are processed by scripts such as app_setup.php. These scripts parse arguments, perform tasks such as initializing the database or updating table structures, and provide helpful messages for usage.

8. What should I do if I encounter errors or need assistance?
`Answer`: If you encounter errors or require assistance, you can refer to the documentation, reach out to team members for help, or consult relevant sections of the codebase for debugging. Additionally, logging and error handling mechanisms may provide insights into issues encountered during runtime.