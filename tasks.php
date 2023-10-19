<?php

require_once 'respond.php';
require_once 'constants.php';
require 'database.php';

$connection = connect_db();

$method = $_SERVER['REQUEST_METHOD'];

function authenticate($connection)
{
    $token = getallheaders()['token'] ?? null;
    if ($token) {
        $now = time();
        $query = "SELECT user_id FROM sessions WHERE token = '{$token}' AND expire_date >= '{$now}'";
        $result = mysqli_query($connection, $query);
        if (mysqli_num_rows($result) === 0) {
            respond(null, Responses::UNEXISTING_OR_EXPIRED_SESSION, 400);
        }
        $row = $result->fetch_assoc();
        return $row['user_id'];
    } else {
        respond(null, Responses::TOKEN_NOT_SET, 400);
    }
}

if ($method === 'GET') {
    $user_id = authenticate($connection);
    $query = "SELECT * FROM tasks WHERE user_id = {$user_id}";
    $result = mysqli_query($connection, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    respond($rows);
} else if ($method === 'POST') {
    $user_id = authenticate($connection);

    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;
    $priority = $data['priority'] ?? null;
    $due_date = $data['due_date'] ?? null;
    $create_date = $data['create_date'] ?? null;

    if ($title === null || $priority === null || $create_date === null) {
        respond(null, Responses::MISSING_IMPORTANT_FIELDS, 400);
    }

    $query = "INSERT INTO tasks (user_id, title, description, priority, due_date, create_date) VALUES ('{$user_id}', '{$title}', '{$description}', '{$priority}', '{$due_date}', '{$create_date}')";
    mysqli_query($connection, $query);

    $id = mysqli_insert_id($connection);

    respond(array('id' => $id), Responses::TASK_CREATED);
} else if ($method === 'PUT') {
    $user_id = authenticate($connection);

    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    $id = $data['id'] ?? null;
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;
    $priority = $data['priority'] ?? null;
    $due_date = $data['due_date'] ?? null;

    if ($id === null || $title === null || $priority === null) {
        respond(null, Responses::MISSING_IMPORTANT_FIELDS, 400);
    }

    $query = "UPDATE tasks SET title = '{$title}', description = '{$description}', priority = '{$priority}', due_date = '{$due_date}' WHERE id = '{$id}'";
    $result = mysqli_query($connection, $query);

    if (mysqli_affected_rows($connection) > 0) {
        respond(null, Responses::TASK_UPDATED);
    } else {
        respond(null, Responses::COULD_NOT_UPDATE_TASK, 400);
    }
} else if ($method === 'DELETE') {
    $user_id = authenticate($connection);

    $id = $_GET['id'] ?? null;

    if ($id === null) {
        respond(null, Responses::MISSING_IMPORTANT_FIELDS, 400);
    }

    $query = "DELETE FROM tasks WHERE id = '{$id}'";
    $result = mysqli_query($connection, $query);
    if (mysqli_affected_rows($connection) > 0) {
        respond(null, Responses::TASK_DELETED);
    } else {
        respond(null, Responses::COULD_NOT_DELETE_TASK, 400);
    }
}

?>