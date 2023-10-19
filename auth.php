<?php

require_once 'respond.php';
require_once 'constants.php';
require 'database.php';

$connection = connect_db();

$method = $_SERVER['REQUEST_METHOD'];

function create_session($connection, $user_id)
{
    $token_bytes = random_bytes(16);
    $token = bin2hex($token_bytes);
    $create_date = time();
    $expire_date = $create_date + 60 * 60 * 24; // one day

    $query = "INSERT INTO sessions (user_id, token, expire_date, create_date) VALUES ('{$user_id}', '{$token}', '{$expire_date}', '{$create_date}')";
    mysqli_query($connection, $query);

    return $token;
}

if ($method === 'POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if ($email === null || $password === null) {
        respond(null, Responses::EMAIL_OR_PASSWORD_IS_MISSING, 400);
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (strlen($password) < 6) {
            respond(null, Responses::PASSWORD_LENGTH_MUST_BE_MINIMUM_6, 400);
        }

        $email = mysqli_real_escape_string($connection, $email);
        $password = mysqli_real_escape_string($connection, $password);
        $password = md5($password);

        $query = "SELECT * FROM users WHERE email = '{$email}'";
        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            respond(null, Responses::EMAIL_ALREADY_REGISTERED, 400);
        }

        $query = "INSERT INTO users (email, password) VALUES ('{$email}', '{$password}')";
        $result = mysqli_query($connection, $query);

        $user_id = mysqli_insert_id($connection);
        $token = create_session($connection, $user_id);

        respond(array('token' => $token), Responses::USER_CREATED);
    } else {
        respond(null, Responses::EMAIL_VALIDATION_FAILED, 400);
    }
} else if ($method === 'GET') {
    $email = $_GET['email'] ?? null;
    $password = $_GET['password'] ?? null;

    if ($email && $password) {
        $email = mysqli_real_escape_string($connection, $email);
        $password = mysqli_real_escape_string($connection, $password);
        $password = md5($password);

        $query = "SELECT id FROM users WHERE email = '{$email}' AND password = '{$password}'";
        $result = mysqli_query($connection, $query);
        if (mysqli_num_rows($result) > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $token = create_session($connection, $id);

            respond(array('token' => $token), Responses::SESSION_CREATED);
        } else {
            respond(null, Responses::EMAIL_OR_PASSWORD_IS_WRONG, 400);
        }
    } else {
        respond(null, Responses::EMAIL_OR_PASSWORD_IS_MISSING, 400);
    }
}

?>