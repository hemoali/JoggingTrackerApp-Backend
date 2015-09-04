<?php

    $headers = array();

    foreach($_SERVER as $key => $value) {
        if(strpos($key, 'HTTP_') === 0) {
            $headers = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        }
    }
    foreach ($headers as $value) {
    echo $value;
}
?>