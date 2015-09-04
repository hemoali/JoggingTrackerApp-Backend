<?php

session_start();

function pg_connection_string() {
    return "dbname=ddb3o0hq7ptq24 host=ec2-54-197-245-93.compute-1.amazonaws.com port=5432 user=qubbbwkggkouoo password=CnxAIHPqUGyOwbhoFfbkVNWhd1 sslmode=require";
}

function request_headers() {
    if (function_exists("apache_request_headers")) {
        if ($headers = apache_request_headers()) { // And works...
            return $headers; // Use it
        }
    }

    $headers = array();

    foreach (array_keys($_SERVER) as $skey) {
        if (substr($skey, 0, 5) == "HTTP_") {
            $headername = str_replace(" ", "-", ucwords(strtolower(str_replace("_", "", substr($skey, 0, 5)))));
            $headers[$headername] = $_SERVER[$skey];
        }
    }

    return $headers;
}
