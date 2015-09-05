<?php
session_start();

define('DB_USERNAME', 'root');
define('DB_PASSWORD', '20061996');
define('DB_HOST', 'localhost');
define('DB_NAME', 'joggingtrackerapp');

function pg_connection_string() {
    return "dbname=ddb3o0hq7ptq24 host=ec2-54-197-245-93.compute-1.amazonaws.com port=5432 user=qubbbwkggkouoo password=CnxAIHPqUGyOwbhoFfbkVNWhd1 sslmode=require";
}

function getAuth() {
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headers = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            if ($headers == Authorization)
                return $value;
        }
    }
}
