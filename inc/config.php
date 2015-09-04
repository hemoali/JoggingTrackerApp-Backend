<?php
session_start();

define('DB_USERNAME', 'root');
define('DB_PASSWORD', '20061996');
define('DB_HOST', 'localhost');
define('DB_NAME', 'joggingtrackerapp');

function pg_connection_string() {
  return "dbname=ddb3o0hq7ptq24 host=localhost port=5432 user=qubbbwkggkouoo password=CnxAIHPqUGyOwbhoFfbkVNWhd1 sslmode=require";
}
 $db = pg_connect(pg_connection_string());
if (!$db) {
    echo "!Database connection error.";
    exit;
}else{
    echo "DONE";
}
