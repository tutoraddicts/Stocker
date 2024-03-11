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
            // var_dump($DB->Users);
            if ( $DB->Users->check_user($username,$password) ){
                echo "Successfully Loggedin";
                session_start();
                $_SESSION['user_name'] = $username;
                redirect("./");
            }else {
                echo "Not able tologin</br>";
            }
            echo "username : $username and password : $password";
        }

    }

    function LogOut() {
        session_destroy();
        redirect('./');
    }
}