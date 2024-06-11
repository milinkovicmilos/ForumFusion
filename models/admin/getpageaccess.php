<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost() || !isAdmin()) {
    responseCodeEnd(401);
}

respondJSON(getPageAccessLog());