<?php

header("Content-Type:application/json");

require_once './utils/Utils.php';
require_once './main.php';

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'PUT':
        break;
    case 'POST':
        $task = trim($_POST['task']);
        if ($task == "register") {
            $email = trim($_POST['email']);
            $pass = trim($_POST['pass']);
            $level = trim($_POST['level']);
            if (strlen($email) == 0 || strlen($pass) == 0 || ($level != 0 && $level != 1 && $level != 2)) {
                json_return(400, "Invalid Data", NULL);
            } else {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $main = new Main();
                    $main->register($email, $pass, $level);
                } else {
                    json_return(400, "Invalid Email", NULL);
                }
            }
        } elseif ($task == "login") {
            $email = trim($_POST['email']);
            $pass = trim($_POST['pass']);
            if (strlen($email) == 0 || strlen($pass) == 0) {
                json_return(400, "Invalid Data", NULL);
            } else {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                    $main = new Main();
                    $main->login($email, $pass);
                } else {
                    json_return(400, "Invalid Email", NULL);
                }
            }
        }
        break;
    case 'GET':
        break;
    case 'DELETE':
        break;
    default:
        json_return(400, "Invalid Request", NULL);
        break;
}
 

