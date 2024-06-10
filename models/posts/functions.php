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
            DISTINCT p.id, username, title, image, text,
            (
                SELECT Count(*)
                FROM post_likes
                WHERE post_id = p.id
            ) as like_count,
            (
                SELECT Count(*)
                FROM post_likes
                WHERE post_id = p.id AND user_id = :uid
            ) as liked,
            (
                SELECT GROUP_CONCAT(CONCAT('#', t.name) SEPARATOR ', ')
                FROM tags t
                WHERE t.id IN (SELECT tag_id FROM post_tags WHERE post_id = p.id)
            ) as tags
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
            ) as liked,
            (
                SELECT GROUP_CONCAT(CONCAT('#', t.name) SEPARATOR ', ')
                FROM tags t
                WHERE t.id IN (SELECT tag_id FROM post_tags WHERE post_id = p.id)
            ) as tags
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
    $image = isset($result->image) ? "data/img/$result->image" : "";
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
    $tags = isset($result->tags) ? "<span class='end'>$result->tags</span>" : "";
    return "
        <img src='$image'>
        <h3>$result->title</h3>
        <p>$text</p>
        <span class='post-info'>
            <p class='post-author'>Post by : $result->username</p>
            $likeIcon
            <span class='comment-like-count'>$result->like_count</span>
            $tags
        </span>
    ";
}

function handlePostImage($filePath) : string {
    list($widthOriginal, $heightOriginal) = getimagesize($filePath);

    $ratioOrignal = $widthOriginal / $heightOriginal;

    $heightScaled = 300;
    $widthScaled = $heightScaled * $ratioOrignal;

    if (mime_content_type($filePath) == "image/png") {
        $original = imagecreatefrompng($filePath);
    } else {
        $original = imagecreatefromjpeg($filePath);
    }

    $scaled = imagecreatetruecolor($widthScaled, $heightScaled);
    $alpha = imagecolorallocatealpha($scaled, 0, 0, 0, 127); 
    imagecolortransparent($scaled, $alpha); 
    imagefill($scaled, 0, 0, $alpha);
    imagesavealpha($scaled, true);
    imagecopyresampled($scaled, $original, 0, 0, 0, 0, $widthScaled, $heightScaled, $widthOriginal, $heightOriginal);

    $thumbnailSize = 150;
    $start_x = 0;
    $start_y = 0;

    if ($widthScaled > $heightScaled) {
        $ratioThumbnail = $heightScaled / $widthScaled;
        $heightThumbnail = $thumbnailSize * $ratioThumbnail;

        $widthThumbnail = $thumbnailSize;
        $start_y = ($thumbnailSize - $heightThumbnail) / 2;
    } else {
        $ratioThumbnail = $widthScaled / $heightScaled;
        $widthThumbnail = $thumbnailSize * $ratioThumbnail;

        $heightThumbnail = $thumbnailSize;
        $start_x = ($thumbnailSize - $widthThumbnail) / 2;
    }


    $thumbnail = imagecreatetruecolor($thumbnailSize, $thumbnailSize);
    $alpha = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
    imagecolortransparent($thumbnail, $alpha);
    imagefill($thumbnail, 0, 0, $alpha);
    imagesavealpha($thumbnail, true);
    imagecopyresampled($thumbnail, $scaled, $start_x, $start_y, 0, 0, $widthThumbnail, $heightThumbnail, $widthScaled, $heightScaled);

    $imgName = time() . md5($filePath) . ".png";
    $imgPath = DATA_DIR . "/img/" . $imgName;
    $thumbnailPath = DATA_DIR . "/img/thumbnail/" . $imgName;
    imagepng($scaled, $imgPath);
    imagepng($thumbnail, $thumbnailPath);

    imagedestroy($original);
    imagedestroy($scaled);
    imagedestroy($thumbnail);

    return $imgName;
}

function addPost($forumId, $title, $text, $image, $tags) : int {
    $userId = getLoggedInUser()->id;
    $image = isset($image) && !empty($image) ? $image : null;
    $query = "
        INSERT INTO posts (title, image, text, forum_id, user_id)
        VALUES (:t, :i, :txt, :fid, :uid)
    ";
    executePrepared($query, [
        "fid" => $forumId,
        "t" => $title,
        "txt" => $text,
        "i" => $image,
        "uid" => $userId
    ]);
    global $dbc;
    $postId = $dbc->lastInsertId();
    if (isset($tags) && !empty($tags)) {
        addPostTags($postId, $tags);
    }
    return $postId;
}

function addPostTags($postId, $tags) : void {
    $query = "";
    foreach ($tags as $tag) {
        $tag = (int) explode("-", $tag)[1];
        $query .= "
            INSERT INTO post_tags (post_id, tag_id)
            VALUES ($postId, $tag);
        ";

    }
    executePrepared($query, []);
}

define("POST_TITLE_REGEX", "/.+/");
define("POST_TITLE_MAX", 250);
function validatePostTitle($title) : bool {
    return preg_match(POST_TITLE_REGEX, $title) && strlen($title) <= POST_TITLE_MAX;
}

define("POST_TEXT_REGEX", "/.+/");
define("POST_TEXT_MAX", 2000);
function validatePostText($text) : bool {
    return preg_match(POST_TEXT_REGEX, $text) && strlen($text) <= POST_TEXT_MAX;
}

function validatePostTags($tags, $forumId) : bool {
    $query = "
        SELECT id
        FROM tags t 
        WHERE category_id is NULL OR category_id = (SELECT category_id FROM forums WHERE id = :fid);
    ";
    $results = queryPrepared($query, ["fid" => $forumId]);
    $tagIds = [];
    foreach ($results as $result) {
        $tagIds[] = $result->id;
    }
    foreach ($tags as $tag) {
        $tag = (int) explode("-", $tag)[1];
        if (!in_array($tag, $tagIds)) {
            return false;
        }
    }
    return true;
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