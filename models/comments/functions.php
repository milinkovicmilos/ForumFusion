<?php

define("COMMENT_TEXT_REGEX", "/.+/");
define("COMMENT_TEXT_MAX", 1000);

function getPostComments($postId) : ?array {
    @$userId = getLoggedInUser()->id;
    $query = "
        SELECT 
            c.id, text, username, 
            (
                SELECT Count(*)
                FROM comment_likes
                WHERE comment_id = c.id
            ) as like_count,
            (
                SELECT Count(*) as liked
                FROM comment_likes cl
                WHERE cl.comment_id = c.id AND cl.user_id = :uid
            ) as liked
        FROM comments c INNER JOIN users u ON c.user_id = u.id
        WHERE post_id = :pid
        ORDER BY like_count DESC, c.last_mod DESC
    ";
    return queryPrepared($query, [
        "pid" => $postId,
        "uid" => $userId
    ]);
}

function showPostComments($postId) : string {
    $results = getPostComments($postId);
    if (empty($results)) {
        return "<h3>There are no comments on this post yet!</h3>";
    }
    $html = "";
    foreach ($results as $result) { 
        $text = processText($result->text);
        $liked = (bool) $result->liked ? "liked" : "";
        $thumbsUpStyle = (bool) $result->liked ? "fa-solid" : "fa-regular";
        $likeIcon = isLoggedIn() ? "
            <i id='cl-$result->id' class='comment-like $liked $thumbsUpStyle fa-thumbs-up'></i>
        " : "
            <a class='reset-link-icon' href='index.php?page=login'>
                <i id='cl-$result->id' class='comment-like $liked $thumbsUpStyle fa-thumbs-up'></i>
            </a>
        ";
        $html .= "
            <div class='comment'>
                <span class='comment-user'>$result->username</span>
                <div class='wrapper'>
                    <p>$text</p>
                    <div>
                        $likeIcon
                        <span class='comment-like-count'>$result->like_count</span>
                    </div>
                </div>
            </div>
        ";
    }
    return $html;
}

function validateText($text) : bool {
    return preg_match(COMMENT_TEXT_REGEX, $text) && strlen($text) <= COMMENT_TEXT_MAX;
}

function validateCommentId($commentId) : bool {
    $query = "
        SELECT id
        FROM comments
        WHERE id = :id
    ";
    $results = queryPrepared($query, ["id" => $commentId]);
    if (empty($results)) {
        return false;
    }
    return true;
}

function isLiked($commentId) : bool { 
    $userId = getLoggedInUser()->id;
    $query = "
        SELECT comment_id
        FROM comment_likes
        WHERE comment_id = :cid AND user_id = :uid
    ";
    $results = queryPrepared($query, [
        "cid" => $commentId,
        "uid" => $userId
    ]);
    return !empty($results);
}

function addComment($userId, $postId, $text) : ?int {
    $query = "
        INSERT INTO comments (user_id, post_id, text)
        VALUES (:u, :p, :t)
    ";
    $result = executePrepared($query, [
        "u" => $userId,
        "p" => $postId,
        "t" => $text
    ]);
    if ($result) {
        global $dbc;
        return $dbc->lastInsertId();
    }
    return NULL;
}

function likeComment($commentId) : bool { 
    $userId = getLoggedInUser()->id;
    $query = "
        INSERT INTO comment_likes (comment_id, user_id)
        VALUES (:cid, :uid)
    ";
    return executePrepared($query, [
        "cid" => $commentId,
        "uid" => $userId
    ]);
}

function unlikeComment($commentId) : bool {
    $userId = getLoggedInUser()->id;
    $query = "
        DELETE FROM comment_likes
        WHERE comment_id = :cid AND user_id = :uid
    ";
    return executePrepared($query, [
        "cid" => $commentId,
        "uid" => $userId
    ]);
}