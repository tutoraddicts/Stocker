<?php
class LoginController
{
    public function Login($username = null, $password = null)
    {      
        if ($username == null || $password == null) { // just the login page where the user will pass username and password 
            $data = [
                "title" => "login",
                "massage" => "Please submit a valid username and password"
            ];
            loadView(
                "login",
                $data
            );
        }else {
            $user = new Users();
            $check_user = $user->Get(array(
                "user_name" => [
                    $username => "="
                ],
                "password" => [
                    $password => "="
                ],
                ));
                // var_dump($check_user);
            if ( $check_user ){
                logconsole("Successfully Loggedin");
                if ( session_start() ) {
                    $_SESSION['user_name'] = $username;
                    redirect("./");
                }
                
            }else {
                echo "Not able Find the User with such name</br>";
            }
            // echo "username : $username and password : $password";
        }

    }

    public function logout() {
        session_destroy();
        redirect('./');
    }
}