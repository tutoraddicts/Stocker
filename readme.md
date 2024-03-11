# Application Documentation

## Table of Contents
- (Application Documentation)[#application-documentation]

## Folder Tree

├───Bin
│   ├───.tempviews
│   ├───Controllers
│   └───Static
│       ├───Styles
│       └───View
└───src
    ├───.tempviews
    ├───Controllers
    ├───Static
    │   ├───Styles
    │   └───View
    └───Tables

## Folder Work

### Bin

This bin folder is to hold all build files you will get all the files after build inside this directory
you should use the build files for your production enviouemt

### Controller

Inside Controller you will get multiple Controller files which will controll the actions when the url is hit

### routes.json

```json
{
    "routes": {
        "/": "HomeController@index",
        "/about": "HomeController@index",
        "/contact": "HomeController@index"
    }
}

```

This file will contain all the routes of the application. Example is metioned above

### config.json

```json
{
    "-build": false,
    "devEnviourment": {
        "Root": "./",
        "BinRoot": "../Bin",
        "staticFolder": "Static",
        "viewFolder": "Static/View",
        "stylesFolder": "Static/Styles",
        "controllersFolder": "Controllers",
        "routeFile": "routes.json",
        "indexFile": "index.php",
        "configFile": "config.json"
    },

    "database": {
        "host" : "localhost",
        "db_name" : "stocker",
        "userName": "admin",
        "password": "admin"
    }
}

```

This **config.json** file will contain all the config of the application for example is it ready to build or not or what is the config for development folder and it's structure
will contain details of the database etc.

### Database

This Folder will contain all the configarations for the database which will be in json fortmat

### Static 

Contain all the static files like html css js and etc

### Setup Database

1. Create Database and Create Tables 
```powetshell
php .\app_setup.php -init_db
```
2. Update the Table Structure according to the update 
```powetshell
php .\app_setup.php -update_tables
```