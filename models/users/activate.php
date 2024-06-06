<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

$code = get("code");

if (isPost() || !$code) {
    redirect("index");
}

activateCode($code);
redirect("index");