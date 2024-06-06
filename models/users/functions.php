<?php

define("NAME_REGEX", "/^[A-Z][a-z]{2,}([A-Z][a-z]{2,})*$/");
define("USERNAME_REGEX", "/^[A-Za-z0-9][A-Za-z0-9]{2,20}$/");
define("PASSWORD_REGEX", "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,}$/");

function validateName($name) : bool {
    return preg_match(NAME_REGEX, $name);
}

function validateUsername($username) : bool {
    return preg_match(USERNAME_REGEX, $username);
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
        unset($user->id);
        $_SESSION["USER"] = $user;
        return true;
    }
    return false;
}

function register($firstname, $lastname, $username, $email, $password) : bool {
    $userRoleId = 2;
    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "
        INSERT INTO users (first_name, last_name, username, email, password, role_id)
        VALUES (:fn, :ln, :u, :e, :pw, :rid)
    ";
    $result = queryPrepared($query, [
        "fn" => $firstname,
        "ln" => $lastname,
        "u" => $username,
        "e" => $email,
        "pw" => $password,
        "rid" => $userRoleId
    ]);
    return empty($result);
}