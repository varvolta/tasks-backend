<?php

require_once 'respond.php';

function connect_db(){
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'tasksapp';
    
    $connection = mysqli_connect($host, $user, $pass, $db);

    if (mysqli_connect_error()) {
        respond(null, 'Could not connect to database', 400);
    }
    return $connection;
}


?>