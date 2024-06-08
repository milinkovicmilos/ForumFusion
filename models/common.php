<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function get($key) {
    if (isset($_GET[$key]))
        return $_GET[$key];
    return null;
}

function post($key) {
    if (isset($_POST[$key]))
        return $_POST[$key];
    return null;
}

function isPost() {
    return $_SERVER["REQUEST_METHOD"] == "POST";
}

function redirect($pageName) {
    $url = BASE_HOST . "/index.php?page=" . $pageName;
    header("Location: $url");
    die;
}

function getJSON() { 
    $data = json_decode(file_get_contents('php://input'));
    return $data;
}

function respondJSON($data) : void {
    header("Content-type: application/json");
    echo json_encode($data);
}

function responseCodeEnd($responseCode) : void {
    http_response_code($responseCode);
    exit();
}

function isLoggedIn() : bool {
    if (isset($_SESSION["USER"])) return true;
    return false;
}

function getLoggedInUser() : ?object {
    if (isLoggedIn()) {
        return $_SESSION["USER"];
    }
    return null;
}

function setFlash($key, $value) : void { 
    $_SESSION["FLASH"][$key] = $value;
}

function getFlash($key) : string {
    $value = $_SESSION["FLASH"][$key];
    unset($_SESSION["FLASH"][$key]);
    return $value;
}

function checkFlash($key) : bool {
    return isset($_SESSION["FLASH"][$key]);
}

function query($query) {
    global $dbc;
    $results = $dbc->query($query);
    return $results->fetchAll();
}

function queryPrepared($query, $params) {
    global $dbc;
    $prepared = $dbc->prepare($query);
    $prepared->execute($params);
    return $prepared->fetchAll();
}

function queryAll($tableName) {
    global $dbc;
    $query = "SELECT * FROM $tableName";
    return queryPrepared($query, []);
}

function executePrepared($query, $params) : bool { 
    global $dbc;
    $prepared = $dbc->prepare($query);
    $result = $prepared->execute($params);
    return $result;
}

function prepareMailer() : PHPMailer {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = SMTPUSERNAME;
    $mail->Password = SMTPPASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    return $mail;
}

function processText($text) : string {
    return str_replace("\n", "<br>", $text);
}