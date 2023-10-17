<?php
function respond($result = null, $message = 'Success', $code = 200)
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    http_response_code($code);

    $data = array();
    $data['status'] = $code >= 400 ? 'error' : 'ok';
    $data['message'] = $message;
    $data['code'] = $code;
    $data['result'] = $result ? json_encode($result) : null;

    echo json_encode($data);
    exit;
}


?>