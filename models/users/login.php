<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost() || isLoggedIn()) {
    redirect("index");
}

$username = post("username");
$password = post("password");

if (!validateUsername($username) || !validatePassword($password)) {
    redirect("login");
}

if (logIn($username, $password)) {
    redirect("index");
}
setFlash("LOGIN", "Invalid login attempt!");
redirect("login");