<?php

define("FORUM_NAME_REGEX", "/^[A-Z][a-z]*( [A-Z][a-z]*)*$/");
define("FORUM_NAME_MAX", 50);
define("FORUM_DESCRIPTION_REGEX", "/^.+$/");
define("FORUM_DESCRIPTION_MAX", 250);

function forumInfo($forumId) {
    $query = "
        SELECT name, description
        FROM forums
        WHERE id = :id
    ";
    $forum = queryPrepared($query, ["id" => $forumId]);
    if (empty($forum)) {
        return "<h2>Forum Not Found</h2>";
    }
    $forum = $forum[0];
    $html = "
        <h2>$forum->name</h2>
        <h3>$forum->description</h3>
    ";
    return $html;
}

function getPopularForums() {
    $followsQuery = "
        SELECT forum_id, Count(*) as count
        FROM forum_follows
        GROUP BY forum_id
        ORDER BY count DESC
        LIMIT 4
    ";
    $results = queryPrepared($followsQuery, []);
    $forumIds = [];
    foreach ($results as $result) {
        $forumIds[] = $result->forum_id;
    }
    $forumIds = implode(", ", $forumIds);
    $forumsQuery = "
        SELECT id, name, description
        FROM forums
        WHERE id IN ($forumIds)
    ";
    return queryPrepared($forumsQuery, []);
}

function popularForums() {
    $forums = getPopularForums();
    $html = "";
    foreach ($forums as $forum) {
        $html .= "
        <div class='index-item'>
        <a class='reset-link' href='index.php?page=forum&forumId=$forum->id'>
                    <h3>$forum->name</h3>
                    <p>$forum->description</p>
                    </a>
                </div>
        ";
    }
    return $html;
}

function forumCategories() {
    $query = "
        SELECT name
        FROM categories
        LIMIT 4
    ";
    $categories = queryPrepared($query, []);
    $html = "";
    foreach ($categories as $category) {
        $html .= "
            <div class='index-item'>
                <h3>$category->name</h3>
            </div>
        ";
    }
    return $html;
}

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