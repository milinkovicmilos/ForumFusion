<?php

function getPosts($forumId) : string {
    $query = "
        SELECT id, title, thumbnail, text
        FROM posts
        WHERE forum_id = :id
    ";
    $results = queryPrepared($query, ["id" => $forumId]);
    $html = "";
    foreach ($results as $result) {
        $img = "";
        if (isset($result->thumbnail)) {
            $img = "<img src='$result->thumbnail'>";
        }
        $html .= "
            <div class='post'>
                <a class='reset-link' href='index.php?page=post&postId=$result->id'>
                    <div class='flex-container cnt-between'>
                        $img
                        <div class='post-text-wrapper'>
                            <h3>$result->title</h3>
                            <p class='clamp-text'>$result->text</p>
                        </div>
                    </div>
                </a>
            </div>
        ";
    }
    return $html;
}

function getForumId($postId) : ?int {
    $query = "
        SELECT forum_id
        FROM posts
        WHERE id = :id
    ";
    $forumId = queryPrepared($query, ["id" => $postId]);
    if (!empty($forumId)) {
        return $forumId[0]->forum_id;
    }
    return null;
}

function showPost($postId) : string {
    $query = "
        SELECT image, title, text, username
        FROM posts p INNER JOIN users u ON p.user_id = u.id
        WHERE p.id = :id
    ";
    $results = queryPrepared($query, ["id" => $postId]);
    if (empty($results)) {
        return "";
    }
    $result = $results[0];
    $text = processPostText($result->text);
    return "
        <img src='$result->image'>
        <h3>$result->title</h3>
        <p>$text</p>
        <p>Post by : $result->username</p>
    ";
}

function processPostText($text) : string {
    return str_replace("\n", "<br>", $text);
}