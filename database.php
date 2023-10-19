<?php

require_once 'respond.php';
require_once 'constants.php';

function connect_db()
{
    $connection = mysqli_connect(Database::HOST, Database::USER, Database::PASSWORD, Database::DATABASE);

    if (mysqli_connect_error()) {
        respond(null, Constants::COULD_NOT_CONNECT_TO_DATABASE, 400);
    }
    return $connection;
}


?>