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
            $sql = "INSERT INTO `users` (`email`, `pass`, `level`) VALUES ('$email', '$hash', '$level')";
            $query = mysqli_query($this->conn, $sql);
            echo mysqli_error($this->conn);
            if ($query) {
                $_SESSION['user_id'] = mysqli_insert_id($this->conn);
                $_SESSION['level'] = $level;
                $_SESSION['email'] = $email;
                json_return(200, "Signup Succeeded", array("session_id" => session_id()));
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
                json_return(200, "Login Succeeded", array("session_id" => session_id(), "level" => $row['level']));
            } else {
                json_return(200, "Invalid Password", NULL);
            }
        } else {
            json_return(400, "Invalid Sign in Data", NULL);
        }
    }

}
