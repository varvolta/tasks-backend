<?php
function respond($data = null, $message = 'Success', $code = 200)
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    http_response_code($code);

    $json = array();
    $json['status'] = $code >= 400 ? 'error' : 'ok';
    $json['message'] = $message;
    $json['code'] = $code;
    $json['data'] = $data;

    echo json_encode($json);
    exit;
}


?>