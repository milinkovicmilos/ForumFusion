<?php

define("FORUM_NAME_REGEX", "/^[A-Z][a-z]*( [A-Z][a-z]*)*$/");
define("FORUM_NAME_MAX", 50);
define("FORUM_DESCRIPTION_REGEX", "/^.+$/");
define("FORUM_DESCRIPTION_MAX", 250);

function categoryOptions() {
    $html = "";
    $results = queryAll("categories");
    foreach ($results as $result) {
        $html .= "<option value='$result->id'>$result->name</option>";
    }
    return $html;
}

function validateName($name) : bool {
    return preg_match(FORUM_NAME_REGEX, $name) 
        && strlen($name) <= FORUM_NAME_MAX;
}

function validateDescription($text) : bool {
    return preg_match(FORUM_DESCRIPTION_REGEX, $text) 
        && strlen($text) <= FORUM_DESCRIPTION_MAX;
}

function validateCategory($categoryId) : bool {
    $results = queryAll("categories");
    foreach ($results as $result) {
        if ($result->id == $categoryId) {
            return true;
        }
    }
    return false;
}

function addForum($name, $description, $category) : bool { 
    $userId = getLoggedInUser()->id;
    $query = "
        INSERT INTO forums (name, description, category_id, user_id)
        VALUES (:n, :d, :c, :u)
    ";
    $result = executePrepared($query, [
        "n" => $name,
        "d" => $description,
        "c" => $category,
        "u" => $userId
    ]);
    return $result;
}