<?php

require_once 'respond.php';
require 'database.php';

$connection = connect_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents("php://input");
    $data = json_decode($body, true);
    respond($data);
} else if (($_SERVER['REQUEST_METHOD'] === 'GET')) {

}

?>