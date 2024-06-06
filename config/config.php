<?php

session_start();

define("BASE_DIR", dirname(__FILE__, 2));
define("BASE_HOST", "/ForumFusion");
define("DATA_DIR", BASE_DIR . "/data");

$envFilePath = BASE_DIR . "/config/.env";
$envFileRows = file($envFilePath);
foreach ($envFileRows as $row) {
    $rowArr = explode("=", $row);
    define($rowArr[0], trim($rowArr[1]));
}