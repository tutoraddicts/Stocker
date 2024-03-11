<?php

/**
 * Create a class and add varible with same name of the class 
 * Note - First element of the array will always be the key element to get the data and that have to be unique in all cases
*/
class Users {
    public $Users = array(
        "user_name" => "VARCHAR(50)",
        "first_name" => "VARCHAR(20)",
        "last_name" => "VARCHAR(20)",
        "password" => "VARCHAR(20)",
        "email_id" => "VARCHAR(50)",
        "secondary_email" => "VARCHAR(50)"
    );

    // it will hold the key attribute by wish you can fetch data form database
    public $key = "user_name";

    /**
     * @param string $user_name - name of the user to get
     * @param string $passsword - password of the user
     * 
     * @return bool - returns true if the password and user_name matched with our database
    */
    public function check_user($user_name, $password): bool
    {
        global $DB;

        $user_data = &$DB->get_table_datas("Users");

        if (array_key_exists($user_name, $user_data)){
            return $user_data[$user_name] == $password;
        }

        return false;
    }
}
