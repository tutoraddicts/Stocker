<?php

/**
 * Create a class and add varible with same name of the class 
 * Note - First element of the array will always be the key element to get the data and that have to be unique in all cases
 */
class Users extends DBTables
{
    public $Users = array(
        "user_name" => "VARCHAR(50)", // first eliment is the Key attribute to get the data
        "first_name" => "VARCHAR(20)",
        "last_name" => "VARCHAR(20)",
        "password" => "VARCHAR(20)",
        "email_id" => "VARCHAR(50)",
        "secondary_email" => "VARCHAR(50)"
    );

    /**
     * @param string $user_name - name of the user to get
     * @param string $passsword - password of the user
     * 
     * @return bool - returns true if the password and user_name matched with our database
     */
    public function check_user($user_name, $password): bool
    {
        global $DB;

        $user_data = &$this->{$user_name};

        // var_dump($user_data);
        if ($user_data) {
            return $user_data["password"] == $password;
        }

        return false;
    }
}
