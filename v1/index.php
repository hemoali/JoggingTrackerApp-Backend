<?php

header("Content-Type:application/json");

require_once './utils/Utils.php';
require_once './main.php';

$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
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
        } elseif ($task == "getTimes") {
            //$headers = apache_request_headers();
            if (trim(getAuth()) != "") {
                $auth_array = split(":", getAuth());
                if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                    $main = new Main();
                    $main->getTimes();
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            } else {
                json_return(400, "Bad Request", NULL);
            }
        } elseif ($task == "getTimes_ForAdmin") {
            $user_id = $_POST['user_id'];
            //$headers = apache_request_headers();
            if (strlen($user_id) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->getTimesForAdmin($user_id);
                    } else {
                        json_return(400, "Bad Request", NULL);
                    }
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            }
        } elseif ($task == "delete_time") {
            $time_id = trim($_POST['time_id']);
            if (strlen($time_id) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                //$headers = apache_request_headers();
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->deleteTime($time_id);
                    } else {
                        json_return(400, "Bad Request", NULL);
                    }
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            }
        } elseif ($task == "add_time") {
            $date = trim($_POST['date']);
            $time = trim($_POST['time']);
            $distance = trim($_POST['distance']);
            if (strlen($date) <= 0 || strlen($time) <= 0 || strlen($distance) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                //$headers = apache_request_headers();
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->addTime($date, $time, $distance);
                    } else {
                        json_return(400, "Bad Request1", NULL);
                    }
                } else {
                    json_return(400, "Bad Request2", NULL);
                }
            }
        }elseif ($task == "add_time_admin") {
            $date = trim($_POST['date']);
            $time = trim($_POST['time']);
            $distance = trim($_POST['distance']);
            $user_id = trim($_POST['user_id']);
            if (strlen($date) <= 0 || strlen($time) <= 0 || strlen($distance) <= 0 || strlen($user_id) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                //$headers = apache_request_headers();
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->addTimeAdmin($date, $time, $distance, $user_id);
                    } else {
                        json_return(400, "Bad Request", NULL);
                    }
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            }
        } elseif ($task == "edit_time") {
            $date = trim($_POST['date']);
            $time = trim($_POST['time']);
            $distance = trim($_POST['distance']);
            $time_id = trim($_POST['time_id']);
            if (strlen($date) <= 0 || strlen($time) <= 0 || strlen($distance) <= 0 || strlen($time_id) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                //$headers = apache_request_headers();
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->editTime($date, $time, $distance, $time_id);
                    } else {
                        json_return(400, "Bad Request", NULL);
                    }
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            }
        } elseif ($task == "getUsers") {
            //$headers = apache_request_headers();
            if (trim(getAuth()) != "") {
                $auth_array = split(":", getAuth());
                if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                    $main = new Main();
                    $main->getUsers();
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            } else {
                json_return(400, "Bad Request", NULL);
            }
        }elseif ($task == "getUsers_ForAdmins") {
            //$headers = apache_request_headers();
            if (trim(getAuth()) != "") {
                $auth_array = split(":", getAuth());
                if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                    $main = new Main();
                    $main->getUsersForAdmins();
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            } else {
                json_return(400, "Bad Request", NULL);
            }
        } elseif ($task == "add_user") {
            $email = trim($_POST['email']);
            $pass = trim($_POST['pass']);
            //$headers = apache_request_headers();
            if (trim(getAuth()) != "") {
                $auth_array = split(":", getAuth());
                if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                    $main = new Main();
                    $main->addUser($email, $pass);
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            } else {
                json_return(400, "Bad Request", NULL);
            }
        } elseif ($task == "delete_user") {
            $user_id = trim($_POST['user_id']);
            if (strlen($user_id) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                //$headers = apache_request_headers();
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->deleteUser($user_id);
                    } else {
                        json_return(400, "Bad Request", NULL);
                    }
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            }
        } elseif ($task == "edit_user") {
            $email = trim($_POST['email']);
            $pass = trim($_POST['pass']);
            $user_id = trim($_POST['user_id']);
            $level = trim($_POST['level']);
            if (strlen($email) <= 0 || strlen($user_id) <= 0) {
                json_return(400, "Bad Request", NULL);
            } else {
                //$headers = apache_request_headers();
                if (trim(getAuth()) != "") {
                    $auth_array = split(":", getAuth());
                    if (trim($auth_array[0]) == session_id() && trim($auth_array[1]) == $_SESSION['api_key']) {
                        $main = new Main();
                        $main->editUser($email, $pass, $user_id, $level);
                    } else {
                        json_return(400, "Bad Request", NULL);
                    }
                } else {
                    json_return(400, "Bad Request", NULL);
                }
            }
        } else {
            json_return(400, "Invalid Request", NULL);
        }
        break;

    default:
        json_return(400, "Invalid Request", NULL);
        break;
}
 

