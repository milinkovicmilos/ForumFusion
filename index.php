<?php

include_once("config/connection.php");
include_once("models/common.php");

include_once("views/fixed/head.php");
include_once("views/fixed/header.php");

$page = get("page");
if (!isset($page)) {
    $page = DEFAULT_PAGE;
}

$viewsPath = "views/";
$adminViewsPath = "views/admin/";
if (!file_exists($viewsPath . $page . ".php") && 
    !file_exists($adminViewsPath . $page . ".php")) {
    include_once($viewsPath . "404.php");
} else {
    includePage($page, [$viewsPath, $adminViewsPath]);
}

include_once("views/fixed/footer.php");
include_once("views/fixed/end.php");