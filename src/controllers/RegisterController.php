<?php

class RegisterController
{
    public function Register($username = null, $password = null, $email_id = null, $first_name = null, $last_name = null, $secondary_email = null, ...$args)
    {
        $Users = new Users();

        if ($username == null) {
            loadView(
                'register'
            );
        } else {
            if (
                $Users->Insert(
                    [
                        "user_name" => $username,
                        "password" => $password,
                        "email_id" => $email_id,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "secondary_email" => $secondary_email
                    ]
                )
            ) {
                logconsole("Successfully Registered the User");
                redirect('./');
            }

        }

    }
}