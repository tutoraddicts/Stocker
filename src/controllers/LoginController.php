<?php
class LoginController
{
    function login($username = null, $password = null)
    {
        global $DB;

        if ($username == null || $password == null) { // just the login page where the user will pass username and password 
            $data = array(
                "title" => "login"
            );
            loadView(
                "login",
                $data
            );
        }else {
            var_dump($DB->Products);
            // if ( $DB->Users->check_user($username) ){
            //     echo "Successfully Loggedin";
            // }else {
            //     echo "Not able tologin";
            // }
            // echo "username : {{{ $username and password send : $password";
        }

    }
}