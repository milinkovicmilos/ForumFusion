<?php

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

function isLoggedIn() : bool {
    if (isset($_SESSION["USER"])) return true;
    return false;
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