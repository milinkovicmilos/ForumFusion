<?php

require_once("../../config/config.php");
require_once("../common.php");

if (isPost() || !isLoggedIn()) {
    redirect("index");
}

unset($_SERVER["USER"]);
redirect("index.php");