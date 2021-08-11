<?php

include_once($_SERVER['DOCUMENT_ROOT'].'/models/OrionAuth.php');
include_once($_SERVER['DOCUMENT_ROOT'] . "/controllers/redirect.php");

OrionAuth::logout();

redirectTo('/views/index.php');