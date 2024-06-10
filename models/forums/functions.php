<?php

define("FORUM_NAME_REGEX", "/^[a-z]*( [a-z]*)*$/");
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

function getForumsInCategory($categoryId) : ?array {
    $query = "
        SELECT id, name, description
        FROM forums
        WHERE category_id = :cid
    ";
    return queryPrepared($query, ["cid" => $categoryId]);
}

function showForumsInCategory($categoryId) : string {
    $forums = getForumsInCategory($categoryId);
    if (empty($forums)) {
        return "<h3>There are no posts in this forum yet !</h3>";
    }
    $html = "";
    foreach ($forums as $forum) {
        $html .= "
            <a class='reset-link' href='index.php?page=forum&forumId=$forum->id'>
                <div>
                    <h3>$forum->name</h3>
                    <p>$forum->description</p>
                </div>
            </a>
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

function forumExists($forumId) : bool {
    $query = "
        SELECT id
        FROM forums
        WHERE id = :fid
    ";
    $results = queryPrepared($query, ["fid" => $forumId]);
    return !empty($results);
}

function forumNameExists($forumName) : bool {
    $query = "
        SELECT id
        FROM forums
        WHERE name = :fn
    ";
    $results = queryPrepared($query, ["fn" => $forumName]);
    return !empty($results);
}

function addForum($name, $description, $category) : bool { 
    $name = strtolower($name);
    if (forumNameExists($name)) {
        setFlash("FORUM", "Forum with provided name already exists!");
        redirect("createforum");
    }
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

function forumTags($forumId) {
    $query = "
        SELECT id, name
        FROM tags t 
        WHERE category_id is NULL OR category_id = (SELECT category_id FROM forums WHERE id = :fid);
    ";
    $results = queryPrepared($query, ["fid" => $forumId]);
    $html = "<summary>Forum tags</summary><div class='filters-container'>";
    foreach ($results as $result) {
        $html .= "
            <input type='checkbox' id='f-$result->name' name='tag[]' value='filter-$result->id'>
            <label for='f-$result->name'>$result->name</label>
            <br>
        ";
    }
    $html .= "</div>";
    return $html;
}