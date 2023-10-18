<?php

require_once 'respond.php';
require_once 'constants.php';
require 'database.php';

$connection = connect_db();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $body = file_get_contents("php://input");
    $data = json_decode($body, true);

    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if ($email && $password) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (strlen($password) >= 6) {
                $email = mysqli_real_escape_string($connection, $email);
                $password = mysqli_real_escape_string($connection, $password);
                $password = md5($password);

                $query = "SELECT * FROM users WHERE email = '{$email}'";
                $result = mysqli_query($connection, $query);

                if ($result->num_rows === 0) {
                    $query = "INSERT INTO users (email, password) VALUES ('{$email}', '{$password}')";
                    $result = mysqli_query($connection, $query);

                    if ($result) {
                        $user_id = mysqli_insert_id($connection);
                        $token_bytes = random_bytes(16);
                        $token = bin2hex($token_bytes);
                        $create_date = time();
                        $expire_date = $create_date + 60 * 60 * 24; // one day

                        $query = "INSERT INTO sessions (user_id, token, expire_date, create_date) VALUES ('{$user_id}', '{$token}', '{$expire_date}', '{$create_date}')";
                        $result = mysqli_query($connection, $query);

                        if ($result) {
                            respond(null, Responses::USER_CREATED);
                        } else {
                            respond(null, Responses::COULD_NOT_CREATE_SESSION, 400);
                        }
                    } else {
                        respond(null, Responses::COULD_NOT_CREATE_USER, 400);
                    }
                } else {
                    respond(null, Responses::EMAIL_ALREADY_REGISTERED, 400);
                }
            } else {
                respond(null, Responses::PASSWORD_LENGTH_MUST_BE_MINIMUM_6, 400);
            }
        } else {
            respond(null, Responses::EMAIL_VALIDATION_FAILED, 400);
        }
    } else {
        respond(null, Responses::EMAIL_OR_PASSWORD_IS_MISSING, 400);
    }
} else if ($method === 'GET') {

}

?>