<?php

define("BASE_DIR", dirname(__FILE__, 2));
define("BASE_HOST", "/ForumFusion/");

$envFilePath = BASE_DIR . "/config/.env";
$envFileRows = file($envFilePath);
foreach ($envFileRows as $row) {
    $rowArr = explode("=", $row);
    define($rowArr[0], trim($rowArr[1]));
}