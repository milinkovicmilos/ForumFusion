<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost() || isLoggedIn()) {
    redirect("index");
}

$firstname = post("firstname");
$lastname = post("lastname");
$username = post("username");
$email = post("email");
$password = post("password");
$repassword = post("repassword");

if (!validateName($firstname) || !validateName($lastname) ||
    !validateUsername($username) || !validateEmail($email) ||
    !validatePassword($password) || !validatePassword($repassword)) {
    redirect("index");
}

$availability = usernameEmailAvailable($username, $email);
$valid = true;
if (!$availability[0]) {
    setFlash("USERNAME", "Username has already been taken!");
    $valid = false;
}
if (!$availability[1]) {
    setFlash("EMAIL", "Provided email is already registred!");
    $valid = false;
}

if (!$valid) {
    redirect("login");
}

$result = register($firstname, $lastname, $username, $email, $password);
if ($result) {
    setFlash("REGISTER", 
        "You have successfully registred your account. 
        Activation link has been sent to your email."
    );
} else {
    setFlash("REGISTER", "Error occured... Please try again later.");
}
redirect("login");