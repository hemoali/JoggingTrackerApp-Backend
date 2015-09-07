<?php

session_start();

require_once '../inc/config.php';
require_once './utils/Utils.php';

class Main {

    public $conn;

    function __construct() {
        if (!$this->conn = @pg_connect(pg_connection_string())) {
            json_return("200", "Database Error", NULL);
        }
    }

    function register($email, $pass, $level) {
        $email = pg_escape_string($this->conn, strtolower($email));
        $pass = pg_escape_string($this->conn, $pass);
        $level = pg_escape_string($this->conn, $level);
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        if (pg_num_rows($query) <= 0) {
            $hash = getHashed($pass);
            $api_key = getAPIKey();
            $sql = "INSERT INTO users (email, pass, level, api_key) VALUES ('$email', '$hash', '$level', '$api_key') RETURNING _id, reg_date;";
            $query = pg_query($this->conn, $sql);
            if ($query) {
                $insert_row = pg_fetch_row($query);
                $insert_id = $insert_row[0];
                $insert_date = $insert_row[5];
                $_SESSION['user_id'] = $insert_id;
                $_SESSION['level'] = $level;
                $_SESSION['email'] = $email;
                $_SESSION['api_key'] = $api_key;
                json_return(200, "Signup Succeeded", array("session_id" => session_id(), "api_key" => $api_key, "reg_date" => $insert_date));
            } else {
                json_return(400, "Something Went Wrong", NULL);
            }
        } else {
            json_return(200, "User Already Exists", NULL);
        }
    }

    function login($email, $pass) {
        $email = pg_escape_string($this->conn, strtolower($email));
        $pass = pg_escape_string($this->conn, $pass);
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        if (pg_num_rows($query) > 0) {
            $row = pg_fetch_assoc($query);
            if (password_verify($pass, $row['pass'])) {
                $_SESSION['user_id'] = $row['_id'];
                $_SESSION['level'] = $row['level'];
                $_SESSION['email'] = $email;
                $_SESSION['api_key'] = $row['api_key'];
                $reg_date = $row['reg_date'];
                json_return(200, "Login Succeeded", array("session_id" => session_id(), "level" => $row['level'], "api_key" => $row['api_key'], "reg_date" => $reg_date));
            } else {
                json_return(400, "Invalid Password", NULL);
            }
        } else {
            json_return(400, "Invalid Sign in Data", NULL);
        }
    }

    function getTimes() {
        $user_id = $_SESSION['user_id'];
        $results = array();
        $sql = "SELECT * FROM times WHERE user_id = '$user_id' ORDER BY _id DESC";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));

        while ($row = pg_fetch_array($query)) {
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

    function getTimesForAdmin($user_id) {
        $results = array();
        $sql = "SELECT * FROM times WHERE user_id = '$user_id' ORDER BY _id DESC";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));

        while ($row = pg_fetch_array($query)) {
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
        $time_id = pg_escape_string($this->conn, $time_id);
        $sql = "DELETE FROM times WHERE _id = '$time_id'";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        if ($query) {
            json_return(200, "Time Delete Succeeded", NULL);
        } else {
            json_return(400, "Connot Find This Record", NULL);
        }
    }

    public function addTime($date, $time, $distance) {
        $date = pg_escape_string($this->conn, $date);
        $time = pg_escape_string($this->conn, $time);
        $distance = pg_escape_string($this->conn, $distance);
        $user_id = pg_escape_string($this->conn, $_SESSION['user_id']);
        $sql = "INSERT INTO times (user_id, date, time, distance) VALUES ('$user_id', '$date', '$time', '$distance') RETURNING _id;";
        $query = pg_query($this->conn, $sql);

        if ($query) {
            $insert_row = pg_fetch_row($query);
            $insert_id = $insert_row[0];
            json_return(200, "Record Add Succeeded", array("user_id" => $user_id, "_id" => $insert_id));
        } else {
            json_return(400, "Something Went Wrong", NULL);
        }
    }

    public function addTimeAdmin($date, $time, $distance, $user_id) {
        $date = pg_escape_string($this->conn, $date);
        $time = pg_escape_string($this->conn, $time);
        $distance = pg_escape_string($this->conn, $distance);
        $user_id = pg_escape_string($this->conn, $user_id);
        $sql = "INSERT INTO times (user_id, date, time, distance) VALUES ('$user_id', '$date', '$time', '$distance') RETURNING _id;";
        $query = pg_query($this->conn, $sql);
        if ($query) {
            $insert_row = pg_fetch_row($query);
            $insert_id = $insert_row[0];
            json_return(200, "Record Add Succeeded", array("user_id" => $user_id, "_id" => $insert_id));
        } else {
            json_return(400, "Something Went Wrong", NULL);
        }
    }

    public function editTime($date, $time, $distance, $time_id) {
        $time_id = pg_escape_string($this->conn, $time_id);
        $date = pg_escape_string($this->conn, $date);
        $time = pg_escape_string($this->conn, $time);
        $distance = pg_escape_string($this->conn, $distance);
        $user_id = pg_escape_string($this->conn, $_SESSION['user_id']);

        $sql = "UPDATE times SET time = '$time', date = '$date', distance = '$distance' WHERE _id = '$time_id'";
        $query = pg_query($this->conn, $sql);
        if ($query) {
            json_return(200, "Record Update Succeeded", NULL);
        } else {
            json_return(400, "Something Went Wrong", NULL);
        }
    }

    public function getUsers() {
        $user_id = $_SESSION['user_id'];

        $results = array();
        $sql = "SELECT * FROM users WHERE _id != '$user_id' AND level = '2' ORDER BY _id DESC";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        while ($row = pg_fetch_array($query)) {
            $results[] = array(
                'id' => $row['_id'],
                'email' => $row['email'],
                'level' => $row['level']
            );
        }
        json_return(200, "Users Read Succeeded", $results);
    }

    public function addUser($email, $pass, $level) {
        $email = pg_escape_string($this->conn, $email);
        $pass = pg_escape_string($this->conn, $pass);
        $level = pg_escape_string($this->conn, $level);

        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        if (pg_num_rows($query) <= 0) {
            $hash = getHashed($pass);
            $api_key = getAPIKey();
            $sql = "INSERT INTO users (email, pass, level, api_key) VALUES ('$email', '$hash', '$level', '$api_key') RETURNING _id;";
            $query = pg_query($this->conn, $sql);
            if ($query) {
                $insert_row = pg_fetch_row($query);
                $insert_id = $insert_row[0];
                json_return(200, "User Add Succeeded", array("_id" => $insert_id));
            } else {
                json_return(400, "Something Went Wrong", NULL);
            }
        } else {
            json_return(200, "User Already Exists", NULL);
        }
    }

    public function deleteUser($user_id) {
        $user_id = pg_escape_string($this->conn, $user_id);
        $sql = "DELETE FROM users WHERE _id = '$user_id'";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        if ($query) {
            json_return(200, "User Delete Succeeded", NULL);
        } else {
            json_return(400, "Connot Find This User", NULL);
        }
    }

    public function editUser($email, $pass, $user_id, $level) {
        $email = pg_escape_string($this->conn, $email);
        $pass = pg_escape_string($this->conn, $pass);
        $user_id = pg_escape_string($this->conn, $user_id);
        $level = pg_escape_string($this->conn, $level);

        $sql = "SELECT * FROM users WHERE email = '$email' AND _id != '$user_id' LIMIT 1";
        $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
        if (pg_num_rows($query) <= 0) {

            if (strlen($pass) > 0 && strlen($level) > 0) {
                $hash = getHashed($pass);
                $sql = "UPDATE users SET email = '$email', pass = '$hash', level = '$level' WHERE _id = '$user_id'";
            } elseif (strlen($pass) > 0) {
                $hash = getHashed($pass);
                $sql = "UPDATE users SET email = '$email', pass = '$hash' WHERE _id = '$user_id'";
            } elseif (strlen($level) > 0) {
                $sql = "UPDATE users SET email = '$email', level = '$level' WHERE _id = '$user_id'";
            } else {
                $sql = "UPDATE users SET email = '$email' WHERE _id = '$user_id'";
            }
            $query = pg_query($this->conn, $sql);
            if ($query) {
                json_return(200, "User Update Succeeded", NULL);
            } else {
                json_return(400, "Something Went Wrong", NULL);
            }
        } else {
            json_return(400, "User Already Exists", NULL);
        }
    }

    public function getUsersForAdmins() {
        if ($_SESSION['level'] == 0) {
            $user_id = $_SESSION['user_id'];

            $results = array();
            $sql = "SELECT * FROM users WHERE _id != '$user_id' AND level != '0' ORDER BY _id DESC";
            $query = pg_query($this->conn, $sql) or die(pg_last_error($this->conn));
            while ($row = pg_fetch_array($query)) {
                $results[] = array(
                    'id' => $row['_id'],
                    'email' => $row['email'],
                    'level' => $row['level']
                );
            }
            json_return(200, "Users Read Succeeded", $results);
        } else {
            json_return(401, "Unauthorized Request", $results);
        }
    }

}
