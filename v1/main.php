<?php

session_start();

require_once '../inc/config.php';
require_once './utils/Utils.php';

class Main {

    public $conn;

    function __construct() {
        $this->conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_errno()) {
            json_return("200", "Database Error", NULL);
        }
    }

    function register($email, $pass, $level) {
        $email = mysqli_real_escape_string($this->conn, $email);
        $pass = mysqli_real_escape_string($this->conn, $pass);
        $level = mysqli_real_escape_string($this->conn, $level);
        $sql = "SELECT * FROM `users` WHERE `email` = '$email' LIMIT 1";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));
        if (mysqli_num_rows($query) <= 0) {
            $hash = getHashed($pass);
            $api_key = getAPIKey();
            $sql = "INSERT INTO `users` (`email`, `pass`, `level`, `api_key`) VALUES ('$email', '$hash', '$level', '$api_key')";
            $query = mysqli_query($this->conn, $sql);
            if ($query) {
                $_SESSION['user_id'] = mysqli_insert_id($this->conn);
                $_SESSION['level'] = $level;
                $_SESSION['email'] = $email;
                $_SESSION['api_key'] = $api_key;
                json_return(200, "Signup Succeeded", array("session_id" => session_id(), "api_key" => $api_key));
            } else {
                json_return(400, "Something Went Wrong", NULL);
            }
        } else {
            json_return(200, "User Already Exists", NULL);
        }
    }

    function login($email, $pass) {
        $email = mysqli_real_escape_string($this->conn, $email);
        $pass = mysqli_real_escape_string($this->conn, $pass);
        $sql = "SELECT * FROM `users` WHERE `email` = '$email' LIMIT 1";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            if (password_verify($pass, $row['pass'])) {
                $_SESSION['user_id'] = $row['_id'];
                $_SESSION['level'] = $row['level'];
                $_SESSION['email'] = $email;
                $_SESSION['api_key'] = $row['api_key'];
                json_return(200, "Login Succeeded", array("session_id" => session_id(), "level" => $row['level'], "api_key" => $row['api_key']));
            } else {
                json_return(200, "Invalid Password", NULL);
            }
        } else {
            json_return(400, "Invalid Sign in Data", NULL);
        }
    }

    function getTimes() {
        $user_id = $_SESSION['user_id'];
        $results = array();
        $sql = "SELECT * FROM `times` WHERE `user_id` = '$user_id' ORDER BY `_id` DESC";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));

        while ($row = mysqli_fetch_array($query)) {
            $results[] = array(
                'id' => $row['_id'],
                'user_id' => $row['user_id'],
                'date' => $row['date'],
                'time' => $row['time'],
                'distance' => $row['distance']
            );
        }
        json_return(200, "Times Read Succeeded", $results);
    }

    public function deleteTime($time_id) {
        $time_id = mysqli_real_escape_string($this->conn, $time_id);
        $sql = "DELETE FROM `times` WHERE `_id` = '$time_id'";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));
        if ($query) {
            json_return(200, "Time Delete Succeeded", NULL);
        } else {
            json_return(400, "Connot Find This Record", NULL);
        }
    }

    public function addTime($date, $time, $distance) {
        $date = mysqli_real_escape_string($this->conn, $date);
        $time = mysqli_real_escape_string($this->conn, $time);
        $distance = mysqli_real_escape_string($this->conn, $distance);
        $user_id = mysqli_real_escape_string($this->conn, $_SESSION['user_id']);
        $sql = "INSERT INTO `times` (`user_id`, `date`, `time`, `distance`) VALUES ('$user_id', '$date', '$time', '$distance')";
        $query = mysqli_query($this->conn, $sql);
        if ($query) {
            json_return(200, "Record Add Succeeded", array("user_id" => $user_id, "_id" => mysqli_insert_id($this->conn)));
        } else {
            json_return(400, "Something Went Wrong", NULL);
        }
    }

    public function editTime($date, $time, $distance, $time_id) {
        $time_id = mysqli_real_escape_string($this->conn, $time_id);
        $date = mysqli_real_escape_string($this->conn, $date);
        $time = mysqli_real_escape_string($this->conn, $time);
        $distance = mysqli_real_escape_string($this->conn, $distance);
        $user_id = mysqli_real_escape_string($this->conn, $_SESSION['user_id']);

        $sql = "UPDATE `times` SET `time` = '$time', `date` = '$date', `distance` = '$distance' WHERE `_id` = '$time_id'";
        $query = mysqli_query($this->conn, $sql);
        if ($query) {
            json_return(200, "Record Update Succeeded", NULL);
        } else {
            json_return(400, "Something Went Wrong", NULL);
        }
    }

    public function getUsers() {
        $user_id = $_SESSION['user_id'];

        $results = array();
        $sql = "SELECT * FROM `users` WHERE `_id` != '$user_id' AND `level` = '2' ORDER BY `_id` DESC";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));
        while ($row = mysqli_fetch_array($query)) {
            $results[] = array(
                'id' => $row['_id'],
                'email' => $row['email']
            );
        }
        json_return(200, "Users Read Succeeded", $results);
    }

    public function addUser($email, $pass) {
        $email = mysqli_real_escape_string($this->conn, $email);
        $pass = mysqli_real_escape_string($this->conn, $pass);

        $sql = "SELECT * FROM `users` WHERE `email` = '$email' LIMIT 1";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));
        if (mysqli_num_rows($query) <= 0) {
            $hash = getHashed($pass);
            $api_key = getAPIKey();
            $sql = "INSERT INTO `users` (`email`, `pass`, `level`, `api_key`) VALUES ('$email', '$hash', '2', '$api_key')";
            $query = mysqli_query($this->conn, $sql);
            if ($query) {
                json_return(200, "User Add Succeeded", array("_id" => mysqli_insert_id($this->conn)));
            } else {
                json_return(400, "Something Went Wrong", NULL);
            }
        } else {
            json_return(200, "User Already Exists", NULL);
        }
    }

    public function deleteUser($user_id) {
        $user_id = mysqli_real_escape_string($this->conn, $user_id);
        $sql = "DELETE FROM `users` WHERE `_id` = '$user_id'";
        $query = mysqli_query($this->conn, $sql) or die(mysqli_errno($this->conn));
        if ($query) {
            json_return(200, "User Delete Succeeded", NULL);
        } else {
            json_return(400, "Connot Find This User", NULL);
        }
    }

}
