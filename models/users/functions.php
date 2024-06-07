<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

define("NAME_REGEX", "/^[A-Z][a-z]{2,}( [A-Z][a-z]{2,})*$/");
define("MAX_NAME", 20);
define("USERNAME_REGEX", "/^[A-Za-z0-9][A-Za-z0-9]{2,20}$/");
define("MAX_USERNAME", 20);
define("PASSWORD_REGEX", "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$/");

function validateName($name) : bool {
    return preg_match(NAME_REGEX, $name) && strlen($name) <= MAX_NAME;
}

function validateUsername($username) : bool {
    return preg_match(USERNAME_REGEX, $username) && strlen($username) <= MAX_USERNAME;
}

function validateEmail($email) : bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) : bool {
    return preg_match(PASSWORD_REGEX, $password);
}

function getUser($username) : ?object {
    $query = "
        SELECT *
        FROM users
        WHERE username = :u
    ";
    $result = queryPrepared($query, ["u" => $username]);
    if ($result) {
        return $result[0];
    }
    else {
        return null;
    }
}

function usernameEmailAvailable($username, $email) : array {
    $queryUsername = "
        SELECT id
        FROM users
        WHERE username = :u
    ";
    $usernameAvailable = queryPrepared($queryUsername, ["u" => $username]);
    $queryEmail = "
        SELECT id
        FROM users
        WHERE email = :e
    ";
    $emailAvailable = queryPrepared($queryEmail, ["e" => $email]);
    return [empty($usernameAvailable), empty($emailAvailable)];
}

function logIn($username, $password) : bool {
    $user = getUser($username);
    if (!$user) {
        return false;
    }
    if (password_verify($password, $user->password)) {
        unset($user->password);
        $_SESSION["USER"] = $user;
        return true;
    }
    return false;
}

function register($firstname, $lastname, $username, $email, $password) : bool {
    global $dbc;
    $userRoleId = 2;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $activationCode = generateActivationCode($username);
    $usersQuery = "
        INSERT INTO users (first_name, last_name, username, email, password, role_id)
        VALUES (:fn, :ln, :u, :e, :pw, :rid)
    ";
    $activationQuery = "
        INSERT INTO user_activations (user_id, code)
        VALUES (:u, :c)
    ";

    $dbc->beginTransaction();
    try {
        executePrepared($usersQuery, [
            "fn" => $firstname,
            "ln" => $lastname,
            "u" => $username,
            "e" => $email,
            "pw" => $password,
            "rid" => $userRoleId
        ]);
        $userId = $dbc->lastInsertId();
        executePrepared($activationQuery, [
            "u" => $userId,
            "c" => $activationCode
        ]);
    }
    catch (PDOException $ex) {
        $dbc->rollback();
        return false;
    }
    $dbc->commit();
    sendActivationCode($activationCode, $email);
    return true;
}

function generateActivationCode($username) {
    return md5(time() . $username);
}

function sendActivationCode($code, $recipientEmail) {
    $link = DOMAIN . "/models/users/activate.php?code=$code";
    try {
        $mail = prepareMailer();
    
        $mail->setFrom(EMAILADDRESS, 'ForumFusion Activation');
        $mail->addAddress($recipientEmail);
    
        $mail->isHTML(true);
        $mail->Subject = "ForumFusion User Account Activation";
        $mail->Body = "
            You have successfully registred your account on ForumFusion !<br>
            Before you can log in, you have to <a href='$link'>activate</a> Your Account.<hr>
            If you can't open the link, try copying it to your browser : $link
        ";
        $mail->AltBody = "
            You have successfully registred your account on ForumFusion !<br>
            Before you can log in, you have to activate Your Account.<hr>
            If you can't open the link, try copying it to your browser : $link
        ";

        $mail->send();
    }
    catch (PDOException $ex) {}
}

function checkActivationCode($code) : bool {
    $query = "
        SELECT activation_date
        FROM user_activations
        WHERE code = :c
    ";
    $result = queryPrepared($query, ["c" => $code]);
    if (!empty($result) && $result[0]->activation_date == null) {
        return true;
    }
    return false;
}

function activateCode($code) : void {
    if (!checkActivationCode($code)) {
        // Log for activation code error!
        return;
    }
    $date = date("Y-m-d H:i:s");
    $updateQuery = "
        UPDATE user_activations 
        SET activation_date = :d 
        WHERE code = :c
    ";
    executePrepared($updateQuery, ["d" => $date, "c" => $code]);
    $selectQuery = "
        SELECT user_id
        FROM user_activations
        WHERE code = :c
    ";
    $userId = queryPrepared($selectQuery, ["c" => $code]);
    activateuser($userId);
}

function activateUser($userId) : void {
    $query = "
        UPDATE users
        SET active = 1
        WHERE id = :id
    ";
    executePrepared($query, ["id" => $userId]);
}