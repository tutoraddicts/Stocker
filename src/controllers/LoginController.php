<?php
class LoginController
{
    public function login($username = null, $password = null)
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
            // $check_user = $DB->Users->check_user($username,$password);
            $check_user = $DB->Users->Get(array(
                "user_name" => array (
                    "$username" => "="
                ),
                "password" => array (
                    "$password" => "="
                ),
                ));
                // var_dump($check_user);
            if ( $check_user ){
                echo "Successfully Loggedin";
                session_start();
                $_SESSION['user_name'] = $username;
                redirect("./");
            }else {
                echo "Not able tologin</br>";
            }
            // echo "username : $username and password : $password";
        }

    }

    public function LogOut() {
        session_destroy();
        redirect('./');
    }
}