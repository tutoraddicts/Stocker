<?php
include_once("index.php");
/* 
Record Perpose 

Way to write a function - you can give the function anyname you want.
@functionArgumentsRules - if you are passing any query as GET or POST you can specify those variables as a arguments
there are some flexibility are there for exampele you pass a GET query like this "?user=u1&password=pass"

but you only specify function index($user, ...$args) user in the argument then the user value will be passed on $user variable and other values will be there in the $args
if you want to have specific variable here $password you can just specify $password in argument of function index($user, $password, ...$args)

NOTE: 
    1. make sure you keep ...$args  at the end of each function argument which you are specified in routes.json file
    2. make sure your variable name aligned with the query key for example if you specify "?user=u1&password=pass" and use $User in argument it will not work yu have to specify $user

How to access the data in html file ?? just use $variable name
*/

class HomeController
{
    function index(...$args)
    {
        global $Controllers;
        // if user not logged in then redirect to different route
        if(!isUserLoggedin()){
            redirect("login");
            // $Controllers->LoginController->login(); // just for example
            // exit();
            return;
        }
        // var_dump($_POST);
        $data = array(
            "stocker" => "BillingApp"
        );
        // include "Static/View/index.php";
        loadView(
            "index",
            $data,
            $args
        );
    }
}