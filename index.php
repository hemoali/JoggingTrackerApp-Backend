<?php

$headers = array();

function getAuth() {
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headers = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers . " : " . $i[$headers] = $value . "<br>";
            if($headers == Authorization)
                return $value;
        }
    }
}
echo getAuth();

?>