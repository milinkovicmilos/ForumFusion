<?php

require_once("../../config/config.php");
require_once("../common.php");

if (isLoggedIn()) {
    unset($_SESSION["USER"]);
}
redirect("index");