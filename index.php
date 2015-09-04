<?php

echo getHashed(22);
function getHashed($pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    return $hash;
}

