<?php

function json_return($status, $status_msg, $data) {
    $response['status'] = $status;
    $response['status_message'] = $status_msg;
    $response['data'] = $data;
    $json_respones = json_encode($response);
    echo $json_respones;
}
function getHashed($pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    return $hash;
}