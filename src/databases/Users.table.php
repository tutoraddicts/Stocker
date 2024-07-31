<?php

/**
 * Create a class and add varible with same name of the class 
 * Note - First element of the array will always be the key element to get the data and that have to be unique in all cases
 */
class Users extends DB
{
    public $Users = array(
        "user_name" => "VARCHAR(50)", // first eliment is the Key attribute to get the data
        "first_name" => "VARCHAR(20)",
        "last_name" => "VARCHAR(20)",
        "password" => "VARCHAR(20)",
        "email_id" => "VARCHAR(50)",
        "secondary_email" => "VARCHAR(50)"
    );
}
