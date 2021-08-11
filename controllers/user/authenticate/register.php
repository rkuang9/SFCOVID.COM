<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionAuth.php');

if (!isset($_POST['username'])) {
    return json_encode(false);
}

if (!isset($_POST['email'])) {
    return json_encode(false);
}

if (!isset($_POST['password'])) {
    return json_encode(false);
}

register();

function register() {
    $user = new OrionAuth();
    $user->username = $_POST['username'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    echo json_encode($user->register());
}