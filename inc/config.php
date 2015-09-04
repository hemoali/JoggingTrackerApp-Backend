<?php
session_start();

define('DB_USERNAME', 'root');
define('DB_PASSWORD', '20061996');
define('DB_HOST', 'localhost');
define('DB_NAME', 'joggingtrackerapp');

function pg_connection_string() {
  return "dbname=ddb3o0hq7ptq24 host=ec2-54-197-245-93.compute-1.amazonaws.com port=5432 user=qubbbwkggkouoo password=CnxAIHPqUGyOwbhoFfbkVNWhd1 sslmode=require";
}
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

try {
 $db = @pg_connect(pg_connection_string());
 if ($db){
     echo "!";
 }
} Catch (Exception $e) {
    Echo $e->getMessage();
}