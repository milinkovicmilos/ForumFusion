<?php

include_once("config/connection.php");
include_once("models/common.php");

include_once("views/fixed/head.php");
include_once("views/fixed/header.php");

$page = get("page");
$viewsPath = "views/";
if (!$page) {
    include_once($viewsPath . "index.php");
} else {
    if (!file_exists($viewsPath . $page . ".php")) {
        include_once($viewsPath . "404.php");
    } else {
        include_once($viewsPath . $page . ".php");
    }
}

include_once("views/fixed/footer.php");
include_once("views/fixed/end.php");