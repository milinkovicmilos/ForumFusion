<?php

define("COMMENT_TEXT_REGEX", "/.+/");
define("COMMENT_TEXT_MAX", 1000);

function showPostComments($postId) : string {
    $query = "
        SELECT text, username
        FROM comments c INNER JOIN users u ON c.user_id = u.id
        WHERE post_id = :id
        ORDER BY c.last_mod DESC
    ";
    $results = queryPrepared($query, ["id" => $postId]);
    if (empty($results)) {
        return "<h3>There are no comments on this post yet!</h3>";
    }
    $html = "";
    foreach ($results as $result) { 
        $text = processText($result->text);
        $html .= "
            <div class='comment'>
                <span class='comment-user'>$result->username</span>
                <p>$text</p>
            </div>
        ";
    }
    return $html;
}

function validateText($text) : bool {
    return preg_match(COMMENT_TEXT_REGEX, $text) && strlen($text) <= COMMENT_TEXT_MAX;
}

function addComment($userId, $postId, $text) : bool {
    $query = "
        INSERT INTO comments (user_id, post_id, text)
        VALUES (:u, :p, :t)
    ";
    return executePrepared($query, [
        "u" => $userId,
        "p" => $postId,
        "t" => $text
    ]);
}