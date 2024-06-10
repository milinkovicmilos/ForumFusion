<?php

include_once("config/connection.php");
include_once("models/common.php");

include_once("views/fixed/head.php");
include_once("views/fixed/header.php");

$pageArr = ["forum", "post", "category"];
$page = get("page");
if (!isset($page)) {
    $page = DEFAULT_PAGE;
}

$viewsPath = "views/";
if (!file_exists($viewsPath . $page . ".php")) {
    include_once($viewsPath . "404.php");
} else {
    include_once($viewsPath . $page . ".php");
    if (in_array($page, $pageArr)) {
        logPageAccess($page, $_GET[$page . "Id"]);
    } else {
        logPageAccess($page);
    }
}

include_once("views/fixed/footer.php");
include_once("views/fixed/end.php");