<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

$username = post("username");
$password = post("password");

if (!validateUsername($username) || !validatePassword($password)) {
    redirect("login");
}

$log = logIn($username, $password);
redirect("");