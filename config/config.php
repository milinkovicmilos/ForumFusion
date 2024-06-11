<?php

session_start();

define("BASE_DIR", dirname(__FILE__, 2));
define("DOMAIN", "localhost/ForumFusion");
define("BASE_HOST", "/ForumFusion");
define("DATA_DIR", BASE_DIR . "/data");
define("PAGE_ACCESS_LOG_PATH", BASE_DIR . "/data/admin/pageAccessLog.txt");
define("LOG_SEPARATOR", ";");
define("PAGE_ID_SEPARATOR", "-");
define("DEFAULT_PAGE", "index");

$envFilePath = BASE_DIR . "/config/.env";
$envFileRows = file($envFilePath);
foreach ($envFileRows as $row) {
    $rowArr = explode("=", $row);
    define($rowArr[0], trim($rowArr[1]));
}

require_once(BASE_DIR . "/vendor/autoload.php");