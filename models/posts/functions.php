<?php

function getPosts($forumId) {
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