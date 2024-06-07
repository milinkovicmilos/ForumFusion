<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost() || !isLoggedIn()) {
    redirect("index");
}

$name = post("forumname");
$description = post("forumdescription");
$category = post("forumcategory");

if (!validateName($name) || 
    !validateDescription($description) || 
    !validateCategory($category)) {
        redirect("index");
}

if (addForum($name, $description, $category)) {
    setFlash("FORUM", "You have sucessfully created new forum!");
} else {
    setFlash("FORUM", "There was an error while making forum.");
}

redirect("index");