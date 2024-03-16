<?php

class RegisterController
{
    public function registerUser($username = null, $password = null, $email_id = null, $first_name = null, $last_name = null, $secondary_email = null, ...$args)
    {
        global $DB;

        if ($username == null) {
            loadView(
                'register'
            );
        } else {
            if (
                $DB->Users->Insert(
                    array(
                        "user_name" => $username,
                        "password" => $password,
                        "email_id" => $email_id,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "secondary_email" => $secondary_email
                    )
                )
            ) {
                logconsole("Successfully Registered the User");
                redirect('./');
            }

        }

    }
}