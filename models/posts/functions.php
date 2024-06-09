<?php

define("SORT_MAP", [
    "1" => "p.last_mod DESC",
    "2" => "p.last_mod ASC",
    "3" => "like_count DESC",
    "4" => "like_count ASC"
]);

define("PERPAGE_MAP", [
    "1" => 5,
    "2" => 10,
    "3" => 15
]);

function getPosts($forumId, $searchParam, $sortParam, $perPageParam, $pageNumber, $filters) : ?array {
    @$userId = getLoggedInUser()->id;
    $search = strtolower($searchParam);
    $sort = SORT_MAP[$sortParam];
    $perPage = PERPAGE_MAP[$perPageParam];
    $offset = ($pageNumber - 1) * $perPage;
    if (!empty($filters)) {
        $filters = implode(", ", $filters);
        $filters = "AND tag_id IN ($filters)";
    } else {
        $filters = "";
    }
    $query = "
        SELECT 
            DISTINCT p.id, username, title, thumbnail, text,
            (
                SELECT Count(*)
                FROM post_likes
                WHERE post_id = p.id
            ) as like_count,
            (
                SELECT Count(*)
                FROM post_likes
                WHERE post_id = p.id AND user_id = :uid
            ) as liked
        FROM 
            posts p INNER JOIN users u ON p.user_id = u.id
            LEFT OUTER JOIN post_tags pt ON p.id = pt.post_id
        WHERE 
            p.forum_id = :fid AND p.active = 1 AND 
            CONCAT(LOWER(title), LOWER(text)) LIKE CONCAT('%', :s, '%')
            $filters
        ORDER BY $sort
        LIMIT $perPage
        OFFSET $offset
    ";
    return queryPrepared($query, [
        "fid" => $forumId,
        "uid" => $userId,
        "s" => $search
    ]);
}

function postCount($forumId, $searchParam, $filters) {
    $search = strtolower($searchParam);
    if (!empty($filters)) {
        $filters = implode(", ", $filters);
        $filters = "AND tag_id IN ($filters)";
    } else {
        $filters = "";
    }
    $query = "
        SELECT Count(*) as count
        FROM posts p LEFT OUTER JOIN post_tags pt ON p.id = pt.post_id
        WHERE
            p.forum_id = :fid AND p.active = 1 AND 
            CONCAT(LOWER(title), LOWER(text)) LIKE CONCAT('%', :s, '%')
            $filters
    ";
    $results = queryPrepared($query, [
        "fid" => $forumId,
        "s" => $search
    ]);
    return $results[0]->count;
}

function showPosts($forumId) : string {
    $results = getPosts($forumId);
    if (empty($results)) {
        return "<h3>There are no posts on this forum yet !</h3>";
    }
    $html = "";
    foreach ($results as $result) {
        $img = empty($result->thumbnail) ? "" : "<img src='$result->thumbnail'>";
        $liked = (bool) $result->liked ? "fa-solid" : "fa-regular";
        $html .= "
            <div class='post'>
                <a class='reset-link' href='index.php?page=post&postId=$result->id'>
                    <div class='flex-container cnt-between'>
                        $img
                        <div class='post-text-wrapper'>
                            <h3>$result->title</h3>
                            <p class='clamp-text'>$result->text</p>
                        </div>
                        <span class='post-info'>
                            <p class='post-author'>Post by : $result->username</p>
                            <i class='$liked fa-thumbs-up'></i>$result->like_count
                        </span>
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

function postExists($postId) : bool {
    $query = "
        SELECT id
        FROM posts
        WHERE id = :pid
    ";
    $results = queryPrepared($query, ["pid" => $postId]);
    return !empty($results);
}

function getPost($postId) : ?object {
    @$userId = getLoggedInUser()->id;
    $query = "
        SELECT image, title, text, username,
            (
                SELECT Count(*)
                FROM post_likes
                WHERE post_id = p.id
            ) as like_count,
            (
                SELECT Count(*)
                FROM post_likes
                WHERE post_id = p.id AND user_id = :uid
            ) as liked
        FROM posts p INNER JOIN users u ON p.user_id = u.id
        WHERE p.id = :pid
    ";
    $results = queryPrepared($query, [
        "pid" => $postId,
        "uid" => $userId
    ]);
    if (empty($results)) {
        return null;
    }
    return $results[0];
}

function showPost($postId) : string {
    $result = getPost($postId);
    if (!$result) {
        return "<h3>Post not found</h3>";
    }
    $text = processText($result->text);
    $iconStyle = (bool) $result->liked ? "fa-solid" : "fa-regular";
    $liked = (bool) $result->liked ? "liked-post" : "";
    $likeIcon = isLoggedIn() ? "
        <i class='$liked $iconStyle fa-thumbs-up'></i>
    " : "
        <a class='reset-link-icon' href='index.php?page=login'>
            <i class='$liked $iconStyle fa-thumbs-up'></i>
        </a>
    ";
    return "
        <img src='$result->image'>
        <h3>$result->title</h3>
        <p>$text</p>
        <span class='post-info'>
            <p class='post-author'>Post by : $result->username</p>
            $likeIcon
            <span class='comment-like-count'>$result->like_count</span>
        </span>
    ";
}

function validatePostId($postId) : bool {
    $query = "
        SELECT id
        FROM posts
        WHERE id = :id
    ";
    $results = queryPrepared($query, ["id" => $postId]);
    return !empty($results);
}

function isPostLiked($postId) : bool {
    @$userId = getLoggedInUser()->id;
    $query = "
        SELECT post_id
        FROM post_likes
        WHERE post_id = :pid AND user_id = :uid
    ";
    $results = queryPrepared($query, [
        "pid" => $postId,
        "uid" => $userId
    ]);
    return !empty($results);
}

function likePost($postId) : bool {
    @$userId = getLoggedInUser()->id;
    $query = "
        INSERT INTO post_likes (post_id, user_id)
        VALUES (:pid, :uid)
    ";
    return executePrepared($query, [
        "pid" => $postId,
        "uid" => $userId
    ]);
}

function unlikePost($postId) : bool {
    @$userId = getLoggedInUser()->id;
    $query = "
        DELETE FROM post_likes
        WHERE post_id = :pid AND user_id = :uid
    ";
    return executePrepared($query, [
        "pid" => $postId,
        "uid" => $userId
    ]);
}