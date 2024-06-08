<?php

function showPostComments($postId) : string {
    $query = "
        SELECT text, username
        FROM comments c INNER JOIN users u ON c.user_id = u.id
        WHERE post_id = :id
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