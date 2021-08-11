<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/models/OrionAuth.php');

if (!isset($_POST['login'])) {
    return json_encode(false);
}

if (!isset($_POST['password'])) {
    return json_encode(false);
}

login();

function login() {
    $user = new OrionAuth($_POST['login'], $_POST['password']);
    echo json_encode($user->login());
}